<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Contact</title>
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
            <div id="contact_sGroup"> {{--contact'sGroup--}}
                <h3>groups</h3>
                @foreach($contactInGroups as $group)
                    {{$group->name}} <br>
                @endforeach
            </div>
        </div>
        <div class="col-10" style="background-color: #c6c9d6">
            <div class="row">
                <div class="col-8">
                    <h2>Details</h2>
                    <form>
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input id="first_name" name="first_input" type="text" class="form-control"
                                   value="{{$contact['first_name']}}">
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input id="last_name" name="last_name" type="text" class="form-control"
                                   value="{{$contact['last_name']}}">
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input id="phone" name="phone" type="text" class="form-control"
                                   value="{{$contact['phone']}}">
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
                    <h2>Picture</h2>
                    <div class="row">
                        <div class="col-4">
                            <img id="my_image" src="{{$contact['image_path']}}" alt="" height="85" width="85">
                        </div>
                        <div class="col-8">
                            <form id="form2" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="image">Image</label>
                                    <input id="image" name="image" type="file" autofocus>
                                </div>
                                <button id="confirm_btn2" type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-4 pt-5">
                    {{-- Display Error types in inputs --}}
                    <div id="result_danger2" style="display:none" class="alert alert-warning" role="alert"></div>
                    {{-- Show the success of recording changes --}}
                    <div id="result_success2" style="display:none" class="alert alert-success" role="alert"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-8 pt-5">
                    <h2>Groups</h2>
                    <div class="row">
                        <div class="col-12">
                            <form>
                                <div class="form-group">
                                    <label for="formControlGroup">Add to new groups</label>
                                    <select id="formControlGroup" class="form-control">
                                        <option value="0"> ----</option>
                                        @for ($i = 0; $i < sizeof($groups); $i++)
                                            <option value="{{$groups[$i]->id}}">{{$groups[$i]->name}}</option>
                                        @endfor
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
                    <form id="myForm" role="form" method="post"
                          action="{{route('contact.remove', $contact['id'])}}">
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
                url: "{{route('contact.save.changes', $contact['id'])}}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    first_name: $('#first_name').val(),
                    last_name: $('#last_name').val(),
                    phone: $('#phone').val(),
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

{{-- * * * * * * * * * * * * AJAX Change Image * * * * * * * * * * * * * --}}
<script>
    $(document).ready(function () {
        $('#confirm_btn2').click(function (e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            let formData = new FormData();
            let files = $('#image')[0].files;
            formData.append("image", files[0]);
            formData.append("_token", "{{ csrf_token() }}");
            $.ajax({
                url: "{{ route('contact.change.image', $contact['id']) }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (result) {
                    if (result.status === "error") {
                        if ($('#result_success2').is(":visible"))
                            $('#result_success2').hide();
                        $('#result_danger2').html('');
                        $.each(result.data, function (key, value) {
                            $('#result_danger2').show();
                            $('#result_danger2').append('<p>' + value + '</p>');
                        });
                    } else {
                        if ($('#result_danger2').is(":visible"))
                            $('#result_danger2').hide();
                        $('#result_success2').show();
                        $('#result_success2').html('Done 🥳 😍');
                        $("#my_image").attr("src", result.data[0]);
                    }
                    console.log(result);
                },
                error: function (error) {
                    if ($('#result_success2').is(":visible"))
                        $('#result_success2').hide();
                    $('#result_danger2').show();
                    $('#result_danger2').html('Sth is wrong 😣 😐');
                    console.log(error);
                }
            });
        });
    });
</script>

{{-- * * * * * * * * * * * AJAX  add to new group ** * * * * * * * * * * --}}
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
                url: "{{route('group.add', $contact['id'])}}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    group: $('#formControlGroup').val(),
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
                        $('#contact_sGroup').append(result.data[0] + '<br>'); {{--contact'sGroup--}}
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
