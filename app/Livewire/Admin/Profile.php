<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;

class Profile extends Component
{
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
