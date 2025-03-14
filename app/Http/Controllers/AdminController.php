<?php

namespace App\Http\Controllers;

use App\Models\GeneralSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use PDO;
use SawaStacks\Utils\Kropify;

class AdminController extends Controller
{
    public function AdminDashboard(Request $request)
    {
        $data = [
            'pageTitle' => 'Admin Dashboard'
        ];

        return view('back.pages.dashboard', $data);
    }

    public function logoutHandler(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login')->with('info', 'You are now logged out.');
    }

    public function profileView(Request $request)
    {
        $data = [
            'pageTitle' => 'Profile'
        ];
        return view('back.pages.profile', $data);
    }

    public function updateProfilePicture(Request $request)
    {
        $user = User::findOrFail(auth()->id());
        $path = 'images/users/';
        $file = $request->file('profilePictureFile');
        $old_picture = $user->getAttributes()['picture'];
        $filename = 'IMG_' . uniqid() . '.png';

        $upload = Kropify::getFile($file, $filename)->maxWoH(255)->save($path);


        if ($upload) {
            if ($old_picture != null && File::exists(public_path($path . $old_picture))) {
                File::delete(public_path($path . $old_picture));
            }
            $user->update(['picture' => $filename]);
            return response()->json(['status' => 1, 'message' => 'Successfully updated profile picture.']);
        } else {
            return response()->json(['status' => 0, 'message' => 'Something went wrong.']);
        }
    }

    public function generalSettings(Request $request)
    {
        $data = [
            'pageTitle' => 'General settings'
        ];
        return view('back.pages.general_settings', $data);
    }

    public function updateLogo(Request $request)
    {
        $settings = GeneralSetting::take(1)->first();

        if (!is_null($settings)) {
            $path = 'images/site/';
            $old_logo = $settings->site_logo;
            $file = $request->file('site_logo');
            $filename = 'logo_' . uniqid() . '.png';

            if ($request->hasFile('site_logo')) {
                $upload = $file->move(public_path($path), $filename);
                if ($upload) {
                    if ($old_logo != null && File::exists(public_path($path.$old_logo))) {
                        File::delete(public_path($path.$old_logo));
                    }
                    $settings->update(['site_logo' => $filename]);


                    return response()->json(['status' => 1, 'image_path' => $path . $filename, 'message' => 'Successfully updated website logo.']);
                } else {
                    return response()->json(['status' => 0, 'message' => 'Something went wrong.']);
                }
            }
        } else {
            return response()->json(['status' => 0, 'message' => 'Something went wrong.']);
        }
    }
}
