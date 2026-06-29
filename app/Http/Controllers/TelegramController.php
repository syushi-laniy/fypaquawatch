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

            $this->sendMenu($chatId, 'Linked successfully. Choose an AquaWatch action:');
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
            $tank = $this->defaultTank($user);
            $this->sendMessage($chatId, $tank ? $this->parameterStatusText($tank, 'pH') : 'No tanks found for your account.');
            return response('ok', 200);
        }

        if ($text === '/turbidity') {
            $tank = $this->defaultTank($user);
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
            $lines = ['Your tanks:'];
            foreach ($tanks as $tank) {
                $lines[] = $tank->id . ' - ' . $tank->name;
            }
            $this->sendMessage($chatId, implode("\n", $lines));
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

        if ($data === 'status') {
            $tank = $this->defaultTank($user);
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
                    ['text' => 'Check Status', 'callback_data' => 'status'],
                ],
                [
                    ['text' => 'pH Up ON', 'callback_data' => 'ph_up_on'],
                    ['text' => 'pH Up OFF', 'callback_data' => 'ph_up_off'],
                ],
                [
                    ['text' => 'pH Down ON', 'callback_data' => 'ph_down_on'],
                    ['text' => 'pH Down OFF', 'callback_data' => 'ph_down_off'],
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
        Cache::remember('telegram_bot_commands_registered_v3', now()->addDay(), function () {
            $token = config('services.telegram.token');
            if (!$token) {
                return true;
            }

            Http::post("https://api.telegram.org/bot{$token}/setMyCommands", [
                'commands' => [
                    ['command' => 'status', 'description' => 'Check latest aquarium reading'],
                    ['command' => 'ph', 'description' => 'Check pH status'],
                    ['command' => 'turbidity', 'description' => 'Check water clarity status'],
                    ['command' => 'ph_up_on', 'description' => 'Turn ON pH up pump'],
                    ['command' => 'ph_up_off', 'description' => 'Turn OFF pH up pump'],
                    ['command' => 'ph_down_on', 'description' => 'Turn ON pH down pump'],
                    ['command' => 'ph_down_off', 'description' => 'Turn OFF pH down pump'],
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
            '/status - Check latest aquarium reading',
            '/ph - Check pH status',
            '/turbidity - Check water clarity status',
            '/ph_up_on - Turn ON pH up pump',
            '/ph_up_off - Turn OFF pH up pump',
            '/ph_down_on - Turn ON pH down pump',
            '/ph_down_off - Turn OFF pH down pump',
            '/water_pump_on - Turn ON water pump',
            '/water_pump_off - Turn OFF water pump',
            '/feed_now - Start fish feeding',
            '/help - Show available commands',
        ]);
    }

    private function commandToAction(string $text): ?array
    {
        return match ($text) {
            '/ph_pump_on', '/ph_up_on' => ['device_key' => 'ph_up', 'state' => true, 'action' => 'pH up pump on'],
            '/ph_pump_off', '/ph_up_off' => ['device_key' => 'ph_up', 'state' => false, 'action' => 'pH up pump off'],
            '/ph_down_on' => ['device_key' => 'ph_down', 'state' => true, 'action' => 'pH down pump on'],
            '/ph_down_off' => ['device_key' => 'ph_down', 'state' => false, 'action' => 'pH down pump off'],
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
        $tank ??= $this->defaultTank($user);
        if (!$tank) {
            $this->sendMessage($chatId, 'No tanks found for your account.');
            return;
        }

        if (($tank->control_mode ?? 'auto') === 'auto') {
            $this->sendMessage($chatId, 'Auto mode is enabled for this tank. Switch to manual to control devices.');
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

    private function defaultTank(User $user): ?Tank
    {
        return Tank::where('user_id', $user->id)->orderBy('name')->first();
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

        return $this->defaultTank($user);
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
        $status = 'Status unavailable';
        if ($threshold && is_numeric($threshold->min_value) && is_numeric($threshold->max_value)) {
            $value = (float) $latest->value;
            $status = $value >= (float) $threshold->min_value && $value <= (float) $threshold->max_value
                ? 'Good'
                : 'Warning';
        }

        return "{$label}: {$latest->value}{$unit} ({$status})";
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
        if (!$threshold || !is_numeric($threshold->min_value) || !is_numeric($threshold->max_value)) {
            return 'Thresholds are not set for this parameter yet.';
        }

        $value = (float) $latest->value;
        $min = (float) $threshold->min_value;
        $max = (float) $threshold->max_value;

        if ($value >= $min && $value <= $max) {
            return 'Device not needed. ' . $sensor . ' is in good condition.';
        }

        return null;
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
