<?php

namespace App\Http\Livewire;

use App\Http\Controllers\ContactController;
use App\Http\Controllers\UserController;
use Livewire\Component;

class Admin extends Component
{
    public function render()
    {
        $userController = new UserController();
        return view('livewire.admin', [
            'users' => $userController->getUsers(),
        ]);
    }
}
