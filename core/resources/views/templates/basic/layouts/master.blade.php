<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title> {{ $general->sitename}} - {{__(@$pageTitle)}} </title>

    <link rel="shortcut icon" href="{{getImage(imagePath()['logoIcon']['path'] .'/favicon.png')}}" type="image/x-icon">
    @include('partials.seo')


    {{-- =====================template css=====================  --}}
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/animate.css')}}">
    <link rel="stylesheet" href="{{asset('assets/global/css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/global/css/line-awesome.min.css')}}">
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/flaticon.css')}}">
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/owl.css')}}">
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/nice-select.css')}}">
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/magnific-popup.css')}}">
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/main.css')}}">
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/custom.css')}}">
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/color.php')}}?color={{ $general->base_color }}&secondColor={{ $general->secondary_color }}">
    {{-- =====================end template css=====================  --}}

    @stack('style-lib')
    @stack('style')
</head>

<body>
    @include($activeTemplate.'partials.header')
    @include($activeTemplate.'layouts.breadcrumb')

    @php
        $plan_id = auth()->user()->plan_id;

        use App\Models\UserExtra;
        $user_extras = UserExtra::where('user_id', auth()->id())->firstOrFail();

        $chk_min = 0;
        if ($user_extras->paid_left < $user_extras->paid_right) {
            $chk_min = $user_extras->paid_left;
        }else{
            $chk_min = $user_extras->paid_right;
        }

        $image = 'User-Rank.png';
        $rank = "User";

        if ($chk_min > 0 && $chk_min < 32){
            $image = 'Master-Rank.png';
            $rank = "Master";
        }else if ($chk_min > 31 && $chk_min < 64){
            $image = 'Director-Rank.png';
            $rank = "Director";
        }else if  ($chk_min > 63 && $chk_min < 256){
            $image = 'Executive-Rank.png';
            $rank = "Executive";
        }else if  ($chk_min > 255 && $chk_min < 1024){
            $image = 'Chief-Rank.png';
            $rank = "Chief";
        }else if ($chk_min > 1023 && $chk_min < 8192){
            $image = 'Royal Mentor-Rank.png';
            $rank = "Royal Mentor";
        }else if ($chk_min > 8191 && $chk_min < 16384){
            $image = 'Ambassador-Rank.png';
            $rank = "Ambassador";
        }else if ($chk_min > 16383 && $chk_min < 32768){
            $image = 'King-Rank.png';
            $rank = "King";
        }else if ($chk_min > 32767){
            $image = 'Emperor-Rank.png';
            $rank = "Emperor";
        }

    @endphp
    <section class="user-dashboard padding-top padding-bottom">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="dashboard-sidebar">
                        <div class="close-dashboard d-lg-none">
                            <i class="las la-times"></i>
                        </div>
                        <div class="dashboard-user">
                            <div class="user-thumb">
                                @if (auth()->user()->image)
    <img src="{{ getImage('assets/images/user/profile/'. auth()->user()->image, '350x300')}}" alt="dashboard">
@else
    <img src="{{ asset('assets/images/default.png') }}" alt="DewDropSkin">
@endif

                            </div>
                            <div class="user-content">
                                <span>@lang('WELCOME')</span>
                                <h5 class="name">{{ auth()->user()->getFullnameAttribute() }}</h5>
								
						<div class="name" style="
    color: #fff;
    padding: 10px;
    border-radius: 3px;
    width: 100%;										 
"><img src="{{ url('assets/images/user/rank/'.$image) }}" style="width: 80px; border-radius: 50%; z-index: 99;" alt="dashboard"><h5 style="font-size: 20px; font-weight: bold; background-color: #060367; color: #ffffff; border-radius: 3px; box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.5);">{{ $rank }}</h5>
</div>

                                <button class="btn btn-primary" onclick="location.href='{{ route('user.logout') }}'">@lang('Sign Out')</button>

                            </div>
                        </div>
                        <ul class="user-dashboard-tab">
                            <li>
								
                            <a class="{{menuActive('user.home')}}" href="{{route('user.home')}}"> <i class="fas fa-tachometer-alt"></i> @lang('Dashboard')</a>
                           
                            <li><a class="{{menuActive('user.my.records')}}" href="{{url('user/my-records')}}"><i class="fas fa-file-alt"></i> @lang('My Statement')</a></li>
                            
							
							

                            <li>
                                <a class="{{menuActive('user.plan.index')}}" href="{{route('user.plan.index')}}"><i class="fas fa-box"></i> @lang('DSP Box') </a>
                            </li>
                            <li>
                                <a class="{{menuActive('user.price-list')}}" href="{{route('user.price-list')}}"><i class="fas fa-money-bill"></i> @lang('Price List') </a>
                            </li>
							<li>
                                <a class="{{menuActive('user.stockist.index')}}" href="{{route('user.stockist.index')}}"><i class="fas fa-warehouse"></i> @lang('Stockist') </a>
                            </li>
                            <li>
                                <a class="{{menuActive('user.stockistlist')}}" href="{{route('user.stockistlist')}}"><i class="fas fa-info-circle"></i> @lang('Stockist Detail') </a>
                            </li>
							<li>
                                <a class="{{menuActive('user.membership')}}" href="{{ route('user.membership') }}"><i class="fas fa-user-check"></i> @lang('Seller Membership')</a>
                            </li>
							<li>
                               <a class="{{menuActive('user.shop-franchise')}}" href="{{ route('user.shops-franchises') }}"><i class="fas fa-store-alt"></i> @lang('Shops & Franchises')</a>
                            </li>

							 <li>
                                <a class="{{menuActive('user.rewards')}}" href="{{route('user.rewards')}}"><i class="fas fa-medal"></i> @lang('Ranks & Rewards')</a>
                            </li>

                            <li>
                                <a class="{{menuActive('user.lucky-draw')}}" href="{{route('user.lucky-draw')}}"><i class="fas fa-medal"></i> @lang('Lucky Draw PROMO')</a>
                            </li>
                            
                            <li>
                                <a class="{{menuActive('user.my.ref')}}" href="{{ route('user.my.ref') }}"><i class="fas fa-users"></i> @lang('My Referrals')</a>
                            </li>
                            <li>
                                <a class="{{menuActive('user.my.tree')}}" href="{{ route('user.my.tree') }}"><i class="fas fa-sitemap"></i> @lang('My Tree')</a>
                            </li>
                            <li>
                                <a href="{{ route('user.binary.summery') }}" class="{{menuActive('user.binary.summery')}}">
                                  <i class="fas fa-clipboard-list"></i>  @lang('My Summary')
                                </a>
                            </li>
                            
                         

                            <li>
                                <a href="https://dewdropskin.com/store"><i class="fas fa-store"></i> @lang('DDS Store')</a>
                            </li>
							<li>
                                <a href="{{ route('user.orders') }}" class="{{menuActive('user.orders')}}">
                                   <i class="fas fa-shopping-cart"></i> @lang('Shop Orders')
                                </a>
                            </li>
                            
                            <li>
                                <a href="{{ route('user.deposit') }}" class="{{menuActive('user.deposit')}}">
                                  <i class="fas fa-money-check"></i>  @lang('Deposit Now')
                                </a>
                            </li>
							<li>
                                <a href="{{ route('user.balance.transfer') }}" class="{{menuActive('user.balance.transfer')}}">
                                  <i class="fas fa-exchange-alt"></i>  @lang('Balance Transfer')
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('user.withdraw') }}" class="{{menuActive('user.withdraw')}}">
                                  <i class="fas fa-money-bill-wave"></i>  @lang('Withdraw Now')
                                </a>
                            </li>
                            
                            <li>
                                <a href="{{ route('ticket') }}" class="{{menuActive('ticket')}}">
                                  <i class="fas fa-ticket-alt"></i>  @lang('Support Ticket')
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('user.twofactor') }}" class="{{menuActive('user.twofactor')}}">
                                  <i class="fas fa-shield-alt"></i>  @lang('2FA Security')
                                </a>
                            </li>
                            <li>
                                <a class="{{menuActive('user.profile.setting')}}" href="{{route('user.profile.setting')}}" class=""><i class="fas fa-user-cog"></i> @lang('Profile Setting')</a>
                            </li>
                            <li>
                                <a class="{{menuActive('user.change.password')}}" href="{{route('user.change.password')}}" class=""><i class="fas fa-lock"></i> @lang('Change Password')</a>
                            </li>
                            
                        </ul>
                    </div>
                </div>
                <div class="col-lg-9">

                    <div class="user-toggler-wrapper d-flex d-lg-none">
                        <h4 class="title">{{ __($pageTitle) }}</h4>
                        <div class="user-toggler">
                            <i class="las la-sliders-h"></i>
                        </div>
                    </div>


                    @yield('content')
                </div>
            </div>
        </div>
        @stack('modal')
    </section>


   


    {{-- =========== template js ===============  --}}
    <a href="#0" class="scrollToTop active"><i class="las la-chevron-up"></i></a>
    <script src="{{asset('assets/global/js/jquery-3.6.0.min.js')}}"></script>
    <script src=" {{asset($activeTemplateTrue . 'js/bootstrap.min.js')}}"></script>
    <script src="{{asset($activeTemplateTrue . 'js/owl.min.js')}}"></script>
    <script src=" {{asset($activeTemplateTrue . 'js/nice-select.js')}}"></script>
    <script src=" {{asset($activeTemplateTrue . 'js/viewport.jquery.js')}}"></script>
    <script src="{{asset($activeTemplateTrue . 'js/magnific-popup.min.js')}}"></script>
	<script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3/jquery.inputmask.bundle.js"></script>
    <script src=" {{asset($activeTemplateTrue . 'js/isotope.min.js')}}"></script>
    <script src="{{asset($activeTemplateTrue . 'js/main.js')}}"></script>
    {{-- =========== template js ===============  --}}

    @stack('script-lib')
    @stack('script')

    @include('partials.notify')
    @include('partials.plugins')

    <script>
        $(".langSel").on("change", function() {
            window.location.href = "{{route('home')}}/change/"+$(this).val() ;
        });
		 $(":input").inputmask();
    </script>


</body>
</html>