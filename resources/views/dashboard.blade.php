<x-app-layout>
    <x-slot name="header">
        <ul class="nav nav-pills" id="pills-tab" role="tablist">
            @if(!Auth::user()->is_admin)
                @if(Auth::user()->is_active)
                    <li class="nav-item">
                        <a class="nav-link active" id="pills-create-group-tab" data-toggle="pill"
                           href="#pills-create-group" role="tab" aria-controls="pills-create-group"
                           aria-selected="true">Create Group</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-create-contact-tab" data-toggle="pill"
                           href="#pills-create-contact" role="tab" aria-controls="pills-create-contact"
                           aria-selected="false">Create Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-show-contacts-tab" data-toggle="pill" href="#pills-show-contacts"
                           role="tab" aria-controls="pills-show-contacts" aria-selected="false">Show Contacts</a>
                    </li>
                @endif
                @if(!Auth::user()->is_active)
                    <p>Your access is limited</p>
                @endif
            @endif
            @if(Auth::user()->is_admin)
                @if(Auth::user()->is_active)
                    <li class="nav-item">
                        <a class="nav-link active" id="pills-admin-tab" data-toggle="pill"
                           href="#pills-admin" role="tab" aria-controls="pills-admin"
                           aria-selected="true">Contact</a>
                    </li>
                @endif
                @if(!Auth::user()->is_active)
                    <p>Your access is limited</p>
                @endif
            @endif
        </ul>
    </x-slot>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @if(!Auth::user()->is_admin)
                    @if(Auth::user()->is_active)
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
                                    @livewire('contact-tab')
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
                @if(Auth::user()->is_admin)
                    @if(Auth::user()->is_active)
                        <div class="pt-8">
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="pills-admin" role="tabpanel"
                                     aria-labelledby="pills-home-tab">
                                    @livewire('admin')
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
