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
    //1.Login authentication
    public function authenticate(Request $request)
    {
        // Validate input
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'required'
        ]);

        // Look up user with correct role
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

            // ✅ FIRST TIME LOGIN redirect for AGENCY
            if ($user->Role === 'agency' && is_null($user->Login_At)) {
                return redirect()->route('first.time.password')->with('user_id', $user->UserID);
            }

            // Optional: Save login time to DB (do this only AFTER first time password change)
            $user->Login_At = now();
            $user->save();

            // Store role-specific profile ID
            switch ($user->Role) {
                case 'publicuser':
                    $public = PublicUser::where('UserID', $user->UserID)->first();
                    session(['profile_id' => $public?->PublicID]);
                    return redirect()->route('display.home')->with('success', 'Welcome, Public User!');

                case 'mcmc':
                    $mcmc = MCMC::where('UserID', $user->UserID)->first();
                    session(['profile_id' => $mcmc?->McmcID]);
                    return redirect()->route('display.home')->with('success', 'Welcome, MCMC Staff!');

                case 'agency':
                    $agency = Agency::where('UserID', $user->UserID)->first();
                    session(['profile_id' => $agency?->AgencyID]);
                    return redirect()->route('display.home')->with('success', 'Welcome, Agency!');

                default:
                    return back()->with('error', 'Role not recognized.');
            }
        }

        return back()->with('error', 'Invalid email, password, or role.');
    }

    //2..First Time Login Change Password for Agency
    public function showFirstTimePasswordForm()
    {
        $user = Auth::user();

        if (!$user || $user->Role !== 'agency' || $user->Login_At !== null) {
            return redirect()->route('home');
        }

        return view('ManageUserUI.FirstTimeLoginAgency', ['userID' => $user->UserID]);
    }

    public function saveFirstTimePassword(Request $request)
    {
        $request->validate([
            'new_password' => 'required|confirmed',
        ]);

        $user = Auth::user();
        $user->Password = $request->new_password; // or use bcrypt() if you're using hashing later
        $user->Login_At = now();
        $user->save();

        return redirect()->route('home')->with('success', 'Password updated successfully.');
    }

    //3.Display Home
    public function displayHome()
    {
        if (!session()->has('user_role')) {
            // User is not logged in, redirect to login
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        // User is logged in, display home
        $user = session('user');
        $role = session('user_role');

        return view('SharedUI.HomepageUI', [
            'userName' => $user['Name'] ?? 'User',
            'userRole' => $role,
        ]);
    }

    //4.Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return view('ManageUserUI.Logout'); // or redirect to login page
    }
}
