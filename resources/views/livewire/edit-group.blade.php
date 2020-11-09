<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Group</title>
    {{--BootStrap--}}
    <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet">
</head>
<body>
{{--JQuery--}}
<script src="{{asset('js/jquery-3.5.1.min.js')}}"></script>
{{--BootStrap--}}
<script src="{{asset('js/bootstrap.min.js')}}"></script>
<script src="{{asset('js/popper.min.js')}}"></script>
<div class="container-fluid">
    <div class="row">
        <div class="col-2" style="background-color: #a0aec0">
            <div id="allContactsInGroup">
                <h3 style="color: #1d2124">All contacts in groups:</h3>
                <div class="pt-3">
                    @foreach($contacts as $contact)
                        <spam id="{{$contact->id}}">{{$contact->first_name}} {{$contact->last_name}}</spam>
                        <br>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-10" style="background-color: #c6c9d6">
            <div class="row">
                <div class="col-8">
                    <h2>Details</h2>
                    <form>
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input id="name" name="name" type="text" class="form-control"
                                   value="{{$group['name']}}">
                        </div>
                        <button id="confirm_btn" type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
                <div class="col-4">
                    {{-- Display Error types in inputs --}}
                    <div id="result_danger" style="display:none" class="alert alert-warning" role="alert"></div>
                    {{-- Show the success of recording changes --}}
                    <div id="result_success" style="display:none" class="alert alert-success" role="alert"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-8 pt-5">
                    <h2>Delete contacts from group</h2>
                    <div class="row">
                        <div class="col-12">
                            <form>
                                <div class="form-group">
                                    <label for="formControlGroup">select contact</label>
                                    <select id="formControlGroup" class="form-control">
                                        <option value="0"> ----</option>
                                        @foreach($contacts as $contact)
                                            <option id="{{$contact->id}}"
                                                    value="{{$contact->id}}">{{$contact->first_name}} {{$contact->last_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button id="confirm_btn3" type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-4 pt-5">
                    {{-- Display Error types in inputs --}}
                    <div id="result_danger3" style="display:none" class="alert alert-warning" role="alert"></div>
                    {{-- Show the success of recording changes --}}
                    <div id="result_success3" style="display:none" class="alert alert-success" role="alert"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 pt-5"><h2>Delete</h2></div>
                <div class="col-12">
                    <form id="myForm" role="form" method="post" action="{{route('group.remove', $group['id'])}}">
                        @csrf
                        <p>R U sure?</p>
                        <button type="submit" class="btn btn-danger">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- * * * * * * * * * * * * * AJAX  Details * * * * * * * * * * * * * * --}}
<script>
    $(document).ready(function () {
        $('#confirm_btn').click(function (e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('group.save.changes', $group['id'])}}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    name: $('#name').val(),
                },
                success: function (result) {
                    if (result.status === "error") {
                        if ($('#result_success').is(":visible"))
                            $('#result_success').hide();
                        $('#result_danger').html('');
                        $.each(result.data, function (key, value) {
                            $('#result_danger').show();
                            $('#result_danger').append('<p>' + value + '</p>');
                        });
                    } else {
                        if ($('#result_danger').is(":visible"))
                            $('#result_danger').hide();
                        $('#result_success').show();
                        $('#result_success').html('Done 🥳 😍');
                    }
                    console.log(result);
                },
                error: function (error) {
                    if ($('#result_success').is(":visible"))
                        $('#result_success').hide();
                    $('#result_danger').show();
                    $('#result_danger').html('Sth is wrong 😣 😐');
                    console.log(error);
                }
            });
        });
    });
</script>

{{-- * * * * * * * * * * * AJAX  delete from group ** * * * * * * * * * * --}}
<script>
    $(document).ready(function () {
        $('#confirm_btn3').click(function (e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('group.delete', $group['id'])}}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    contact: $('#formControlGroup').val(),
                },
                success: function (result) {
                    if (result.status === "error") {
                        if ($('#result_success3').is(":visible"))
                            $('#result_success3').hide();
                        $('#result_danger3').html('');
                        $.each(result.data, function (key, value) {
                            $('#result_danger3').show();
                            $('#result_danger3').append('<p>' + value + '</p>');
                        });
                    } else {
                        if ($('#result_danger3').is(":visible"))
                            $('#result_danger3').hide();
                        $('#result_success3').show();
                        $('#result_success3').html('Done 🥳 😍');
                        $('#' + result.data[0]).remove();
                    }
                    console.log(result);
                },
                error: function (error) {
                    if ($('#result_success3').is(":visible"))
                        $('#result_success3').hide();
                    $('#result_danger3').show();
                    $('#result_danger3').html('Sth is wrong 😣 😐');
                    console.log(error);
                }
            });
        });
    });
</script>
</body>
</html>
