<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\Websitemail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function login()
    {
        return view('admin.auth.login');
    }
    public function forget_password()
    {
        return view('admin.auth.forget_password');
    }
    public function login_submit(Request $request)
    {
        $request->validate([

            'email' => ['required', 'email'],
            'password'  =>  ['required', 'min:6'],
        ]);

        $credential = [
            'email' =>   $request->email,
            'password'  =>  $request->password,
        ];

        // ['email' => $request->email, 'password' => $request->password]


        if (Auth::guard('admin')->attempt($credential)) {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('admin.login')->with('error', 'Incorrect password!!');
        }
    }
    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }

    public function forget_password_submit(Request $request)
    {
        $request->validate([

            'email' => ['required', 'email'],

        ]);

        $data = Admin::where('email', $request->email)->first();

        if (!$data) {
            return redirect()->back()->with('error', 'Email address not found!!');
        }

        $token = hash('sha256', time());

        $data->token = $token;
        $data->update();



        $reset_link = url('admin/reset-password/' . $token . '/' . $request->email);
        $subject  = 'Reset password';
        $body = 'Please click on the following link : <br>';
        $body .= '<a href = "' . $reset_link . '">Click here</a>';

        Mail::to($request->email)->send(new Websitemail($subject, $body));

        return redirect()->route('admin.login')->with('sucess', 'Please check your email and follow the steps');
    }

    public function reset_password($token, $email)
    {
        $data = Admin::where('token', $token)->where('email', $email)->first();

        if (!$data) {
            return redirect()->route('admin.login');
        }
        return view('admin.auth.reset_password', compact('token', 'email'));
    }

    public function reset_password_submit(Request $request)
    {
        $request->validate([
            'password'  =>  ['required', 'min:6'],
            'retype_password'   =>  ['required', 'same:password'],
        ]);


        $data = Admin::where('token', $request->token)->where('email', $request->email)->first();

        $data->password = Hash::make($request->password);
        $data->token = '';
        $data->update();


        return redirect()->route('admin.login')->with('success', 'Password reset succeddfully!!');
    }
}
