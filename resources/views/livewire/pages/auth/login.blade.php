<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="space-y-6">
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-5">
        <div>
            <label for="email" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Alamat Email Admin</label>
            <input wire:model="form.email" id="email" type="email" name="email" required autofocus autocomplete="username"
                   class="w-full rounded-lg border-gray-700 bg-gray-900/80 text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Kata Sandi</label>
            <input wire:model="form.password" id="password" type="password" name="password" required autocomplete="current-password"
            class="w-full rounded-lg border-gray-700 bg-gray-900/80 text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
            </div>
            <div class="flex justify-between items-center">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs text-blue-400 hover:text-blue-300 underline" wire:navigate>
                        Lupa sandi?
                    </a>
                @endif
            </div>

        {{-- <div class="flex items-center">
            <input wire:model="form.remember" id="remember" type="checkbox" name="remember"
                   class="rounded border-gray-700 bg-gray-900 text-blue-600 shadow-sm focus:ring-blue-500/20 focus:ring-offset-gray-900">
            <label for="remember" class="ms-2 text-sm text-gray-400 select-none">Ingat sesi saya di perangkat ini</label>
        </div> --}}

        <div class="pt-2">
            <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold tracking-wide uppercase transition shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                Masuk ke Panel Kontrol
            </button>
        </div>
    </form>
</div>
