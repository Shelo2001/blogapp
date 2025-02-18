<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Http\Client\Request;
use Livewire\Component;

class Profile extends Component
{
    public $tab = null;
    public $tabname="personal_details";
    protected $queryString=['tab'=>true];

    public function selectTab($tab){
        $this->tab=$tab;
    }

    public function mount(){
        $this->tab=Request('tab') ? Request('tab') : $this->tabname;
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
