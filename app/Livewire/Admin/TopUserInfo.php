<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;

class TopUserInfo extends Component
{
    public function render()
    {
        return view('livewire.admin.top-user-info',['user'=>User::findOrFail(auth()->id())]);
        
    }
}
