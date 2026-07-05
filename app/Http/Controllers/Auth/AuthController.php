<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:mahasiswa,dosen'],
            // field khusus mahasiswa
            'nim' => ['required_if:role,mahasiswa', 'nullable', 'string', 'unique:mahasiswas,nim'],
            'program_studi' => ['required_if:role,mahasiswa', 'nullable', 'string'],
            'semester' => ['required_if:role,mahasiswa', 'nullable', 'integer', 'min:1', 'max:8'],
            // field khusus dosen
            'nidn' => ['required_if:role,dosen', 'nullable', 'string', 'unique:dosens,nidn'],
            'bidang_keahlian' => ['nullable', 'string'],
        ]);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        if ($request->role === 'mahasiswa') {
            Mahasiswa::create([
                'user_id' => $user->id,
                'nim' => $request->nim,
                'program_studi' => $request->program_studi,
                'semester' => $request->semester,
            ]);
        } else {
            Dosen::create([
                'user_id' => $user->id,
                'nidn' => $request->nidn,
                'bidang_keahlian' => $request->bidang_keahlian,
            ]);
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
