<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PublicUser;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserProfileController extends Controller
{
    public function showRegistrationForm()
    {
        return view('ManageUserUI.Register');
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

}
