<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PublicUser;
use App\Models\Agency;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str; 

class UserProfileController extends Controller
{
    //Create Profile Public User
    public function showRegistrationForm()
    {
        return view('ManageUserUI.RegisterPublic');
    }

    public function store(Request $request)
    {
        // Validate request
        $validatedData = $request->validate([
            'Name' => 'required|string|max:255',
            'Email' => 'required|email|unique:user,Email|max:255',
            'Password' => 'required|confirmed',
            'PhoneNum' => 'nullable|string|max:20',
            'Gender' => 'nullable|in:male,female'
        ]);

        DB::beginTransaction();

        try {
            // Generate UserID
            $lastUser = User::orderBy('UserID', 'desc')->first();
            $newUserNumber = $lastUser ? (int) filter_var($lastUser->UserID, FILTER_SANITIZE_NUMBER_INT) + 1 : 1;
            $newUserID = 'U' . $newUserNumber;

            // Create user
            $user = User::create([
                'UserID' => $newUserID,
                'Email' => $request->Email,
                'Password' => $request->Password, // Plaintext, as per your request
                'Name' => $request->Name,
                'PhoneNum' => $request->PhoneNum,
                'Role' => 'publicuser',
                'Created_At' => now(),
            ]);

            if (!$user) {
                throw new \Exception('User creation failed.');
            }

            // Generate PublicID
            $lastPublic = PublicUser::orderBy('PublicID', 'desc')->first();
            $newPublicNumber = $lastPublic ? (int) filter_var($lastPublic->PublicID, FILTER_SANITIZE_NUMBER_INT) + 1 : 1;
            $newPublicID = 'P' . $newPublicNumber;

            // Create public user record
            PublicUser::create([
                'PublicID' => $newPublicID,
                'UserID' => $user->UserID,
                'Gender' => $request->Gender,
            ]);

            DB::commit();
            return redirect()->route('login')->with('success', 'Account created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()]);
        }
    }

    //View Profile
    public function view()
    {
        $user = Auth::user();

        $user->load($user->Role); // Eager load role-specific data

        return view('ManageUserUI.ViewProfile', compact('user'));
    }

    //Edit Profile
    public function edit()
    {
        if (!in_array(session('user_role'), ['publicuser', 'agency'])) {
            abort(403, 'Unauthorized action.');
        }
        
        $user = Auth::user();
        $user->load($user->Role); // Eager load role-specific data
        return view('ManageUserUI.EditProfile', compact('user'));
    }

    public function saveEdit(Request $request)
    {
        if (!in_array(session('user_role'), ['publicuser', 'agency'])) {
            abort(403, 'Unauthorized action.');
        }
        
        $user = Auth::user();

        // Validate common fields
        $validated = $request->validate([
            'Name' => 'required|string|max:30',
            'Email' => 'required|email|max:30|unique:user,Email,' . $user->UserID . ',UserID',
            'PhoneNum' => 'nullable|string|max:20',
            'ProfilePic' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);

        // Save profile pic if uploaded
        if ($request->hasFile('ProfilePic')) {
            $image = $request->file('ProfilePic');
            $user->ProfilePic = file_get_contents($image->getRealPath());
        }

        // Update core user info
        $user->Name = $validated['Name'];
        $user->Email = $validated['Email'];
        $user->PhoneNum = $validated['PhoneNum'] ?? null;
        $user->Updated_At = now();
        $user->save();

        // Role-specific update
        if ($user->Role === 'publicuser') {
            $user->publicuser()->updateOrCreate(['UserID' => $user->UserID], [
                'Gender' => $request->Gender,
            ]);
        } elseif ($user->Role === 'mcmc') {
            $user->mcmc()->updateOrCreate(['UserID' => $user->UserID], [
                'Position' => $request->Position,
            ]);
        } elseif ($user->Role === 'agency') {
            $user->agency()->updateOrCreate(['UserID' => $user->UserID], [
                'AgencyName' => $request->AgencyName,
            ]);
        }

        return redirect()->route('profile.view')->with('success', 'Profile updated successfully.');
    }

    //Update Security
    public function showUpdateSecurityForm()
    {
        if (!in_array(session('user_role'), ['publicuser', 'agency'])) {
            abort(403, 'Unauthorized action.');
        }
        $user = Auth::user();
        
        return view('ManageUserUI.updateSecurity'); // your Blade file for the form
    }

    public function updateSecurity(Request $request)
    {
        if (!in_array(session('user_role'), ['publicuser', 'agency'])) {
            abort(403, 'Unauthorized action.');
        }
        
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'confirmed'],
        ]);

        $user = Auth::user(); // No need to import again if already used

        if ($request->current_password !== $user->Password) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->Password = $request->new_password;
        $user->save();

        return redirect()->route('view.profile')->with('success', 'Password updated successfully');
    }

    //Register Agency
    public function registerAgency(Request $request)
    {
        // Validate form input
        $request->validate([
            'Name' => 'required|string|max:30',
            'Email' => 'required|email|unique:user,Email',
            'PhoneNum' => 'nullable|string|max:20',
            'AgencyName' => 'required|string|max:20',
        ]);

        DB::beginTransaction();

        try {
            // Generate new UserID
            $lastUser = User::orderBy('UserID', 'desc')->first();
            $newUserNumber = $lastUser ? (int) filter_var($lastUser->UserID, FILTER_SANITIZE_NUMBER_INT) + 1 : 1;
            $newUserID = 'U' . $newUserNumber;

            // Auto-generate password (not hashed, per your request)
            $password = Str::random(8); // 8 characters or less

            // Create user record
            $user = User::create([
                'UserID' => $newUserID,
                'Email' => $request->Email,
                'Password' => $password,
                'Name' => $request->Name,
                'PhoneNum' => $request->PhoneNum,
                'Role' => 'agency',
                'Created_At' => now(),
            ]);

            if (!$user) {
                throw new \Exception('User creation failed.');
            }

            // Generate new AgencyID
            $lastAgency = Agency::orderBy('AgencyID', 'desc')->first();
            $newAgencyNumber = $lastAgency ? (int) filter_var($lastAgency->AgencyID, FILTER_SANITIZE_NUMBER_INT) + 1 : 1;
            $newAgencyID = 'A' . $newAgencyNumber;

            // Create agency record
            Agency::create([
                'AgencyID' => $newAgencyID,
                'UserID' => $user->UserID,
                'AgencyName' => $request->AgencyName,
            ]);

            DB::commit();

            return back()->with('success', "Agency staff registered successfully. Password: $password");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()]);
        }
    }
}
