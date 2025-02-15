<?php

namespace App\Http\Controllers;

use App\UserStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function loginForm(Request $request){
        $data = [
            'pageTitle' => 'Login'
        ];

        return view('back.pages.auth.login', $data);
    }

    public function forgotForm(Request $request){
        $data = [
            'pageTitle' => 'Forgot Password'
        ];

        return view('back.pages.auth.forgot', $data);
    }

    public function loginHandler(Request $request){
        $fieldType=filter_var($request->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if($fieldType=='email'){
            $request->validate([
                'login_id'=>'required|email|exists:users,email',
                'password'=>'required|min:6'
            ],[
                'login_id.required'=>"Enter your email or username",
                'login_id.email'=>"Invalid email address",
                'login_id.exists'=>'No account found for this email'
            ]);
        } else {
            $request->validate([
                'login_id'=>'required|exists:users,username',
                'password'=>'required|min:6'
            ],[
                'login_id.required'=>"Enter your email or username",
                'login_id.exists'=>'No account found for this username'
            ]);
        }

        $creds = array(
            $fieldType=>$request->login_id,
            'password'=>$request->password,
        );
    
        if(Auth::attempt($creds)){
            if(auth()->user()->status==UserStatus::Inactive){
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('admin.login')->with('fail', 'Your account is currently Inactive.');
            }
            if(auth()->user()->status==UserStatus::Pending){
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
}
