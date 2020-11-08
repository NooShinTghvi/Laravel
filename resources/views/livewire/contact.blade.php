<div style="background-color: #cbd5e0" class="p-3">
    <div class="row">
        <div class="col-12">
            <h2>Contacts</h2>
        </div>
    </div>
    <div class="row">
        @for ($i = 0; $i < sizeof($contacts); $i++)
                <div class="col-3 pb-3">
                    <img src="{{$contacts[$i]['image_path']}}" alt="" height="50" width="50">
                </div>
                <div class="col-3">{{$contacts[$i]['first_name']}} {{$contacts[$i]['last_name']}}</div>
                <div class="col-3">{{$contacts[$i]['phone']}}</div>
                <div class="col-3"><a href="{{route('contact.edit',$contacts[$i]['id'])}}">Edit</a></div>
        @endfor
    </div>
</div>
