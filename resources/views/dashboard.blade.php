<x-app-layout>
    <x-slot name="header">
        <ul class="nav nav-pills" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="pills-create-group-tab" data-toggle="pill" href="#pills-create-group"
                   role="tab" aria-controls="pills-create-group" aria-selected="true">Create Group</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pills-create-contact-tab" data-toggle="pill" href="#pills-create-contact"
                   role="tab" aria-controls="pills-create-contact" aria-selected="false">Create Contact</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pills-show-contacts-tab" data-toggle="pill" href="#pills-show-contacts"
                   role="tab" aria-controls="pills-show-contacts" aria-selected="false">Show Contacts</a>
            </li>
        </ul>
    </x-slot>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="pt-8">
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-create-group" role="tabpanel"
                             aria-labelledby="pills-home-tab">
                            @livewire('create-group')
                        </div>
                        <div class="tab-pane fade" id="pills-create-contact" role="tabpanel"
                             aria-labelledby="pills-create-contact-tab">
                            @livewire('create-contact')
                        </div>
                        <div class="tab-pane fade" id="pills-show-contacts" role="tabpanel"
                             aria-labelledby="pills-show-contacts-tab">
                            @livewire('show-contacts')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
