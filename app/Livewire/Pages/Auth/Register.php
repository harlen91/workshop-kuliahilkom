<?php

namespace App\Livewire\Pages\Auth;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $phone_number = '';

    public string $institution = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function register(): void
    {
        $this->validate([
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone_number' => 'required|string|min:8|max:20',
            'institution' => 'nullable|string|max:255',
            'password' => 'required|min:6|confirmed',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan masuk.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'institution' => $this->institution,
            'password' => Hash::make($this->password),
            'role' => 'participant',
        ]);

        Auth::login($user);

        session()->flash('success', 'Akun berhasil dibuat! Selamat datang di KuliahIlkom Workshop.');
        $this->redirectRoute('participant.tickets');
    }

    public function render(): View
    {
        return view('livewire.pages.auth.register');
    }
}
