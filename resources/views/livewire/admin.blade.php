<div>
    <div class="row">
        <div class="col-1"></div>
        <div class="col-10">
            <p><b>Toggle access user:</b></p>
            <form>
                <table style="width:80%;">
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Status</th>
                    </tr>
                    @foreach($users as $user)
                        <tr>
                            <td>{{$user['first_name']}}</td>
                            <td>{{$user['last_name']}}</td>
                            <td>{{$user['email']}}</td>
                            <td><input type="checkbox" name="userAccess" id="C{{$user['id']}}" value="{{$user['id']}}">
                                <span id="{{$user['id']}}">{{$user['is_active'] ? 'Close access': 'Open access'}}</span>
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td>
                            <button id="confirm_btn" type="submit" class="btn btn-primary">Submit</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <div class="col-1"></div>
    </div>
    <div class="row">
        <div class="col-1"></div>
        <div class="col-4">
            {{-- Display Error types in inputs --}}
            <div id="result_danger" style="display:none" class="alert alert-warning" role="alert"></div>
            {{-- Show the success of recording changes --}}
            <div id="result_success" style="display:none" class="alert alert-success" role="alert"></div>
        </div>
        <div class="col-7"></div>
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
                let selectedUsers = [];
                $('input[name="userAccess"]:checked').each(function () {
                    selectedUsers.push(this.value);
                });
                $.ajax({
                    url: "{{route('admin.change.access')}}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        userAccess: selectedUsers,
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
                            $.each(result.data, function (key, value) {
                                $('#C' + value).prop("checked", false);
                                if ($('#' + value).text() === 'Close access') {
                                    $('#' + value).html('');
                                    $('#' + value).append('Open access');
                                } else {
                                    $('#' + value).html('');
                                    $('#' + value).append('Close access');
                                }
                            });

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
</div>
