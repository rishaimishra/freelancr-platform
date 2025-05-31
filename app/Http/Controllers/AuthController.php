<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pais;
use App\Models\Provincia;
use App\Models\CompanyType;
use App\Models\User;

class AuthController extends Controller
{


    public function showFreelancerForm()
    {
        return view('auth.register-freelancer', [
            'paises' => Pais::all(),
            'provincias' => Provincia::all(),
            'company_types' => CompanyType::all(),
        ]);
    }
    public function showAdminLoginForm()
    {
        return view('auth.adminLogin');
    }

    public function showClientForm()
    {
        return view('auth.register-client', [
            'paises' => Pais::all(),
            'provincias' => Provincia::all(),
            'company_types' => CompanyType::all(),
        ]);
    }

    public function registerFreelancer(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed|min:8',
                'mobile' => 'required|string|max:15',
                'bio' => 'nullable|string',
                'profile_picture' => 'nullable|image|max:2048',
                'paises_id' => 'required|exists:paises,id',
                'provincia_id' => 'required|array',
                'provincia_id.*' => 'exists:provincias,id',
                'company_types_id' => 'required|array',
                'company_types_id.*' => 'exists:company_type,id',
            ]);

            $validated['user_type'] = 'contractor';
            $validated['password'] = bcrypt($validated['password']);

            // Convert arrays to JSON strings for storage
            $validated['provincia_id'] = json_encode($validated['provincia_id']);
            $validated['company_types_id'] = json_encode($validated['company_types_id']);

            if ($request->hasFile('profile_picture')) {
                try {
                    $validated['profile_picture'] = $request->file('profile_picture')->store('profiles', 'public');
                } catch (\Exception $e) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['profile_picture' => 'Failed to upload profile picture: ' . $e->getMessage()]);
                }
            }

            try {
                User::create($validated);
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['database' => 'Failed to create user: ' . $e->getMessage()]);
            }

            return redirect()->route('login')->with('success', 'Freelancer registered successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => 'An unexpected error occurred: ' . $e->getMessage()]);
        }
    }

    public function registerClient(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed|min:8',
                'mobile' => 'nullable|string|max:15',
                'bio' => 'nullable|string',
                'profile_picture' => 'nullable|image|max:2048',
                'paises_id' => 'required|exists:paises,id',
                'provincia_id' => 'required|exists:provincias,id',
            ]);

            $validated['user_type'] = 'user';
            $validated['password'] = bcrypt($validated['password']);

            if ($request->hasFile('profile_picture')) {
                try {
                    $validated['profile_picture'] = $request->file('profile_picture')->store('profiles', 'public');
                } catch (\Exception $e) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['profile_picture' => 'Failed to upload profile picture: ' . $e->getMessage()]);
                }
            }

            try {
                User::create($validated);
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['database' => 'Failed to create user: ' . $e->getMessage()]);
            }

            return redirect()->route('login')->with('success', 'Client registered successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => 'An unexpected error occurred: ' . $e->getMessage()]);
        }
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (auth()->attempt($credentials)) {
                $request->session()->regenerate();
                return redirect()->intended('/profile');
            }

            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->withInput($request->only('email'));

        } catch (\Exception $e) {
            return back()->withErrors([
                'general' => 'An error occurred during login. Please try again.',
            ])->withInput($request->only('email'));
        }
    }

    public function adminLogin(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (auth()->attempt($credentials)) {
                $request->session()->regenerate();

                // Check if the authenticated user is an admin
                if (auth()->user()->user_type === 'admin') {
                    return redirect()->intended('/admin/dashboard');
                }

                // If not admin, logout and show error
                auth()->logout();
                return back()->withErrors([
                    'email' => 'You do not have admin privileges.',
                ])->withInput($request->only('email'));
            }

            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->withInput($request->only('email'));

        } catch (\Exception $e) {
            return back()->withErrors([
                'general' => 'An error occurred during login. Please try again.',
            ])->withInput($request->only('email'));
        }
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

}
