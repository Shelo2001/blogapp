<?php

namespace App\Http\Controllers;

use App\Helpers\CMail;
use App\UserStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    public function loginForm(Request $request)
    {
        $data = [
            'pageTitle' => 'Login'
        ];

        return view('back.pages.auth.login', $data);
    }

    public function forgotForm(Request $request)
    {
        $data = [
            'pageTitle' => 'Forgot Password'
        ];

        return view('back.pages.auth.forgot', $data);
    }

    public function loginHandler(Request $request)
    {
        $fieldType = filter_var($request->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if ($fieldType == 'email') {
            $request->validate([
                'login_id' => 'required|email|exists:users,email',
                'password' => 'required|min:6'
            ], [
                'login_id.required' => "Enter your email or username",
                'login_id.email' => "Invalid email address",
                'login_id.exists' => 'No account found for this email'
            ]);
        } else {
            $request->validate([
                'login_id' => 'required|exists:users,username',
                'password' => 'required|min:6'
            ], [
                'login_id.required' => "Enter your email or username",
                'login_id.exists' => 'No account found for this username'
            ]);
        }

        $creds = array(
            $fieldType => $request->login_id,
            'password' => $request->password,
        );

        if (Auth::attempt($creds)) {
            if (auth()->user()->status == UserStatus::Inactive) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('admin.login')->with('fail', 'Your account is currently Inactive.');
            }
            if (auth()->user()->status == UserStatus::Pending) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('admin.login')->with('fail', 'Your account is currently pending.');
            }
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('admin.login')->withInput()->with('fail', 'Incorrect Password.');
        }
    }

    public function sendPasswordResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => "Enter your email",
            'email.email' => "Invalid email address",
            'email.exists' => 'No account found for this email'
        ]);

        $user = User::where('email', $request->email)->first();

        $token = base64_encode(Str::random(64));

        $oldToken = DB::table('password_reset_tokens')->where('email', $user->email)->first();

        if ($oldToken) {
            $oldToken = DB::table('password_reset_tokens')->where('email', $user->email)->update([
                'token' => $token,
                'created_at' => Carbon::now()
            ]);
        } else {
            $oldToken = DB::table('password_reset_tokens')->insert([
                'email' => $user->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);
        }

        $actionLink = route('admin.reset_password_form', ['token' => $token]);
        $data = array(
            'actionLink' => $actionLink,
            'user' => $user
        );

        $mail_body = view('email-templates.forgot-template', $data)->render();

        $mailConfig = array(
            'recepient_address' => $user->email,
            'recepient_name' => $user->name,
            'subject' => 'Reset Password',
            'body' => $mail_body,
        );
        if (CMail::send($mailConfig)) {
            return redirect()->route('admin.forgot')->with('success', 'We have emailed your password reset link.');
        } else {
            return redirect()->route('admin.forgot')->with('fail', 'Something went wrong. Try again later.');
        }
    }

    public function resetForm(Request $request, $token = null)
    {
        $isTokenExists = DB::table('password_reset_tokens')->where('token', $token)->first();
        if (!$isTokenExists) {
            return redirect()->route('admin.forgot')->with('fail', 'Invalid token.');
        } else {

            $diffMins=Carbon::createFromFormat('Y-m-d H:i:s',$isTokenExists->created_at)->diffInMinutes(Carbon::now());

            if($diffMins>15){
                return redirect()->route('admin.forgot')->with('fail', 'Password reset token has expired. Please request a new link.');
            }

            $data = [
                'pageTitle' => 'Reset password',
                'token' => $token
            ];

            return view('back.pages.auth.reset', $data);
        }
    }

    public function resetPasswordHandler(Request $request)
    {
        $request->validate([
            'new_password' => 'required|min:6|required_with:new_password_confirmation|same:new_password_confirmation',
            'new_password_confirmation' => 'required'
        ]);

        $dbToken = DB::table('password_reset_tokens')->where('token', $request->token)->first();

        $user = User::where('email', $dbToken->email)->first();

        User::where('email', $user->email)->update([
            'password' => Hash::make($request->new_password)
        ]);

        $data = array(
            'user' => $user,
            "new_password" => $request->new_password
        );

        $mail_body=view('email-templates.password-changes-template',$data)->render();

        $mailConfig=array(
            'recepient_address'=>$user->email,
            'recepient_name'=>$user->name,
            'subject'=>'password changed',
            'body'=>$mail_body
        );

        if (CMail::send($mailConfig)) {
            DB::table('password_reset_tokens')->where([
                'email'=>$dbToken->email,
                'token'=>$dbToken->token
            ])->delete();
            return redirect()->route('admin.login')->with('success', 'Your password has been changed successfully.');
        } else {
            return redirect()->route('admin.reset')->with('fail', 'Something went wrong. Try again later.');
        }

    }
}
