@extends('admin.layouts.master')

@section('content')
    <div class="page-wrapper default-version">
        <div class="form-area bg_img" data-background="{{asset('assets/admin/images/1.jpg')}}">
            <div class="form-wrapper">
                <h4 class="logo-text mb-15">@lang('Welcome to') <strong>{{__($general->sitename)}}</strong></h4>
                <p>{{__($pageTitle)}} @lang('to')  {{__($general->sitename)}} @lang('dashboard')</p>
              <form action="{{ route('admin.login') }}" method="POST" class="cmn-form mt-30">
    @csrf
    <div class="form-group">
        <label for="email">@lang('Username')</label>
        <input type="text" name="username" class="form-control b-radius--capsule" id="username" value="{{ old('admin') }}" placeholder="@lang('Enter your username')">
        <i class="las la-user input-icon"></i>
    </div>
    <div class="form-group">
        <label for="pass">@lang('Password')</label>
        <div class="password-wrapper">
            <input type="password" name="password" class="form-control b-radius--capsule" id="pass" placeholder="@lang('Enter your password')">
            <i class="las la-lock input-icon"></i>
            <i class="las la-eye input-icon toggle-password" onclick="togglePassword(this)"></i>
        </div>
    </div>
    <div class="form-group d-flex justify-content-between align-items-center">
        <a href="{{ route('admin.password.reset') }}" class="text-muted text--small"><i class="las la-lock"></i>@lang('Forgot password?')</a>
    </div>
    <div class="form-group">
        <button type="submit" class="submit-btn mt-25 b-radius--capsule">@lang('Login') <i class="las la-sign-in-alt"></i></button>
    </div>
</form>



            </div>
        </div><!-- login-area end -->
    </div>
@endsection

@push('script')
    <script>
    function togglePassword(icon) {
        var input = document.getElementById("pass");
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        }
    }
</script>

@endpush