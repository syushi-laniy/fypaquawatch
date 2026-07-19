<?php

namespace App\Http\Controllers;

use App\Models\Tank;
use App\Models\TankAction;
use App\Models\TankDeviceState;
use App\Models\TankReading;
use App\Models\TankThreshold;
use App\Models\Threshold;
use App\Models\User;
use App\Models\Parameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TelegramController extends Controller
{
    private const PH_DOSE_KEYS = [
        'ph_up',
        'ph_down',
    ];

    private const PH_DOSE_COOLDOWN_SECONDS = 5;

    public function handle(Request $request)
    {
        $this->ensureBotCommands();

        if ($request->input('callback_query')) {
            return $this->handleCallbackQuery($request->input('callback_query'));
        }

        $message = $request->input('message');
        if (!$message) {
            return response('ok', 200);
        }

        $chatId = $message['chat']['id'] ?? null;
        $text = trim($message['text'] ?? '');
        if (!$chatId || $text === '') {
            return response('ok', 200);
        }

        if (str_starts_with($text, '/link ')) {
            $code = trim(substr($text, 6));
            $user = User::where('telegram_link_token', $code)->first();
            if (!$user) {
                $this->sendMessage($chatId, 'Invalid or expired code.');
                return response('ok', 200);
            }

            // Detach this chat from any previous user to avoid stale links.
            User::where('telegram_chat_id', (string) $chatId)
                ->where('id', '!=', $user->id)
                ->update(['telegram_chat_id' => null]);

            $user->telegram_chat_id = (string) $chatId;
            $user->telegram_link_token = null;
            $user->save();

            $this->sendTankSelection($chatId, $user, 'Linked successfully. Choose the tank for Telegram controls, status, and alerts:');
            return response('ok', 200);
        }

        $user = User::where('telegram_chat_id', (string) $chatId)->first();
        if (!$user) {
            $this->sendMessage($chatId, 'Please link your account. Use the code from the dashboard with /link <code>.');
            return response('ok', 200);
        }

        if ($text === '/menu') {
            $this->sendMenu($chatId);
            return response('ok', 200);
        }

        if ($text === '/help') {
            $this->sendMessage($chatId, $this->helpText());
            $this->sendMenu($chatId);
            return response('ok', 200);
        }

        if ($text === '/start') {
            $this->sendMenu($chatId, 'Choose an AquaWatch action:');
            return response('ok', 200);
        }

        if (str_starts_with($text, '/select')) {
            if ($text === '/select') {
                $this->sendTankSelection($chatId, $user);
                return response('ok', 200);
            }

            $tank = $this->resolveTankForCommand($user, $text);
            if (!$tank) {
                $this->sendTankSelection($chatId, $user, 'Tank not found. Choose a tank:');
                return response('ok', 200);
            }

            $this->selectTank($user, $tank);
            $this->sendMenu($chatId, "Selected tank: {$tank->name}");
            return response('ok', 200);
        }

        if (str_starts_with($text, '/status')) {
            $tank = $this->resolveTankForCommand($user, $text);
            if (!$tank) {
                $this->sendMessage($chatId, 'Tank not found. Use /tanks to list your tanks.');
                return response('ok', 200);
            }

            $this->sendMessage($chatId, $this->tankStatusText($tank));
            return response('ok', 200);
        }

        if ($text === '/ph') {
            $tank = $this->selectedTank($user);
            $this->sendMessage($chatId, $tank ? $this->parameterStatusText($tank, 'pH') : 'No tanks found for your account.');
            return response('ok', 200);
        }

        if ($text === '/turbidity') {
            $tank = $this->selectedTank($user);
            $this->sendMessage($chatId, $tank ? $this->parameterStatusText($tank, 'Turbidity') : 'No tanks found for your account.');
            return response('ok', 200);
        }

        $quickAction = $this->commandToAction($text);
        if ($quickAction) {
            $this->performDeviceAction($chatId, $user, $quickAction['action'], $quickAction['device_key'], $quickAction['state']);
            return response('ok', 200);
        }

        if (str_starts_with($text, '/action')) {
            $parts = preg_split('/\s+/', $text, 3);
            $target = $parts[1] ?? null;
            $tankId = isset($target) && is_numeric($target) ? (int) $target : null;
            $actionText = $tankId ? ($parts[2] ?? '') : (isset($parts[2]) ? $parts[2] : trim(substr($text, 7)));
            if ($actionText === '') {
                $this->sendMessage($chatId, 'Usage: /action <tank_id|tank_name> <description>. Example: /action 1 feeder on');
                return response('ok', 200);
            }

            $tankQuery = Tank::where('user_id', $user->id);
            $tank = null;
            if ($tankId) {
                $tank = $tankQuery->where('id', $tankId)->first();
            } elseif ($target) {
                $tank = $tankQuery->where('name', $target)->first();
            }

            if (!$tank) {
                $count = $tankQuery->count();
                if ($count > 1) {
                    $this->sendMessage($chatId, 'Please specify a tank. Use /tanks then /action <tank_id> <text>.');
                    return response('ok', 200);
                }
                $tank = $tankQuery->orderBy('name')->first();
            }
            if (!$tank) {
                $this->sendMessage($chatId, 'No tanks found for your account.');
                return response('ok', 200);
            }

            $deviceState = $this->parseDeviceState($actionText);
            if (!$deviceState) {
                TankAction::create([
                    'tank_id' => $tank->id,
                    'user_id' => $user->id,
                    'action' => $actionText,
                ]);

                $this->sendMessage($chatId, $tank->name . ': action recorded - ' . $actionText);
                return response('ok', 200);
            }

            $this->performDeviceAction($chatId, $user, $actionText, $deviceState['device_key'], $deviceState['state'], $tank);
            return response('ok', 200);
        }

        if ($text === '/tanks') {
            $tanks = Tank::where('user_id', $user->id)->orderBy('name')->get(['id', 'name']);
            if ($tanks->isEmpty()) {
                $this->sendMessage($chatId, 'No tanks found for your account.');
                return response('ok', 200);
            }
            $selected = $this->selectedTank($user);
            $lines = ['Your tanks:'];
            foreach ($tanks as $tank) {
                $prefix = $selected && $selected->id === $tank->id ? '* ' : '';
                $lines[] = $prefix . $tank->id . ' - ' . $tank->name;
            }
            $lines[] = '';
            $lines[] = 'Use /select <tank_id> to choose the tank for Telegram status, controls, and alerts.';
            $this->sendMessage($chatId, implode("\n", $lines));
            $this->sendTankSelection($chatId, $user);
            return response('ok', 200);
        }

        $this->sendMessage($chatId, $this->helpText());
        return response('ok', 200);
    }

    private function handleCallbackQuery(array $callbackQuery)
    {
        $callbackId = $callbackQuery['id'] ?? null;
        $chatId = $callbackQuery['message']['chat']['id'] ?? null;
        $data = $callbackQuery['data'] ?? '';

        if ($callbackId) {
            $this->answerCallbackQuery($callbackId);
        }

        if (!$chatId || $data === '') {
            return response('ok', 200);
        }

        $user = User::where('telegram_chat_id', (string) $chatId)->first();
        if (!$user) {
            $this->sendMessage($chatId, 'Please link your account. Use the code from the dashboard with /link <code>.');
            return response('ok', 200);
        }

        if ($data === 'select_tank') {
            $this->sendTankSelection($chatId, $user);
            return response('ok', 200);
        }

        if (str_starts_with($data, 'tank_select_')) {
            $tankId = (int) substr($data, strlen('tank_select_'));
            $tank = Tank::where('user_id', $user->id)->where('id', $tankId)->first();
            if (!$tank) {
                $this->sendTankSelection($chatId, $user, 'Tank not found. Choose a tank:');
                return response('ok', 200);
            }

            $this->selectTank($user, $tank);
            $this->sendMenu($chatId, "Selected tank: {$tank->name}");
            return response('ok', 200);
        }

        if ($data === 'status') {
            $tank = $this->selectedTank($user);
            $this->sendMessage($chatId, $tank ? $this->tankStatusText($tank) : 'No tanks found for your account.');
            return response('ok', 200);
        }

        if ($data === 'help') {
            $this->sendMessage($chatId, $this->helpText());
            return response('ok', 200);
        }

        $quickAction = $this->commandToAction('/' . $data);
        if ($quickAction) {
            $this->performDeviceAction($chatId, $user, $quickAction['action'], $quickAction['device_key'], $quickAction['state']);
        }

        return response('ok', 200);
    }

    private function sendMenu(string $chatId, string $message = 'Choose an AquaWatch action:'): void
    {
        $this->sendMessage($chatId, $message, [
            'inline_keyboard' => [
                [
                    ['text' => 'Choose Tank', 'callback_data' => 'select_tank'],
                ],
                [
                    ['text' => 'Check Status', 'callback_data' => 'status'],
                ],
                [
                    ['text' => 'Dose pH Up', 'callback_data' => 'ph_up_dose'],
                ],
                [
                    ['text' => 'Dose pH Down', 'callback_data' => 'ph_down_dose'],
                ],
                [
                    ['text' => 'Turn ON Water Pump', 'callback_data' => 'water_pump_on'],
                    ['text' => 'Turn OFF Water Pump', 'callback_data' => 'water_pump_off'],
                ],
                [
                    ['text' => 'Feed Now', 'callback_data' => 'feed_now'],
                ],
                [
                    ['text' => 'Help', 'callback_data' => 'help'],
                ],
            ],
        ]);
    }

    private function sendMessage(string $chatId, string $text, ?array $replyMarkup = null): void
    {
        $token = config('services.telegram.token');
        if (!$token) {
            return;
        }

        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
        ];

        if ($replyMarkup) {
            $payload['reply_markup'] = $replyMarkup;
        }

        Http::post("https://api.telegram.org/bot{$token}/sendMessage", $payload);
    }

    private function answerCallbackQuery(string $callbackId): void
    {
        $token = config('services.telegram.token');
        if (!$token) {
            return;
        }

        Http::post("https://api.telegram.org/bot{$token}/answerCallbackQuery", [
            'callback_query_id' => $callbackId,
        ]);
    }

    private function ensureBotCommands(): void
    {
        Cache::remember('telegram_bot_commands_registered_v4', now()->addDay(), function () {
            $token = config('services.telegram.token');
            if (!$token) {
                return true;
            }

            Http::post("https://api.telegram.org/bot{$token}/setMyCommands", [
                'commands' => [
                    ['command' => 'tanks', 'description' => 'List and choose tanks'],
                    ['command' => 'select', 'description' => 'Choose active tank'],
                    ['command' => 'status', 'description' => 'Check latest aquarium reading'],
                    ['command' => 'ph', 'description' => 'Check pH status'],
                    ['command' => 'turbidity', 'description' => 'Check water clarity status'],
                    ['command' => 'ph_up_dose', 'description' => 'Request one pH up dose'],
                    ['command' => 'ph_down_dose', 'description' => 'Request one pH down dose'],
                    ['command' => 'water_pump_on', 'description' => 'Turn ON water pump'],
                    ['command' => 'water_pump_off', 'description' => 'Turn OFF water pump'],
                    ['command' => 'feed_now', 'description' => 'Start fish feeding'],
                    ['command' => 'menu', 'description' => 'Show action buttons'],
                    ['command' => 'help', 'description' => 'Show available commands'],
                ],
            ]);

            Http::post("https://api.telegram.org/bot{$token}/setChatMenuButton", [
                'menu_button' => [
                    'type' => 'commands',
                ],
            ]);

            return true;
        });
    }

    private function helpText(): string
    {
        return implode("\n", [
            'AquaWatch commands:',
            '/menu - Show action buttons',
            '/tanks - List and choose your tanks',
            '/select <tank_id|tank_name> - Choose tank for Telegram',
            '/status - Check latest aquarium reading',
            '/status <tank_id|tank_name> - Check a specific tank',
            '/ph - Check pH status',
            '/turbidity - Check water clarity status',
            '/ph_up_dose - Request one pH up dose',
            '/ph_down_dose - Request one pH down dose',
            '/water_pump_on - Turn ON water pump',
            '/water_pump_off - Turn OFF water pump',
            '/feed_now - Start fish feeding',
            '/help - Show available commands',
        ]);
    }

    private function commandToAction(string $text): ?array
    {
        return match ($text) {
            '/ph_pump_on', '/ph_up_on', '/ph_up_dose' => ['device_key' => 'ph_up', 'state' => true, 'action' => 'pH up dose'],
            '/ph_pump_off', '/ph_up_off' => ['device_key' => 'ph_up', 'state' => false, 'action' => 'pH up dose off'],
            '/ph_down_on', '/ph_down_dose' => ['device_key' => 'ph_down', 'state' => true, 'action' => 'pH down dose'],
            '/ph_down_off' => ['device_key' => 'ph_down', 'state' => false, 'action' => 'pH down dose off'],
            '/water_pump_on' => ['device_key' => 'topup', 'state' => true, 'action' => 'water pump on'],
            '/water_pump_off' => ['device_key' => 'topup', 'state' => false, 'action' => 'water pump off'],
            '/feed_now' => ['device_key' => 'feeder', 'state' => true, 'action' => 'feed now'],
            default => null,
        };
    }

    private function performDeviceAction(
        string $chatId,
        User $user,
        string $actionText,
        string $deviceKey,
        bool $state,
        ?Tank $tank = null
    ): void {
        $tank ??= $this->selectedTank($user);
        if (!$tank) {
            $this->sendMessage($chatId, 'No tanks found for your account.');
            return;
        }

        if (($tank->control_mode ?? 'auto') === 'auto') {
            $this->sendMessage($chatId, 'Auto mode is enabled for this tank. Switch to manual to control devices.');
            return;
        }

        if (in_array($deviceKey, self::PH_DOSE_KEYS, true)) {
            if (!$state) {
                $this->sendMessage($chatId, $tank->name . ': pH dosing is one-time only. There is no continuous pH pump state to turn off.');
                return;
            }

            $this->requestPhDose($chatId, $user, $tank, $deviceKey, 'One-time manual dose requested from Telegram.');
            return;
        }

        TankAction::create([
            'tank_id' => $tank->id,
            'user_id' => $user->id,
            'action' => $actionText,
        ]);

        if ($state === true) {
            $blockReason = $this->getBlockReason($tank, $deviceKey);
            if ($blockReason) {
                $this->sendMessage($chatId, $tank->name . ': ' . $blockReason);
                return;
            }
        }

        $existing = TankDeviceState::where('tank_id', $tank->id)
            ->where('device_key', $deviceKey)
            ->first();
        if ($existing && $existing->state === $state) {
            $this->sendMessage($chatId, $tank->name . ': device already ' . ($state ? 'ON' : 'OFF') . '.');
            return;
        }

        TankDeviceState::updateOrCreate(
            ['tank_id' => $tank->id, 'device_key' => $deviceKey],
            ['state' => $state]
        );

        $this->sendMessage($chatId, $tank->name . ': action recorded - ' . $actionText);
    }

    private function requestPhDose(string $chatId, User $user, Tank $tank, string $deviceKey, string $note): void
    {
        $blockReason = $this->getBlockReason($tank, $deviceKey);
        if ($blockReason) {
            $this->sendMessage($chatId, $tank->name . ': ' . $blockReason);
            return;
        }

        $pending = TankAction::where('tank_id', $tank->id)
            ->whereIn('action_type', self::PH_DOSE_KEYS)
            ->whereIn('status', ['pending', 'dispatched'])
            ->first();

        if ($pending) {
            $this->sendMessage($chatId, $tank->name . ': a pH dose command is already pending.');
            return;
        }

        $recentSameDose = TankAction::where('tank_id', $tank->id)
            ->where('action_type', $deviceKey)
            ->where('requested_at', '>=', now()->subSeconds(self::PH_DOSE_COOLDOWN_SECONDS))
            ->exists();

        if ($recentSameDose) {
            $this->sendMessage($chatId, $tank->name . ': please wait a few seconds before requesting another pH dose.');
            return;
        }

        $label = $deviceKey === 'ph_up' ? 'pH Up' : 'pH Down';

        TankAction::create([
            'tank_id' => $tank->id,
            'user_id' => $user->id,
            'requested_by' => $user->id,
            'action' => $deviceKey . ' dose',
            'action_type' => $deviceKey,
            'note' => $note,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        $this->sendMessage($chatId, $tank->name . ": one {$label} dose has been requested.");
    }

    private function selectedTank(User $user): ?Tank
    {
        $tankId = Cache::get($this->selectedTankCacheKey($user));
        if ($tankId) {
            $tank = Tank::where('user_id', $user->id)->where('id', $tankId)->first();
            if ($tank) {
                return $tank;
            }
        }

        $firstTank = Tank::where('user_id', $user->id)->orderBy('id')->first();
        if ($firstTank) {
            $this->selectTank($user, $firstTank);
        }

        return $firstTank;
    }

    private function selectTank(User $user, Tank $tank): void
    {
        Cache::forever($this->selectedTankCacheKey($user), $tank->id);
    }

    private function selectedTankCacheKey(User $user): string
    {
        return 'telegram_selected_tank_user_' . $user->id;
    }

    private function sendTankSelection(string $chatId, User $user, string $message = 'Choose a tank for Telegram controls, status, and alerts:'): void
    {
        $tanks = Tank::where('user_id', $user->id)->orderBy('name')->get(['id', 'name']);
        if ($tanks->isEmpty()) {
            $this->sendMessage($chatId, 'No tanks found for your account.');
            return;
        }

        $buttons = $tanks->map(function (Tank $tank) {
            return [[
                'text' => $tank->name,
                'callback_data' => 'tank_select_' . $tank->id,
            ]];
        })->values()->all();

        $this->sendMessage($chatId, $message, [
            'inline_keyboard' => $buttons,
        ]);
    }

    private function resolveTankForCommand(User $user, string $text): ?Tank
    {
        $parts = preg_split('/\s+/', $text);
        $target = $parts[1] ?? null;
        $tankQuery = Tank::where('user_id', $user->id);
        if ($target && is_numeric($target)) {
            return $tankQuery->where('id', (int) $target)->first();
        }
        if ($target) {
            return $tankQuery->where('name', $target)->first();
        }

        return $this->selectedTank($user);
    }

    private function tankStatusText(Tank $tank): string
    {
        $lines = ["Status for {$tank->name}:"];
        foreach (['pH', 'Turbidity', 'Water Level'] as $parameter) {
            $lines[] = $this->parameterStatusText($tank, $parameter, false);
        }

        return implode("\n", $lines);
    }

    private function parameterStatusText(Tank $tank, string $parameter, bool $includeTankName = true): string
    {
        $latest = TankReading::where('tank_id', $tank->id)
            ->where('parameter', $parameter)
            ->orderByDesc('recorded_at')
            ->orderByDesc('id')
            ->first();

        $label = $includeTankName ? "{$tank->name} {$parameter}" : $parameter;
        if (!$latest || !is_numeric($latest->value)) {
            return "{$label}: No reading yet.";
        }

        $thresholds = TankThreshold::where('tank_id', $tank->id)->get()->keyBy('parameter');
        $fallback = Threshold::all()->keyBy('parameter');
        $threshold = $thresholds->get($parameter) ?? $fallback->get($parameter);
        $unit = $latest->unit ? ' ' . $latest->unit : '';
        $displayValue = number_format((float) $latest->value, 2);
        $status = 'Status unavailable';
        if ($parameter === 'Water Level') {
            $value = $tank->actualWaterLevelFromDistance((float) $latest->value);
            $range = $this->waterLevelRangeForTank($tank);
            $displayValue = number_format($value, 2);
            $status = $value >= $range['min'] && $value <= $range['max']
                ? 'Good'
                : 'Warning';
        } elseif ($parameter === 'pH' && ($range = $this->speciesPhRangeForTank($tank))) {
            $value = (float) $latest->value;
            $status = $value >= $range['min'] && $value <= $range['max']
                ? 'Good'
                : 'Warning';
        } elseif ($threshold && is_numeric($threshold->min_value) && is_numeric($threshold->max_value)) {
            $value = (float) $latest->value;
            $status = $value >= (float) $threshold->min_value && $value <= (float) $threshold->max_value
                ? 'Good'
                : 'Warning';
        }

        return "{$label}: {$displayValue}{$unit} ({$status})";
    }

    private function parseDeviceState(string $actionText): ?array
    {
        $text = strtolower($actionText);
        $state = null;
        if (str_contains($text, ' on')) {
            $state = true;
        }
        if (str_contains($text, ' off')) {
            $state = false;
        }
        if ($state === null) {
            return null;
        }

        $map = [
            'ph pump' => 'ph_up',
            'ph up' => 'ph_up',
            'dosing' => 'ph_up',
            'ph down' => 'ph_down',
            'top-up' => 'topup',
            'topup' => 'topup',
            'water pump' => 'topup',
            'feeder' => 'feeder',
            'feed' => 'feeder',
        ];

        foreach ($map as $needle => $deviceKey) {
            if (str_contains($text, $needle)) {
                return ['device_key' => $deviceKey, 'state' => $state];
            }
        }

        return null;
    }

    private function getBlockReason(Tank $tank, string $deviceKey): ?string
    {
        $sensor = $this->deviceToSensor($deviceKey);
        if (!$sensor) {
            return null;
        }

        $activeParameters = Parameter::where('status', 'Active')->pluck('name')->flip();
        if (!$activeParameters->has($sensor)) {
            return null;
        }

        $latest = TankReading::where('tank_id', $tank->id)
            ->where('parameter', $sensor)
            ->orderByDesc('recorded_at')
            ->orderByDesc('id')
            ->first();

        if (!$latest || !is_numeric($latest->value)) {
            return 'No recent reading available. Try again after the next sensor update.';
        }

        $thresholds = TankThreshold::where('tank_id', $tank->id)->get()->keyBy('parameter');
        $fallback = Threshold::all()->keyBy('parameter');
        $threshold = $thresholds->get($sensor) ?? $fallback->get($sensor);
        $speciesPhRange = $sensor === 'pH' ? $this->speciesPhRangeForTank($tank) : null;
        if ($sensor !== 'Water Level' && !$speciesPhRange && (!$threshold || !is_numeric($threshold->min_value) || !is_numeric($threshold->max_value))) {
            return 'Thresholds are not set for this parameter yet.';
        }

        $value = (float) $latest->value;
        if ($sensor === 'Water Level') {
            $value = $tank->actualWaterLevelFromDistance($value);
            $range = $this->waterLevelRangeForTank($tank);
            $min = $range['min'];
            $max = $range['max'];
        } elseif ($sensor === 'pH' && $speciesPhRange) {
            $min = $speciesPhRange['min'];
            $max = $speciesPhRange['max'];
        } else {
            $min = (float) $threshold->min_value;
            $max = (float) $threshold->max_value;
        }

        if ($value >= $min && $value <= $max) {
            return 'Device not needed. ' . $sensor . ' is in good condition.';
        }

        return null;
    }

    private function speciesPhRangeForTank(Tank $tank): ?array
    {
        $species = $tank->species()->get();
        if ($species->isEmpty()) {
            return null;
        }

        $min = (float) $species->max('min_ph');
        $max = (float) $species->min('max_ph');

        if ($min > $max) {
            return null;
        }

        return [
            'min' => $min,
            'max' => $max,
        ];
    }

    private function waterLevelRangeForTank(Tank $tank): array
    {
        $threshold = TankThreshold::where('tank_id', $tank->id)
            ->where('parameter', 'Water Level')
            ->first();

        return $tank->waterLevelRange(
            is_numeric($threshold?->min_value) ? (float) $threshold->min_value : null,
            is_numeric($threshold?->max_value) ? (float) $threshold->max_value : null
        );
    }

    private function deviceToSensor(string $deviceKey): ?string
    {
        return match ($deviceKey) {
            'ph_up', 'ph_down' => 'pH',
            'topup' => 'Water Level',
            'feeder' => null,
            default => null,
        };
    }

}
