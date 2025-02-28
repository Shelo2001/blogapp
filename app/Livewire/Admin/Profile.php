<?php

namespace App\Livewire\Admin;

use App\Helpers\CMail;
use App\Models\User;
use App\Models\UserSocialLinks;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Profile extends Component
{
    public $tab = null;
    public $tabname = "personal_details";
    protected $queryString = ['tab' => true];

    public $name, $email, $username, $bio;

    public $current_password, $new_password, $new_password_confirmation;

    public $facebook_url, $instagram_url, $linkedin_url, $github_url, $twitter_url, $youtube_url;

    protected $listeners = ['updateProfile' => '$refresh'];

    public function selectTab($tab)
    {
        $this->tab = $tab;
    }

    public function mount()
    {
        $this->tab = Request('tab') ? Request('tab') : $this->tabname;

        $user = User::with('social_links')->findOrFail(auth()->id());

        $this->name = $user->name;
        $this->email = $user->email;
        $this->username = $user->username;
        $this->bio = $user->bio;

        if (!is_null($user->social_links)) {
            $this->facebook_url = $user->social_links->facebook_url;
            $this->instagram_url = $user->social_links->instagram_url;
            $this->linkedin_url = $user->social_links->linkedin_url;
            $this->twitter_url = $user->social_links->twitter_url;
            $this->github_url = $user->social_links->github_url;
            $this->youtube_url = $user->social_links->youtube_url;
        }
    }

    public function updatePersonalDetails()
    {
        $user = User::findOrFail(auth()->id());

        $this->validate([
            'name' => 'required',
            'username' => 'required|unique:users,username,' . $user->id
        ]);

        $user->name = $this->name;
        $user->username = $this->username;
        $user->bio = $this->bio;

        $updated = $user->save();
        sleep(0.5);
        if ($updated) {
            toastr()->success('Data has been saved successfully!');
            $this->dispatch('updatedTopUserInfo')->to(TopUserInfo::class);
        } else {
            toastr()->error('An error has occurred please try again later.');
        }
    }

    public function updatePassword()
    {
        $user = User::findOrFail(auth()->id());

        $this->validate([
            'current_password' => [
                'required',
                'min:6',
                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        return $fail(__("your current password does not match"));
                    }
                }
            ],
            'new_password' => 'required|min:6|confirmed'
        ]);

        $updated = $user->update([
            'password' => Hash::make($this->new_password)
        ]);

        if ($updated) {
            $data = array('user' => $user, "new_password" => $this->new_password);

            $mail_body = view('email-templates.password-changes-template', $data)->render();

            $mail_config = array(
                'recepient_address' => $user->email,
                'recepient_name' => $user->name,
                'subject' => 'Password changed',
                'body' => $mail_body,
            );

            CMail::send($mail_config);

            auth()->logout();
            Session::flash('info', 'Password changed successfully. Please login with your new password');
            $this->redirectRoute('admin.login');
        } else {
            toastr()->error('An error has occurred please try again later.');
        }
    }

    public function updateSocialLinks()
    {
        $this->validate([
            'facebook_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'youtube_url' => 'nullable|url',
            'github_url' => 'nullable|url',
        ]);

        $user = User::findOrFail(auth()->id());

        $data = array(
            'facebook_url' => $this->facebook_url,
            'instagram_url' => $this->instagram_url,
            'linkedin_url' => $this->linkedin_url,
            'twitter_url' => $this->twitter_url,
            'youtube_url' => $this->youtube_url,
            'github_url' => $this->github_url,
        );

        if (!is_null($user->social_links)) {
            $query = $user->social_links()->update($data);
        } else {
            $data['user_id'] = $user->id;
            $query = UserSocialLinks::insert($data);
        }
        
        if ($query) {
            toastr()->success('Data has been saved successfully!');
        } else {
            toastr()->error('An error has occurred please try again later.');
        }
    }

    public function render()
    {

        return view(
            'livewire.admin.profile',
            [
                'user' => User::findOrFail(auth()->id())

            ]
        );
    }
}
