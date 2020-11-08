<div>
    <div class="row">
        <div class="col-2"></div>
        <div class="col-8">
            <form id="form2" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input id="first_name" name="first_name" type="text" class="form-control" autofocus>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input id="last_name" name="last_name" type="text" class="form-control" autofocus>
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input id="phone" name="phone" type="text" class="form-control" placeholder="09123456789" autofocus>
                </div>
                <div class="form-group">
                    <label for="image">Image</label>
                    <input id="image" name="image" type="file" autofocus>
                </div>
                <input id="confirm_btn2" type="button" name="send" value="Submit" class="btn btn-dark btn-block">
            </form>
        </div>
        <div class="col-2"></div>
    </div>
    <div class="row">
        <div class="col-2"></div>
        <div class="col-8 pt-5">
            {{-- Display Error types in inputs --}}
            <div id="result_danger2" style="display:none" class="alert alert-warning" role="alert"></div>
            {{-- Show the success of recording changes --}}
            <div id="result_success2" style="display:none" class="alert alert-success" role="alert"></div>
        </div>
        <div class="col-2"></div>
    </div>
    {{-- * * * * * * * * * * * * * * * * AJAX * * * * * * * * * * * * * * * * --}}
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
                formData.append("first_name", $('#first_name').val());
                formData.append("last_name", $('#last_name').val());
                formData.append("phone", $('#phone').val());
                let files = $('#image')[0].files;
                formData.append("image", files[0]);
                $.ajax({
                    url: "{{ route('contact.create') }}",
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
    {{-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * --}}
</div>
