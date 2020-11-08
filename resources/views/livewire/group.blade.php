<div style="background-color: #a0aec0" class="p-3">
    <div class="row">
        <div class="col-12">
            <h2>Groups</h2>
        </div>
    </div>
    <div class="row pt-5 pb-2w">
        <div class="col-12">
            <form>
                <div class="form-group">
                    <label for="group-name">Find Group</label>
                    <input id="group-name" name="group-name" type="text" class="form-control" autofocus>
                </div>

                <input id="confirm_btn1" type="button" name="send" value="Submit" class="btn btn-dark btn-block">
            </form>
        </div>
    </div>
    <div class="row pt-5">
        @for ($i = 0; $i < sizeof($groups); $i++)
            <div class="col-3 pb-3">

            </div>
            <div class="col-3">{{$groups[$i]['name']}} </div>
            <div class="col-3"></div>
            <div class="col-3"><a href="{{route('group.edit',$groups[$i]['id'])}}">Edit</a></div>
        @endfor
    </div>
</div>
