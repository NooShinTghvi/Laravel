<div style="background-color: #a0aec0" class="p-3">
    <div class="row">
        <div class="col-12">
            <h2>Groups</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="row">
                @for ($i = 0; $i < sizeof($groups); $i++)
                    <div class="col-8">{{$groups[$i]['name']}} </div>
                    <div class="col-4"><a href="{{route('group.edit',$groups[$i]['id'])}}">Edit</a></div>
                @endfor
            </div>
        </div>
    </div>
</div>
