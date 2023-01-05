<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit_profile()
    {
        return view('admin.profile');
    }

    public function edit_profile_submit(Request $request)
    {
        $admin_data = Admin::where('email', Auth::guard('admin')->user()->email)->first();
        $request->validate([
            'name'  =>  ['required'],
            'email' =>  ['required', 'email'],
        ]);

        if ($request->password != '') {
            $request->validate([
                'password'  =>  ['required', 'min:6'],
                'retype_password'   =>  ['required', 'same:password'],
            ]);

            $admin_data->password = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            $request->validate([
                'photo' =>  ['image', 'mimes:png,jpg,jpeg,gif'],
            ]);

            if ($admin_data->photo != '') {
                unlink(public_path('uploads/' . $admin_data->photo));
            }

            $ext = $request->file('photo')->extension();
            $final_name = 'admin' . '.' . $ext;

            $request->file('photo')->move(public_path('uploads/'), $final_name);

            $admin_data->photo = $final_name;
        }

        $admin_data->name = $request->name;
        $admin_data->email = $request->email;
        $admin_data->update();

        return redirect()->back()->with('success', 'Profile ionformation is changed successfully!!');
    }
}
