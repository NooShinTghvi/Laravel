<?php

namespace App\Http\Livewire;

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Contact extends Component
{
    public function render()
    {
        $contactController = new ContactController();
        return view('livewire.contact', [
            'contacts' => $contactController->getLegalCases(),
            'path' => Storage::url('contact/image/RNzkHJLxve9oKns.jpeg'),
        ]);
    }
}
