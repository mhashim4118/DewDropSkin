@extends($activeTemplate.'layouts.app')
@php
$registerSectionContent=getContent('register.content',true);
$privacyAndPolicyContents = getContent('policy_pages.element');
@endphp
@section('content')



    <!-- Account Section Starts Here -->
    <section class="account-section">
        <div class="row g-0">
            <div class="col-md-6 col-xl-7 col-lg-6">
                <div class="account-thumb">
                    <img  src="{{getImage('assets/images/frontend/register/'.@$registerSectionContent->data_values->register_image,'1100x750')}}" alt="thumb">
                    <div class="account-thumb-content">
                        <p class="welc">{{ __(@$registerSectionContent->data_values->title) }}</p>
                        <h3 class="title">{{ __(@$registerSectionContent->data_values->heading) }}</h3>
                        <p class="info">{{ __(@$registerSectionContent->data_values->sub_heading) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-5 col-lg-6 ">
                <div class="account-form-wrapper">
                     <div class="logo"><a href="{{ route('home') }}"><img src="{{getImage(imagePath()['logoIcon']['path'] .'/light_logo.png')}}" alt="logo"></a></div>
                     <form class="account-form" method="POST" action="{{route('user.register')}}" onsubmit="return submitUserForm();">
                      @csrf
                        <div class="row">
                            @if ($refUser == null)
                             <div class="col-md-6 ">
                                <div class="form--group ">
                                    <label for="ref_name" class="form-label">@lang('Referral Username')<span>*</span></label>
                                    <input type="text" name="referral" class="referral form-control form--control"
                                           value="{{old('referral')}}" id="ref_name"
                                           placeholder="@lang('Enter referral username')*" required>
                                    <div id="ref"></div>
                                    <span id="referral"></span>
                                </div>
                            </div>
                            <div class="col-md-6 ">
                                <div class="form--group">
                                    <label for="position" class="form-label">@lang('Position')<span>*</span></label>
                                    <select name="position"  id="position" class="position form-control form--control mt-2"  >
                                        <option value="">@lang('Select position')*</option>
                                        @foreach(mlmPositions() as $k=> $v)
                                            <option value="{{$k}}">@lang($v)</option>
                                        @endforeach
                                    </select>
                                    <span id="position-test"><span
                                    class="text-danger"></span></span>
                                </div>
                            </div>
                        @else
                            <div class="col-md-6 ">
                                <div class="form--group">
                                    <label for="ref_name" class="form-label">@lang('Referral Username')<span>*</span></label>
                                    <input type="text" name="referral" class="referral form-control form--control"value="{{$refUser->username}}" placeholder="@lang('Enter referral username')*" id="ref_name" required
                                           readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form--group ">
                                    <label for="position" class="form-label">@lang('Position')<span>*</span></label>
                                    <select class="position form-control form--control mt-2" id="position" required>
                                        <option value="">@lang('Select position')*</option>
                                        @foreach(mlmPositions() as $k=> $v)
                                            <option @if($position == $k) selected
                                                    @endif value="{{$k}}">@lang($v)</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="position" value="{{$position}}">
                                    @php echo $joining; @endphp
                                </div>
                            </div>
                        @endif
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form--group">
                                    <label for="fname" class="form-label">@lang('First Name')<span>*</span></label>
                                    <input id="fname" name="firstname" type="text" class="form-control form--control" required placeholder="Enter your First Name">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form--group">
                                    <label for="lname" class="form-label">@lang('Last Name')<span>*</span></label>
                                    <input name="lastname" id="lname" type="text" class="form-control form--control" required placeholder="Enter your Last Name">
                                </div>
                            </div>
                        </div>
						 
						 <div class="row">
							 <div class="col-lg-6">
								 <div class="form--group">
									 <label for="fathername" class="form-label">@lang('Father Name')<span>*</span></label>
									 <input id="fathername" type="text" class="form-control form--control" value="" placeholder="Enter your Father Name" name="fathername">
								 </div>
							 </div>

							 <div class="col-lg-6">
								 <div class="form--group">
									 <label for="cnicnumber" class="form-label">@lang('CNIC Number')<span>*</span></label>
									 <input data-inputmask="'mask': '99999-9999999-9'"  placeholder="XXXXX-XXXXXXX-X" id="cnicnumber" type="text" class="form-control form--control" value="" name="cnicnumber">
								 </div>
							 </div>
						 </div>
						 
						  



                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form--group">
                                    <label for="email" class="form-label">@lang('Email')<span>*</span></label>
                                    <input id="email" type="email" name="email" class="form-control form--control" required placeholder="Enter Username or Email">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form--group">
                                    <label for="username" class="form-label">@lang('Username')<span>*</span></label>
                                    <input id="username" type="text" class="form-control form--control" value="dds000{{($last_id+1)}}"  readonly name="username">
                                </div>
                            </div>
                        </div>						 

                        <div class="form--group mb-3">
                            <label class="form-label for="country">{{ __('Country') }}</label>
                            <select name="country" id="country" class="form-control form--control">
                                @foreach($countries as $key => $country)
                                    <option data-mobile_code="{{ $country->dial_code }}" value="{{ $country->country }}" data-code="{{ $key }}">{{ __($country->country) }}</option>
                                @endforeach
                            </select>
                        </div>
					
					<div class="row">
						<div class="col-lg-6">
							<div class="form--group">
								<label for="city" class="form-label">@lang('City')<span>*</span></label>
								<input id="city" type="text" class="form-control form--control" value="" name="city">
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form--group">
								<label for="state" class="form-label">@lang('State')<span>*</span></label>
								<select class="form-control form--control" type="text" id="state" value="" name="state" required>
                                        <option value="">@lang('Select state')*</option>
									    <option value="JK">Azad Kashmir</option>
                                        <option value="BA">Balochistan</option>
                                        <option value="TA">FATA</option>
                                        <option value="GB">Gilgit Baltistan</option>
                                        <option value="IS">Islamabad Capital Territory</option>
                                        <option value="KP">Khyber Pakhtunkhwa</option>
                                        <option value="PB">Punjab</option>
                                        <option value="SD">Sindh</option>
                                </select>
    
							</div>
						</div>
					</div>
					
					<div class="col-lg-12">
						<div class="form--group">
							<label for="address" class="form-label">@lang('Address')<span>*</span></label>
							<input id="address" type="text" class="form-control form--control" value="" placeholder="@lang('Enter your current address for receiving DewDropSkin products')" name="address">
						</div>
					</div>

                        <div class="form--group">
                            <label for="mobile" class="form-label">@lang('Phone') <span>*</span></label>
							<input type="hidden" name="country_code">
							 <div class="d-flex align-items-center">

                                +<input type="text" name="mobile_code" readonly style="width: 55px;  background:none; border:none">
                                <input type="number" class="form-control form--control" name="mobile"
                                placeholder="@lang('Phone Number Without Country Code')" id="mobile" required>
                            </div>
                        </div>

                      <div class="row">
                            <div class="col-lg-6">
                                <div class="form--group hover-input-popup">
                                    <label for="pass" class="form-label">@lang('Password')<span>*</span></label>
                                    <input  name="password" type="password" class="form-control form--control" required placeholder="Enter Password">
                                    @if($general->secure_password)
                                        <div class="input-popup">
                                            <p class="error lower">@lang('1 small letter minimum')</p>
                                            <p class="error capital">@lang('1 capital letter minimum')</p>
                                            <p class="error number">@lang('1 number minimum')</p>
                                            <p class="error special">@lang('1 special character minimum')</p>
                                            <p class="error minimum">@lang('6 character password')</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form--group">
                                    <label for="myInputTwo" class="form-label">@lang('Re-Password')<span>*</span></label>
                                    <input name="password_confirmation"  type="password" class="form-control form--control" required placeholder="Confirm Password">
                                </div>
                            </div>
                        </div>
                        <div class="form--group">
                            @php echo loadReCaptcha() @endphp
                        </div>

                        @include($activeTemplate.'partials.custom_captcha')


                        <div class="form-group">
                            <input style="width: 20px;"  id="agree" name="agree" type="checkbox">

                            <label for="agree">@lang('I agree with ')</label>

                            @if ($privacyAndPolicyContents != null && !empty($privacyAndPolicyContents))
                                @foreach ($privacyAndPolicyContents as $k => $privacyAndPolicyContent)
                                     <a href="#" class="text-primary"> {{ __(@$privacyAndPolicyContent->data_values->title) }} {{ @$privacyAndPolicyContents->count() != $k + 1 ? ',' : ''}}
                                     </a>
                                 @endforeach
                            @endif

                        </div>
                       <div class="form--group button-wrapper">
                            @if($is_active == 1)
                            <button style="display: block;" class="account--btn" type="submit">@lang('Create Account')</button>
                            @else
                            <button id="BTD" class="account--btn" disabled="true" type="submit">@lang('Create Account')</button>
                            @endif
                            <a href="{{route('user.login')}}" class="custom--btn"><span>@lang('Login Account')</span></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="shape shape3"><img src="{{asset($activeTemplateTrue.'images/shape/08.png')}}" alt="shape"></div>
        <div class="shape shape4"><img src="{{asset($activeTemplateTrue.'images/shape/waves.png')}}" alt="shape"></div>
    </section>
    <!-- Account Section Ends Here -->


@endsection
@push('script-lib')
<script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
@endpush
@push('script')
    <script>

        (function ($) {
            "use strict";
            var not_select_msg = $('#position-test').html();
            $(document).on('keyup', '#ref_name', function () {
                var ref_id = $('#ref_name').val();
                var token = "{{csrf_token()}}";
                $.ajax({
                    type: "POST",
                    url: "{{route('check.referral')}}",
                    data: {
                        'ref_id': ref_id,
                        '_token': token
                    },
                    success: function (data) {
                        if (data.success) {
                            $('select[name=position]').attr('disabled',false);
                            $('#position-test').text('');
                        } else {
                            $('select[name=position]').attr('disabled', true);
                            $('#position-test').html(not_select_msg);
                        }
                        $("#ref").html(data.msg);
                    }
                });
            });
            $(document).on('change', '#position', function () {
                updateHand();
            });

            @if($general->secure_password)
                $('input[name=password]').on('input',function(){
                    secure_password($(this));
                });
            @endif

            function updateHand() {
                var pos = $('#position').val();
                var referrer_id = $('#referrer_id').val();
                var token = "{{csrf_token()}}";
                $.ajax({
                    type: "POST",
                    url: "{{route('get.user.position')}}",
                    data: {
                        'referrer': referrer_id,
                        'position': pos,
                        '_token': token
                    },
                    success: function (data) {
                       if(!data.success){
                        document.getElementById("ref_name").focus()
                       }
                        $("#position-test").html(data.msg);
						if(data.is_active === 1) {
                            document.getElementById('BTD').removeAttribute('disabled');
                        } if(data.is_active === 0){
                            document.getElementById('BTD').setAttribute('disabled', true);
                        }
                    }
                });
            }

            @if($mobile_code)
            $(`option[data-code={{ $mobile_code }}]`).attr('selected','');
            @endif
            $('select[name=country]').change(function(){
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
                $('.mobile-code').text('+'+$('select[name=country] :selected').data('mobile_code'));
            });
            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
            $('.mobile-code').text('+'+$('select[name=country] :selected').data('mobile_code'));

            function submitUserForm() {
                var response = grecaptcha.getResponse();
                if (response.length == 0) {
                    document.getElementById('g-recaptcha-error').innerHTML = '<span style="color:red;">@lang("Captcha field is required.")</span>';
                    return false;
                }
                return true;
            }

            function verifyCaptcha() {
                document.getElementById('g-recaptcha-error').innerHTML = '';
            }


            @if(old('position'))
            $(`select[name=position]`).val('{{ old('position') }}');
            @endif

        })(jQuery);

    $(":input").inputmask();



    </script>



@endpush