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
            <div id="result_danger" style="display:none" class="alert alert-warning" role="alert"></div>
            {{-- Show the success of recording changes --}}
            <div id="result_success" style="display:none" class="alert alert-success" role="alert"></div>
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
        {{-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * --}}
</div>
