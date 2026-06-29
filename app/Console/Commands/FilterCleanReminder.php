<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FilterCleanReminder extends Command
{
    protected $signature = 'reminder:filter-clean';
    protected $description = 'Send filter cleaning reminders to users with tanks';

    public function handle(): int
    {
        $token = config('services.telegram.token');
        if (!$token) {
            $this->error('TELEGRAM_BOT_TOKEN is not set.');
            return self::FAILURE;
        }

        $users = User::whereNotNull('telegram_chat_id')
            ->whereHas('tanks')
            ->get();

        foreach ($users as $user) {
            Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $user->telegram_chat_id,
                'text' => 'Reminder: Please clean or check your filter system.',
            ]);
        }

        $this->info('Filter clean reminders sent: ' . $users->count());
        return self::SUCCESS;
    }
}
