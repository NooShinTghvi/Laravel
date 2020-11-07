<div>
    <div class="row">
        <div class="col-2"></div>
        <div class="col-8">
            <form>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input id="name" name="name" type="text" class="form-control" value="{{ old('name') }}" autofocus>
                </div>

                <input id="confirm_btn" type="button" name="send" value="Submit" class="btn btn-dark btn-block">
            </form>
        </div>
        <div class="col-2"></div>
    </div>
    <div class="row">
        <div class="col-2"></div>
        <div class="col-8 pt-5">
            {{-- Display Error types in inputs --}}
            <div id="alert__danger" style="display:none">
                <div class="alert alert-warning" role="alert" id="alert__danger_ShowMsg">
                </div>
            </div>
            {{-- Show the success of recording changes --}}
            <div id="result_success" style="display:none">
                <div class="alert alert-success" role="alert">
                    Done 🥳 😍
                </div>
            </div>
            {{-- Displays the failure of record changes --}}
            <div id="result_danger" style="display:none">
                <div class="alert alert-danger" role="alert">
                    Sth is wrong 😣 😐
                </div>
            </div>
        </div>
        <div class="col-2"></div>
    </div>
    {{-- * * * * * * * * * * * * * * * * AJAX * * * * * * * * * * * * * * * * --}}
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
                    url: "{{ route('group.create') }}",
                    method: 'POST',
                    data: {
                        name: $('#name').val(),
                    },
                    success: function (result) {
                        if (result.status === "error") {
                            $.each(result.data, function (key, value) {
                                $('#alert__danger').show();
                                $('#alert__danger_ShowMsg').html('');
                                $('#alert__danger_ShowMsg').append('<p>' + value + '</p>');
                            });
                        } else {
                            if ($('#alert__danger').is(":visible"))
                                $('#alert__danger').remove();
                            $('#result_success').show();
                        }
                        // console.log(result);
                    },
                    error: function (error) {
                        $('#result_danger').show();
                        // console.log(error);
                    }
                });
            });
        });
    </script>
    {{-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * --}}
</div>
