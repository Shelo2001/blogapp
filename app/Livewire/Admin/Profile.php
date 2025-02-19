<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Http\Client\Request;
use Livewire\Component;

class Profile extends Component
{
    public $tab = null;
    public $tabname = "personal_details";
    protected $queryString = ['tab' => true];

    public $name, $email, $username, $bio;

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
