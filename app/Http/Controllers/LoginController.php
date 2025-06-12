<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\PublicUser;
use App\Models\MCMC;
use App\Models\Agency;

class LoginController extends Controller
{
    public function authenticate(Request $request)
    {
        // Validate input
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'required'
        ]);

        // Fix: Use correct column names (case-sensitive)
        $user = User::where('Email', $request->email)
                    ->where('Role', $request->role)
                    ->first();

        // Check if user exists and password is correct (plaintext version for now)
        if ($user && $request->password === $user->Password) {
            Auth::login($user); // Log the user in

            // Store basic session data
            session([
                'user_id' => $user->UserID,
                'user_role' => $user->Role,
                'login_at' => now()
            ]);

            // Optional: Save login time to DB
            $user->Login_At = now();
            $user->save();

            // Store role-specific profile ID
            switch ($user->Role) {
                case 'publicuser':
                    $public = PublicUser::where('UserID', $user->UserID)->first();
                    session(['profile_id' => $public?->PublicID]);
                    return redirect()->route('home')->with('success', 'Welcome, Public User!');

                case 'mcmc':
                    $mcmc = MCMC::where('UserID', $user->UserID)->first();
                    session(['profile_id' => $mcmc?->McmcID]);
                    return redirect()->route('home')->with('success', 'Welcome, MCMC Staff!');

                case 'agency':
                    $agency = Agency::where('UserID', $user->UserID)->first();
                    session(['profile_id' => $agency?->AgencyID]);
                    return redirect()->route('home')->with('success', 'Welcome, Agency!');

                default:
                    return back()->with('error', 'Role not recognized.');
            }
        }

        // If authentication fails
        return back()->with('error', 'Invalid credentials.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return view('ManageUserUI.Logout'); // or redirect to login page
    }
}
