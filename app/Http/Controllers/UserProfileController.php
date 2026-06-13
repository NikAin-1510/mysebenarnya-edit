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
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RegisteredUserExport;
use Illuminate\Support\Facades\Hash; //Add Hash import for password hashing

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
            //'Password' => 'required|confirmed',
            'Password' => 'required|confirmed|min:8',  //add min 8 chracters for password 
            'PhoneNum' => 'nullable|string|max:20',
            'Gender' => 'nullable|in:male,female'
        ]);

        DB::beginTransaction();

        try {
            // Generate UserID using numeric MAX to avoid alphabetical sort duplicates
            $maxUserNum = DB::table('user')
                ->selectRaw("COALESCE(MAX(CAST(SUBSTRING(UserID, 2) AS UNSIGNED)), 0) as max_num")
                ->value('max_num');
            $newUserID = 'U' . ($maxUserNum + 1);

            // Create user
            $user = User::create([
                'UserID' => $newUserID,
                'Email' => $request->Email,
                'password' => Hash::make($request->Password), // [MAINTENANCE] Hash password using bcrypt
                'Name' => $request->Name,
                'PhoneNum' => $request->PhoneNum,
                'Role' => 'publicuser',
                'Created_At' => now(),
            ]);

            if (!$user) {
                throw new \Exception('User creation failed.');
            }

            // Generate PublicID using numeric MAX to avoid alphabetical sort duplicates
            $maxPublicNum = DB::table('publicuser')
                ->selectRaw("COALESCE(MAX(CAST(SUBSTRING(PublicID, 2) AS UNSIGNED)), 0) as max_num")
                ->value('max_num');
            $newPublicID = 'P' . ($maxPublicNum + 1);

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
        } elseif ($user->Role === 'agency') {
            $user->agency()->updateOrCreate(['UserID' => $user->UserID], [
                'AgencyName' => $request->AgencyName,
            ]);
        }

        return redirect()->route('view.profile')->with('success', 'Profile updated successfully.');
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

        if (!Hash::check($request->current_password, $user->password)) { // Use Hash::check to verify current password
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->password = Hash::make($request->new_password);// Hash the new password for security
        $user->save();

        return redirect()->route('view.profile')->with('success', 'Password updated successfully');
    }

    //Register Agency
    public function showRegisterAgency(){
        return view('ManageUserUI.RegisterAgency');
    }

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
            #$lastUser = User::orderByRaw('CAST(SUBSTRING(UserID, 2) AS UNSIGNED) DESC')->first();
            #$newUserNumber = $lastUser ? (int) filter_var($lastUser->UserID, FILTER_SANITIZE_NUMBER_INT) + 1 : 1;
            #$newUserID = 'U' . $newUserNumber;

            // Generate UserID — use MAX() to avoid alphabetical sort duplicates
            $maxNumber = DB::table('user')
                ->selectRaw("COALESCE(MAX(CAST(SUBSTRING(UserID, 2) AS UNSIGNED)), 0) as max_num")
                ->value('max_num');
            $newUserID = 'U' . ($maxNumber + 1);

            // Auto-generate password (not hashed, per your request)
            $password = Str::random(8); // 8 characters or less

            // Create user record
            $user = User::create([
                'UserID' => $newUserID,
                'Email' => $request->Email,
                'password' => $password,
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

    //View List of All Users
    public function viewAllUsers(){
        if (session('user_role') !== 'mcmc') {
            return redirect('/login')->with('error', 'Unauthorized access.');
        }

        $users = User::select('UserID', 'Name', 'Role')
                 ->whereIn('Role', ['agency', 'publicuser'])
                 ->orderBy('Name')
                 ->get();
        return view('ManageUserUI.ViewAllUsers', compact('users'));
    }

    //View User Data including user profile, registration details and activity logs
    public function viewUserData($id)
    {
        if (session('user_role') !== 'mcmc') {
            return redirect('/login')->with('error', 'Unauthorized access.');
        }

        $user = User::with(['publicuser', 'agency'])
                    ->where('UserID', $id)
                    ->whereIn('Role', ['publicuser', 'agency'])
                    ->firstOrFail(); 

        return view('ManageUserUI.ViewUserData', compact('user'));
    }

    //Report Dashbord
    public function displayReportDashboard()
    {
        return view('SharedUI.ReportDashboardUI');
    }
    //User Reports
    public function showUserReport(Request $request)
    {
        [$users, $agencies] = $this->queryUsers($request);

        return view('ManageUserUI.RegisteredUserReportUI', [
            'users'    => $users,
            'agencies' => $agencies,
            'total'    => $users->count(),
            'pdf'      => false,
        ]);
    }

    public function downloadPdf(Request $request)
    {
        [$users, $agencies] = $this->queryUsers($request);

        $pdf = Pdf::loadView('ManageUserUI.RegisteredUserReportUI', [
            'users'    => $users,
            'agencies' => $agencies,
            'total'    => $users->count(),
            'pdf'      => true,
        ])->setPaper('A4', 'portrait');

        return $pdf->download('RegisteredUserReport.pdf');
    }

    private function queryUsers(Request $request)
    {
        $query = DB::table('user');

        if ($request->filter_by === 'role' && $request->role) {
            $query->where('Role', $request->role);
        }

        if ($request->filter_by === 'date' && $request->date) {
            $query->whereDate('Created_At', $request->date);
        }

        if ($request->filter_by === 'agency' && $request->agency) {
            $query->join('agency', 'user.UserID', '=', 'agency.UserID')
                  ->where('agency.AgencyName', $request->agency);
        }

        $users    = $query->select('user.*')->get();
        $agencies = DB::table('agency')->distinct()->get(['AgencyName']);

        return [$users, $agencies];
    }

    public function downloadExcel(Request $request)
    {
        [$users] = $this->queryUsers($request);
        return Excel::download(new RegisteredUserExport($users), 'RegisteredUserReport.xlsx');
    }
}
