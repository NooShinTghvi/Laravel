<?php

namespace App\Http\Livewire;

use App\Http\Controllers\GroupController;
use Livewire\Component;

class Group extends Component
{
    public function render()
    {
        $groupController = new GroupController();
        return view('livewire.group', [
            'groups' => $groupController->get(),
        ]);
    }
}
