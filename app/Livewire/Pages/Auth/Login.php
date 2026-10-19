<?php

namespace App\Livewire\Pages\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = true;

    public ?string $errorMessage = null;

    public function login(): void
    {
        $this->errorMessage = null;

        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();

            if (Auth::user()->isAdmin()) {
                $this->redirect('/admin');

                return;
            }

            $this->redirectRoute('participant.tickets');

            return;
        }

        $this->errorMessage = 'Email atau password yang Anda masukkan salah.';
    }

    public function render(): View
    {
        return view('livewire.pages.auth.login');
    }
}
