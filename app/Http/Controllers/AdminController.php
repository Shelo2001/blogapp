<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use SawaStacks\Utils\Kropify;

class AdminController extends Controller
{
    public function AdminDashboard(Request $request){
        $data = [
            'pageTitle' => 'Admin Dashboard'
        ];

        return view('back.pages.dashboard', $data);
    }

    public function logoutHandler(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login')->with('info', 'You are now logged out.');
    }

    public function profileView(Request $request){
        $data = [
            'pageTitle'=>'Profile'
        ];
        return view('back.pages.profile',$data);
    }

    public function updateProfilePicture(Request $request){
        $user = User::findOrFail(auth()->id());
        $path = 'images/users/';
        $file = $request->file('profilePictureFile');
        $old_picture=$user->getAttributes()['picture'];
        $filename='IMG_'.uniqid().'.png';

        $upload = Kropify::getFile($file, $filename)->maxWoH(255)->save($path);
        

        if($upload) {
            if($old_picture!=null && File::exists(public_path($path.$old_picture))){
                File::delete(public_path($path.$old_picture));
            }
            $user->update(['picture'=>$filename]);
            return response()->json(['status'=>1,'message'=>'Successfully updated profile picture.']);
        } else {
            return response()->json(['status'=>0,'message'=>'Something went wrong.']);
        }
    }

    public function generalSettings(Request $request){
        $data =[
            'pageTitle'=>'General settings'
        ];
        return view('back.pages.general_settings',$data);
    }
}