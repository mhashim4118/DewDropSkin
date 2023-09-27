@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @include($activeTemplate . 'layouts.breadcrumb')
    <section class="account-section padding-bottom padding-top">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div id="getData" class="card">
                        <div id="showMessage" style="display: none" class="card">
                            <div class="card-header text-center">
                                <h6 class="text-danger">@lang('User Not Found!')</h6>
                            </div>

                        </div>
                        <div class="card-header">
                            <h6>@lang('Find Email Address')</h6>
                        </div>
                        <div class="card-body">
                            <form>
                                @csrf
                                <div class="form-group hover-input-popup">
                                    <label for="cnic">@lang('CNIC Number')</label>
                                    <input type="text" id="cnic" data-inputmask="'mask': '99999-9999999-9'"
                                        placeholder="XXXXX-XXXXXXX-X" class="form-control form--control" name="cnic"
                                        required>

                                </div>

                                <div class="form-group">
                                    <button id="search_user" class="btn btn--base w-100">
                                        @lang('Find Email')
                                    </button>
                     
                                </div>


                            </form>
                        </div>
                    </div>

                    <div id="showData" style="display: none" class="card">
                        <div class="card-header">
                            <h6>@lang('Your Email Address and DDS Username')</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('user.password.update') }}">
                                @csrf
                                <div class="form-group hover-input-popup">
                                    <label>@lang('Email Address')</label>
                                    <input type="text" id="got_email" class="form-control form--control" readonly>

                                </div>
                                <div class="form-group hover-input-popup">
                                    <label>@lang('DDS Username')</label>
                                    <input type="text" id="got_username" class="form-control form--control" readonly>

                                </div>

                              


                            </form>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </section>
@endsection

@push('script-lib')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3/jquery.inputmask.bundle.js"></script>
@endpush
@push('script')
    <script>
        $(":input").inputmask();
    </script>
    <script>
        $(document).ready(function() {
            $("#search_user").click(function(e) {
                e.preventDefault();

                var cnic = $('#cnic').val();

                $.ajax({
                    type: 'post',
                    url: "{{ route('user.email.username') }}",

                    data: {
                        "_token": "{{ csrf_token() }}",
                        "cnic": cnic
                    },
                    success: function(response) {
                        console.log(response);
                        if (response == 1) {
                            $('#user_info_error').html('User Not Found');
                        } else {
                            $('#user_info_error').html('');
                            var email = response.email;
                            var username = response.username;
                            if (response.status == 200) {


                                $('#getData').hide();
                                $('#showData').show();
                                $('#got_email').val(email);
                                $('#got_username').val(username);
                            } else if (response.status == 404) {

                                $('#showMessage').show();
                            }

                        }
                    }
                });




            });
        });
    </script>
@endpush
