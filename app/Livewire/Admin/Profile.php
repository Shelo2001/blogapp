<?php

namespace App\Livewire\Admin;

use App\Helpers\CMail;
use App\Models\User;
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

    public $current_password, $new_password,$new_password_confirmation;

    protected $listeners = ['updateProfile'=>'$refresh'];

    public function selectTab($tab)
    {
        $this->tab = $tab;
    }

    public function mount()
    {
        $this->tab = Request('tab') ? Request('tab') : $this->tabname;

        $user = User::findOrFail(auth()->id());

        $this->name = $user->name;
        $this->email = $user->email;
        $this->username = $user->username;
        $this->bio = $user->bio;
    }

    public function updatePersonalDetails(){
        $user=User::findOrFail(auth()->id());

        $this->validate([
            'name' => 'required',
            'username' => 'required|unique:users,username,'.$user->id
        ]);

        $user->name = $this->name;
        $user->username = $this->username;
        $user->bio = $this->bio;
    
        $updated = $user->save(); 
        sleep(0.5);
        if($updated){
            toastr()->success('Data has been saved successfully!');
            $this->dispatch('updatedTopUserInfo')->to(TopUserInfo::class);
        } else{
            toastr()->error('An error has occurred please try again later.');
        }
    }

    public function updatePassword(){
        $user = User::findOrFail(auth()->id());

        $this->validate([
            'current_password'=>[
                'required', 'min:6', function($attribute, $value, $fail) use ($user){
                    if(!Hash::check($value, $user->password)){
                        return $fail(__("your current password does not match"));
                    }
                }
            ],
            'new_password'=>'required|min:6|confirmed'
        ]);

        $updated= $user->update([
            'password'=>Hash::make($this->new_password)
        ]);

        if($updated){
            $data=array('user'=>$user, "new_password"=>$this->new_password);

            $mail_body=view('email-templates.password-changes-template',$data)->render();

            $mail_config=array(
                'recepient_address' => $user->email,
                'recepient_name' => $user->name,
                'subject' => 'Password changed',
                'body' => $mail_body,
            );

            CMail::send($mail_config);

            auth()->logout();
            Session::flash('info','Password changed successfully. Please login with your new password');
            $this->redirectRoute('admin.login');
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
