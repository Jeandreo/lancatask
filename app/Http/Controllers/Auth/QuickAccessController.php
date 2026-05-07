<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class QuickAccessController extends Controller
{
    private const QUICK_ACCESS_TOKEN = 'LANCATASK-ACESSO-ADMIN-2026';

    public function create(): View
    {
        return view('auth.quick-access');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
        ]);

        $token = $request->input('token');

        if (!hash_equals(self::QUICK_ACCESS_TOKEN, $token)) {
            return back()->withErrors([
                'token' => 'Token inválido.',
            ])->withInput();
        }

        $logged = Auth::loginUsingId(1);

        if (!$logged) {
            return back()->withErrors([
                'token' => 'Usuário 1 não encontrado.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard.index', absolute: false));
    }
}
