@extends($activeTemplate.'layouts.master')

@section('content')

    <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title font-weight-normal">@lang('Balance Transfer')</h4>
                    </div>
                    <form class="contact-form"  method="POST" action="{{route('user.balance.transfer.post')}}">
                        @csrf
                        <div class="card-body">
                            <div class="col-md-12 text-center">
                                <div class="alert block-none alert-danger p-2" role="alert">
                                   <!-- <strong>@lang('Balance Transfer Charge') {{getAmount($general->bal_trans_fixed_charge)}} {{__($general->cur_text)}} @lang('Fixed and')  {{getAmount($general->bal_trans_per_charge)}}
                                        % @lang('of your total amount to transfer balance.')</strong> -->
									<strong>@lang('You can only transfer your balance to DewDropSkin Store.')</strong>
                                    <p id="after-balance"></p>
                                </div>
                            </div> 
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                      <label>@lang('DewDropSkin Store Username / Email To Send Amount')  <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form--control" id="username" name="username"
                                           placeholder="@lang('username / email')" required autocomplete="off">
                                    <span id="position-test"></span>
                                </div>
                                <div class="form-group col-md-12" style="display:none" id="fullName">
                                      <label>@lang('Beneficiary Name') </label>
                                    <input type="text" class="form-control form--control" id="fullname" name="fullname"
    readonly required autocomplete="off"
    style="color: green; background-color: transparent; font-weight: bold;">

                                    <span id="position-test"></span>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="InputMail">@lang('Transfer Amount')<span class="requred">*</span></label>
                                    <input onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')"  class="form-control form--control" autocomplete="off" id="amount" name="amount" placeholder="@lang('Amount') {{__($general->cur_text)}}" required>
                                    <span id="balance-message"></span>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" disabled id="sendBtn" class="btn btn--base w-100">@lang('Transfer Balance')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

@endsection
@push('script')

    <script>
        'use strict';
        (function($){
            $(document).on('focusout', '#username', function () {
                var username = $('#username').val();
                var token = "{{csrf_token()}}";
                if(username){
                    $.ajax({
                        type: "POST",
                        url: "{{route('user.search.user')}}",
                        data: {
                            'username': username,
                            '_token': token
                        },
                        success: function (data) {
                            if (data.success) {
                            
                              
                                $('#fullname').val(data.fullname);
                                $('#fullName').show();
                                $('#err').hide();
                                $('#sendBtn').removeAttr('disabled')
                            
                            
                            } else {
                                $('#position-test').html('<div id="err" class="text--danger mt-2">@lang("User not found")</div>');
                                $('#sendBtn').attr('disabled')
                                $('#fullName').hide();
                            }
                        }
                    });
                }else{
                    $('#position-test').html('');
                }
            });
            $(document).on('keyup', '#amount', function () {
                var amount = parseFloat($('#amount').val()) ;
                var balance = parseFloat("{{Auth::user()->balance+0}}");
                var fixed_charge = parseFloat("{{$general->bal_trans_fixed_charge+0}}");
                var percent_charge = parseFloat("{{$general->bal_trans_per_charge+0}}");
                var percent = (amount * percent_charge) / 100;
                var with_charge = amount+fixed_charge+percent;
                if(with_charge > balance)
                {
                    $('#after-balance').html('<p  class="text-danger">' + with_charge  + ' {{$general->cur_text}} ' + ' {{__('will be subtracted from your balance')}}' + '</p>');
                    $('#balance-message').html('<small class="text-danger">Insufficient Balance!</small>');
                } else if (with_charge <= balance) {
                    $('#after-balance').html('<p class="text-danger">' + with_charge  + ' {{$general->cur_text}} ' + ' {{__('will be subtracted from your balance')}}' + '</p>');
                    $('#balance-message').html('');
                }
            });
        })(jQuery)
    </script>

@endpush