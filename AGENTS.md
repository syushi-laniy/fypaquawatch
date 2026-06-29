# AquaWatch Backend (Laravel) - Agent Notes

This file summarizes prior work and current state so new chats can continue without re-discovery.

## Current Status (Hostinger)
- Telegram bot works on Hostinger (webhook points to Hostinger).
- Manual mode actions from Telegram and web both update DB correctly.
- Dashboard sync uses `syncDeviceStates()` polling every 3 seconds.
- If UI looks stale after deploy: clear view/cache or hard refresh.


## Hostinger Deployment Notes
- Project lives in `public_html/aquawatch_app`.
- SSH path to app: `~/domains/seashell-eland-922052.hostingersite.com/public_html/aquawatch_app`
- If `artisan` not found, you’re in the wrong directory.
- Ensure `.env` on Hostinger has:
  - `APP_URL=https://seashell-eland-922052.hostingersite.com`

## Telegram Webhook
- Webhook is `POST /api/telegram/webhook` (no CSRF).
- Commands:
  - `/link <code>` links user.
  - `/tanks` lists tanks.
  - `/status [tank_id|name]`
  - `/action <tank_id|name> <text>`
- Auto mode blocks actions; manual mode allows.
- Blocks for “good condition” and “already ON/OFF”.

## Device State Sync
- Device state stored in `tank_device_states`.
- API:
  - `GET /tanks/{tank}/devices` returns all keys with defaults.
  - `POST /tanks/{tank}/devices` updates state.
- `TankDeviceStateController@index` returns complete map.
- `UserDashboardController@show` passes `deviceStates` to view for initial toggle state.

## Alerts
- Alerts are batched into one Telegram message per reading save (⚠️ icon).
- Toggle warnings are web-only (not sent to Telegram).

## Control Modes
- Tank `control_mode` field (`auto` / `manual`).
- Mode update route: `POST /tanks/{tank}/mode`.
- Switching to manual resets device states OFF in DB.

## Shop / Stripe (Phase 1 + 2)
- Shop, Cart, Orders, Addresses implemented.
- Stripe Checkout via HTTP (no SDK).
- Webhook: `POST /api/stripe/webhook` with signature verification.
- Orders store `payment_intent_id`, `fulfillment_status`.

## Key Files
- `resources/views/user/dashboard.blade.php` (sensor UI, device toggles, sync JS)
- `app/Http/Controllers/TelegramController.php`
- `app/Http/Controllers/TankDeviceStateController.php`
- `app/Http/Controllers/UserDashboardController.php`
- `routes/web.php`, `routes/api.php`

## Latest JS Fix (dashboard)
- Added `getControlMode()` and replaced static `controlMode`.
- `syncDeviceStates()` now uses live mode.
- `setInterval(syncDeviceStates, 3000)` always runs.

