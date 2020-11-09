<?php

namespace App\Http\Livewire;

use App\Http\Controllers\GroupController;
use Livewire\Component;

class ShowAllGroups extends Component
{
    public function render()
    {
        $groupController = new GroupController();
        return view('livewire.show-all-groups', [
            'groups' => $groupController->get(),
        ]);
    }
}
