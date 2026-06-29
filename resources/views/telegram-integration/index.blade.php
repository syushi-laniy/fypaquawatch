@extends('user.layouts.app')

@section('title', 'Telegram Integration')

@section('content')
<style>
    .telegram-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }
    .telegram-card {
        border: 1px solid #D6E8ED;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 4px 12px rgba(15, 87, 110, 0.08);
        height: 100%;
        overflow: hidden;
    }
    .telegram-card-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 16px 18px;
        border-bottom: 1px solid #D6E8ED;
        background: #fff;
        font-weight: 700;
    }
    .telegram-card-body {
        padding: 18px;
    }
    .telegram-icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: rgba(11, 95, 118, 0.12);
        color: #0b5f76;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 34px;
    }
    .telegram-icon svg {
        width: 18px;
        height: 18px;
    }
    .telegram-status-dot {
        width: 10px;
        height: 10px;
        border-radius: 999px;
        display: inline-block;
    }
    .telegram-full {
        grid-column: 1 / -1;
    }
    .command-row,
    .step-row {
        padding: 8px 0;
        border-bottom: 1px solid rgba(11, 95, 118, 0.06);
    }
    .command-row:last-child,
    .step-row:last-child {
        border-bottom: 0;
    }
    .link-command-box {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #F7FBFC;
        border: 1px solid #D6E8ED;
        border-radius: 10px;
        padding: 10px 12px;
    }
    .copy-command-btn {
        width: 36px;
        height: 36px;
        border: 1px solid rgba(11, 95, 118, 0.25);
        border-radius: 8px;
        background: #fff;
        color: #0b5f76;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 36px;
    }
    .copy-command-btn:hover {
        background: rgba(11, 95, 118, 0.08);
    }
    .copy-command-btn svg {
        width: 18px;
        height: 18px;
    }
    @media (max-width: 991.98px) {
        .telegram-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <div class="h4 mb-0 fw-bold">Telegram Integration</div>
    </div>
</div>

<div class="telegram-grid">
    <div class="card-shadow telegram-card">
        <div class="telegram-card-header">
            <span class="telegram-icon">
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="m21 4-4.8 16-4.4-6.8L5 10.8 21 4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    <path d="m11.8 13.2 4.6-4.7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </span>
            <span>Telegram Status</span>
        </div>
        <div class="telegram-card-body">
            @if(auth()->user()->telegram_chat_id)
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="telegram-status-dot bg-success"></span>
                    <span class="fw-semibold">Connected</span>
                </div>
                <div class="small muted mb-3">Your account is linked to Telegram.</div>
            @else
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="telegram-status-dot bg-danger"></span>
                    <span class="fw-semibold">Not Connected</span>
                </div>
                <div class="small muted mb-3">No account linked yet.</div>
            @endif

            @if(session('telegram_link_token'))
                <div class="border rounded-3 p-3 mb-3">
                    <div class="small muted mb-1">Send this command to the bot:</div>
                    <div class="link-command-box">
                        <div class="fw-semibold text-truncate" id="telegram-link-command">/link {{ session('telegram_link_token') }}</div>
                        <button class="copy-command-btn" type="button" id="copy-telegram-command" aria-label="Copy Telegram link command" title="Copy">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M8 8h10v12H8V8Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                <path d="M6 16H4V4h12v2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                    <div class="small text-success mt-2 d-none" id="copy-telegram-feedback">Copied.</div>
                </div>
            @endif

            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-outline-primary" href="https://t.me/aqualaniymonitorBot" target="_blank" rel="noopener">
                    Open Telegram Bot
                </a>
                @if($tank)
                    <form method="POST" action="{{ route('telegram.link') }}">
                        @csrf
                        <button class="btn btn-primary" type="submit">Generate Link Code</button>
                    </form>
                @else
                    <a class="btn btn-primary" href="{{ route('tanks.create') }}">Add Tank First</a>
                @endif
            </div>
        </div>
    </div>

    <div class="card-shadow telegram-card">
        <div class="telegram-card-header">
            <span class="telegram-icon">
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M12 3v4m0 10v4M5.6 5.6l2.8 2.8m8.2 8.2 2.8 2.8M3 12h4m10 0h4M5.6 18.4l2.8-2.8m8.2-8.2 2.8-2.8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </span>
            <span>Alert Settings</span>
        </div>
        <div class="telegram-card-body">
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" checked disabled id="ph-alert">
                <label class="form-check-label" for="ph-alert">pH Warning Alert</label>
            </div>
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" checked disabled id="turbidity-alert">
                <label class="form-check-label" for="turbidity-alert">Turbidity Alert</label>
            </div>
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" checked disabled id="water-alert">
                <label class="form-check-label" for="water-alert">Low Water Level Alert</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" checked disabled id="feeding-alert">
                <label class="form-check-label" for="feeding-alert">Feeding Reminder</label>
            </div>
        </div>
    </div>

    <div class="card-shadow telegram-card">
        <div class="telegram-card-header">
            <span class="telegram-icon">
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M12 18h.01M9 9a3 3 0 1 1 5.1 2.1c-.9.8-2.1 1.5-2.1 3.4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20Z" stroke="currentColor" stroke-width="1.8"/>
                </svg>
            </span>
            <span>How to Connect</span>
        </div>
        <div class="telegram-card-body">
            <div class="step-row">1. Click Generate Link Code.</div>
            <div class="step-row">2. Open Telegram Bot.</div>
            <div class="step-row">3. Send <span class="fw-semibold">/link CODE</span>.</div>
            <div class="step-row">4. Wait for confirmation.</div>
        </div>
    </div>

    <div class="card-shadow telegram-card">
        <div class="telegram-card-header">
            <span class="telegram-icon">
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M4 6h16M4 12h16M4 18h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </span>
            <span>Bot Commands</span>
        </div>
        <div class="telegram-card-body">
            <div class="command-row"><span class="fw-semibold">/start</span> - Start bot</div>
            <div class="command-row"><span class="fw-semibold">/status</span> - View tank status</div>
            <div class="command-row"><span class="fw-semibold">/feed</span> - Trigger feeder</div>
            <div class="command-row"><span class="fw-semibold">/help</span> - Show help</div>
        </div>
    </div>

    <div class="card-shadow telegram-card telegram-full">
        <div class="telegram-card-header">
            <span class="telegram-icon">
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 7h18s-3 0-3-7Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    <path d="M10 19a2 2 0 0 0 4 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </span>
            <span>Test Notification</span>
        </div>
        <div class="telegram-card-body">
            <div class="small muted mb-3">Send a test message to confirm Telegram alert is working.</div>
            @if($tank)
                <form method="POST" action="{{ route('tanks.notify', $tank) }}">
                    @csrf
                    <input type="hidden" name="message" value="AquaWatch test notification for {{ $tank->name }}.">
                    <button class="btn btn-primary" type="submit" {{ auth()->user()->telegram_chat_id ? '' : 'disabled' }}>
                        Send Test Notification
                    </button>
                    @unless(auth()->user()->telegram_chat_id)
                        <div class="small text-warning mt-2">Link your Telegram account before sending a test notification.</div>
                    @endunless
                </form>
            @else
                <a class="btn btn-primary" href="{{ route('tanks.create') }}">Add Tank First</a>
            @endif
        </div>
    </div>
</div>

@if(session('telegram_link_token'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const copyButton = document.getElementById('copy-telegram-command');
            const command = document.getElementById('telegram-link-command');
            const feedback = document.getElementById('copy-telegram-feedback');

            if (!copyButton || !command) return;

            copyButton.addEventListener('click', async () => {
                const text = command.textContent.trim();

                try {
                    await navigator.clipboard.writeText(text);
                } catch (error) {
                    const tempInput = document.createElement('textarea');
                    tempInput.value = text;
                    document.body.appendChild(tempInput);
                    tempInput.select();
                    document.execCommand('copy');
                    document.body.removeChild(tempInput);
                }

                if (feedback) {
                    feedback.classList.remove('d-none');
                    setTimeout(() => feedback.classList.add('d-none'), 1800);
                }
            });
        });
    </script>
@endif
@endsection
