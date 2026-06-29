<?php

namespace App\Http\Controllers;

use App\Models\Tank;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TelegramLinkController extends Controller
{
    public function index(Request $request)
    {
        $tankId = $request->session()->get('selected_tank_id');
        $tank = $tankId
            ? Tank::where('user_id', $request->user()->id)->where('id', $tankId)->first()
            : null;

        if (!$tank) {
            $tank = Tank::where('user_id', $request->user()->id)->orderBy('id')->first();
        }

        return view('telegram-integration.index', compact('tank'));
    }

    public function generate(Request $request)
    {
        $user = $request->user();
        $user->telegram_link_token = Str::random(32);
        $user->save();

        return redirect()->route('telegram.index')
            ->with('telegram_link_token', $user->telegram_link_token);
    }
}
