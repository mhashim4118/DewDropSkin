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
                            <div class="col-lg-12">
                                <div class="form--group">
                                    <label for="email" class="form-label">@lang('Email')<span>*</span></label>
                                    <input id="email" type="email" name="email" class="form-control form--control" required placeholder="Enter Email">
                                </div>
                            </div>
                          
                        </div>						 

                      
					
					<div class="row">
						<div class="col-lg-6">
    <div class="form--group">
        <label for="city" class="form-label">@lang('City')<span>*</span></label>
        <input id="city" type="text" class="form-control form--control" value="" name="city" list="cities">
        <datalist id="cities">
	<option value="Islamabad">Islamabad</option>
    <option value="Ahmed Nager Chatha">Ahmed Nager Chatha</option>
    <option value="Ahmadpur East">Ahmadpur East</option>
    <option value="Ali Khan Abad">Ali Khan Abad</option>
    <option value="Alipur">Alipur</option>
    <option value="Arifwala">Arifwala</option>
    <option value="Attock">Attock</option>
    <option value="Bhera">Bhera</option>
    <option value="Bhalwal">Bhalwal</option>
    <option value="Bahawalnagar">Bahawalnagar</option>
    <option value="Bahawalpur">Bahawalpur</option>
    <option value="Bhakkar">Bhakkar</option>
    <option value="Burewala">Burewala</option>
    <option value="Chillianwala">Chillianwala</option>
    <option value="Chakwal">Chakwal</option>
    <option value="Chichawatni">Chichawatni</option>
    <option value="Chiniot">Chiniot</option>
    <option value="Chishtian">Chishtian</option>
    <option value="Daska">Daska</option>
    <option value="Darya Khan">Darya Khan</option>
    <option value="Dera Ghazi Khan">Dera Ghazi Khan</option>
    <option value="Dhaular">Dhaular</option>
    <option value="Dina">Dina</option>
    <option value="Dinga">Dinga</option>
    <option value="Dipalpur">Dipalpur</option>
    <option value="Faisalabad">Faisalabad</option>
    <option value="Ferozewala">Ferozewala</option>
    <option value="Fateh Jhang">Fateh Jang</option>
    <option value="Ghakhar Mandi">Ghakhar Mandi</option>
    <option value="Gojra">Gojra</option>
    <option value="Gujranwala">Gujranwala</option>
    <option value="Gujrat">Gujrat</option>
    <option value="Gujar Khan">Gujar Khan</option>
    <option value="Hafizabad">Hafizabad</option>
    <option value="Haroonabad">Haroonabad</option>
    <option value="Hasilpur">Hasilpur</option>
    <option value="Haveli Lakha">Haveli Lakha</option>
    <option value="Jatoi">Jatoi</option>
    <option value="Jalalpur">Jalalpur</option>
    <option value="Jattan">Jattan</option>
    <option value="Jampur">Jampur</option>
    <option value="Jaranwala">Jaranwala</option>
    <option value="Jhang">Jhang</option>
    <option value="Jhelum">Jhelum</option>
    <option value="Kalabagh">Kalabagh</option>
    <option value="Karor Lal Esan">Karor Lal Esan</option>
    <option value="Kasur">Kasur</option>
    <option value="Kamalia">Kamalia</option>
    <option value="Kamoke">Kamoke</option>
    <option value="Khanewal">Khanewal</option>
    <option value="Khanpur">Khanpur</option>
    <option value="Kharian">Kharian</option>
    <option value="Khushab">Khushab</option>
    <option value="Kot Addu">Kot Addu</option>
    <option value="Jauharabad">Jauharabad</option>
    <option value="Lahore">Lahore</option>
    <option value="Lalamusa">Lalamusa</option>
    <option value="Layyah">Layyah</option>
    <option value="Liaquat Pur">Liaquat Pur</option>
    <option value="Lodhran">Lodhran</option>
    <option value="Malakwal">Malakwal</option>
    <option value="Mamoori">Mamoori</option>
    <option value="Mailsi">Mailsi</option>
    <option value="Mandi Bahauddin">Mandi Bahauddin</option>
    <option value="Mian Channu">Mian Channu</option>
    <option value="Mianwali">Mianwali</option>
    <option value="Multan">Multan</option>
    <option value="Murree">Murree</option>
    <option value="Muridke">Muridke</option>
    <option value="Mianwali Bangla">Mianwali Bangla</option>
    <option value="Muzaffargarh">Muzaffargarh</option>
    <option value="Narowal">Narowal</option>
    <option value="Nankana Sahib">Nankana Sahib</option>
    <option value="Okara">Okara</option>
    <option value="Renala Khurd">Renala Khurd</option>
    <option value="Pakpattan">Pakpattan</option>
    <option value="Pattoki">Pattoki</option>
    <option value="Pir Mahal">Pir Mahal</option>
    <option value="Qaimpur">Qaimpur</option>
    <option value="Qila Didar Singh">Qila Didar Singh</option>
    <option value="Rabwah">Rabwah</option>
    <option value="Raiwind">Raiwind</option>
    <option value="Rajanpur">Rajanpur</option>
    <option value="Rahim Yar Khan">Rahim Yar Khan</option>
    <option value="Rawalpindi">Rawalpindi</option>
    <option value="Sadiqabad">Sadiqabad</option>
    <option value="Safdarabad">Safdarabad</option>
    <option value="Sahiwal">Sahiwal</option>
    <option value="Sangla Hill">Sangla Hill</option>
    <option value="Sarai Alamgir">Sarai Alamgir</option>
    <option value="Sargodha">Sargodha</option>
    <option value="Shakargarh">Shakargarh</option>
    <option value="Sheikhupura">Sheikhupura</option>
    <option value="Sialkot">Sialkot</option>
    <option value="Sohawa">Sohawa</option>
    <option value="Soianwala">Soianwala</option>
    <option value="Siranwali">Siranwali</option>
    <option value="Talagang">Talagang</option>
    <option value="Taxila">Taxila</option>
    <option value="Toba Tek Singh">Toba Tek Singh</option>
    <option value="Vehari">Vehari</option>
    <option value="Wah Cantonment">Wah Cantonment</option>
    <option value="Wazirabad">Wazirabad</option>
    <option value="" disabled>Sindh Cities</option>
    <option value="Badin">Badin</option>
    <option value="Bhirkan">Bhirkan</option>
    <option value="Rajo Khanani">Rajo Khanani</option>
    <option value="Chak">Chak</option>
    <option value="Dadu">Dadu</option>
    <option value="Digri">Digri</option>
    <option value="Diplo">Diplo</option>
    <option value="Dokri">Dokri</option>
    <option value="Ghotki">Ghotki</option>
    <option value="Haala">Haala</option>
    <option value="Hyderabad">Hyderabad</option>
    <option value="Islamkot">Islamkot</option>
    <option value="Jacobabad">Jacobabad</option>
    <option value="Jamshoro">Jamshoro</option>
    <option value="Jungshahi">Jungshahi</option>
    <option value="Kandhkot">Kandhkot</option>
    <option value="Kandiaro">Kandiaro</option>
    <option value="Karachi">Karachi</option>
    <option value="Kashmore">Kashmore</option>
    <option value="Keti Bandar">Keti Bandar</option>
    <option value="Khairpur">Khairpur</option>
    <option value="Kotri">Kotri</option>
    <option value="Larkana">Larkana</option>
    <option value="Matiari">Matiari</option>
    <option value="Mehar">Mehar</option>
    <option value="Mirpur Khas">Mirpur Khas</option>
    <option value="Mithani">Mithani</option>
    <option value="Mithi">Mithi</option>
    <option value="Mehrabpur">Mehrabpur</option>
    <option value="Moro">Moro</option>
    <option value="Nagarparkar">Nagarparkar</option>
    <option value="Naudero">Naudero</option>
    <option value="Naushahro Feroze">Naushahro Feroze</option>
    <option value="Naushara">Naushara</option>
    <option value="Nawabshah">Nawabshah</option>
    <option value="Nazimabad">Nazimabad</option>
    <option value="Qambar">Qambar</option>
    <option value="Qasimabad">Qasimabad</option>
    <option value="Ranipur">Ranipur</option>
    <option value="Ratodero">Ratodero</option>
    <option value="Rohri">Rohri</option>
    <option value="Sakrand">Sakrand</option>
    <option value="Sanghar">Sanghar</option>
    <option value="Shahbandar">Shahbandar</option>
    <option value="Shahdadkot">Shahdadkot</option>
    <option value="Shahdadpur">Shahdadpur</option>
    <option value="Shahpur Chakar">Shahpur Chakar</option>
    <option value="Shikarpaur">Shikarpaur</option>
    <option value="Sukkur">Sukkur</option>
    <option value="Tangwani">Tangwani</option>
    <option value="Tando Adam Khan">Tando Adam Khan</option>
    <option value="Tando Allahyar">Tando Allahyar</option>
    <option value="Tando Muhammad Khan">Tando Muhammad Khan</option>
    <option value="Thatta">Thatta</option>
    <option value="Umerkot">Umerkot</option>
    <option value="Warah">Warah</option>
    <option value="" disabled>Khyber Cities</option>
    <option value="Abbottabad">Abbottabad</option>
    <option value="Adezai">Adezai</option>
    <option value="Alpuri">Alpuri</option>
    <option value="Akora Khattak">Akora Khattak</option>
    <option value="Ayubia">Ayubia</option>
    <option value="Banda Daud Shah">Banda Daud Shah</option>
    <option value="Bannu">Bannu</option>
    <option value="Batkhela">Batkhela</option>
    <option value="Battagram">Battagram</option>
    <option value="Birote">Birote</option>
    <option value="Chakdara">Chakdara</option>
    <option value="Charsadda">Charsadda</option>
    <option value="Chitral">Chitral</option>
    <option value="Daggar">Daggar</option>
    <option value="Dargai">Dargai</option>
    <option value="Darya Khan">Darya Khan</option>
    <option value="Dera Ismail Khan">Dera Ismail Khan</option>
    <option value="Doaba">Doaba</option>
    <option value="Dir">Dir</option>
    <option value="Drosh">Drosh</option>
    <option value="Hangu">Hangu</option>
    <option value="Haripur">Haripur</option>
    <option value="Karak">Karak</option>
    <option value="Kohat">Kohat</option>
    <option value="Kulachi">Kulachi</option>
    <option value="Lakki Marwat">Lakki Marwat</option>
    <option value="Latamber">Latamber</option>
    <option value="Madyan">Madyan</option>
    <option value="Mansehra">Mansehra</option>
    <option value="Mardan">Mardan</option>
    <option value="Mastuj">Mastuj</option>
    <option value="Mingora">Mingora</option>
    <option value="Nowshera">Nowshera</option>
    <option value="Paharpur">Paharpur</option>
    <option value="Pabbi">Pabbi</option>
    <option value="Peshawar">Peshawar</option>
    <option value="Saidu Sharif">Saidu Sharif</option>
    <option value="Shorkot">Shorkot</option>
    <option value="Shewa Adda">Shewa Adda</option>
    <option value="Swabi">Swabi</option>
    <option value="Swat">Swat</option>
    <option value="Tangi">Tangi</option>
    <option value="Tank">Tank</option>
    <option value="Thall">Thall</option>
    <option value="Timergara">Timergara</option>
    <option value="Tordher">Tordher</option>
    <option value="" disabled>Balochistan Cities</option>
    <option value="Awaran">Awaran</option>
    <option value="Barkhan">Barkhan</option>
    <option value="Chagai">Chagai</option>
    <option value="Dera Bugti">Dera Bugti</option>
    <option value="Gwadar">Gwadar</option>
    <option value="Harnai">Harnai</option>
    <option value="Jafarabad">Jafarabad</option>
    <option value="Jhal Magsi">Jhal Magsi</option>
    <option value="Kacchi">Kacchi</option>
    <option value="Kalat">Kalat</option>
    <option value="Kech">Kech</option>
    <option value="Kharan">Kharan</option>
    <option value="Khuzdar">Khuzdar</option>
    <option value="Killa Abdullah">Killa Abdullah</option>
    <option value="Killa Saifullah">Killa Saifullah</option>
    <option value="Kohlu">Kohlu</option>
    <option value="Lasbela">Lasbela</option>
    <option value="Lehri">Lehri</option>
    <option value="Loralai">Loralai</option>
    <option value="Mastung">Mastung</option>
    <option value="Musakhel">Musakhel</option>
    <option value="Nasirabad">Nasirabad</option>
    <option value="Nushki">Nushki</option>
    <option value="Panjgur">Panjgur</option>
    <option value="Pishin Valley">Pishin Valley</option>
    <option value="Quetta">Quetta</option>
    <option value="Sherani">Sherani</option>
    <option value="Sibi">Sibi</option>
    <option value="Sohbatpur">Sohbatpur</option>
    <option value="Washuk">Washuk</option>
    <option value="Zhob">Zhob</option>
    <option value="Ziarat">Ziarat</option>
    <!-- Add more options for other Pakistani cities as needed -->
</datalist>
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
        <SPan style="margin-right: 10px;">+92</SPan>
        <input type="text" class="form-control form--control ml-2" name="mobile" placeholder="@lang('Enter your phone number without country code')" id="mobile-number" required>
    </div>
</div>
                   <div class="row">
    <div class="col-lg-6">
        <div class="form--group hover-input-popup">
            <label for="pass" class="form-label">@lang('Password')<span>*</span></label>
            <div class="input-group">
                <input  name="password" type="password" class="form-control form--control" required placeholder="Enter Password">
                <button type="button" class="btn btn-outline-secondary toggle-password" data-toggle="tooltip" data-placement="top" title="Show/Hide Password">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
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
            <div class="input-group">
                <input name="password_confirmation" type="password" class="form-control form--control" required placeholder="Confirm Password">
                <button type="button" class="btn btn-outline-secondary toggle-password" data-toggle="tooltip" data-placement="top" title="Show/Hide Password">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
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
    <button class="account--btn full-width" type="submit" style="width: 100%;">@lang('Create Account')</button>
    <!-- <a href="{{route('user.login')}}" class="custom--btn"><span>@lang('Login Account')</span></a> -->
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
    $(function() {
        $('.toggle-password').click(function() {
            $(this).toggleClass('active');
            var input = $(this).parent().find('input');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                $(this).html('<i class="fas fa-eye-slash"></i>');
            } else {
                input.attr('type', 'password');
                $(this).html('<i class="fas fa-eye"></i>');
            }
        });
    });
</script>
<script>
$(document).ready(function() {
    $('#mobile-code').on('input', function() {
        $(this).val($(this).val().replace(/\D/g, ''));
    });
    $('#mobile-number').on('input', function() {
        $(this).val($(this).val().replace(/\D/g, ''));
    });
});
</script>

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
                    }
                });
            }

        
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