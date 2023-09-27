@extends($activeTemplate.'layouts.master')

@push('style')
    <link href="{{asset('assets/global/css/tree.css')}}" rel="stylesheet">
@endpush

@section('content')

    <div class="card">
		 <form action="{{url('user/tree/search')}}" method="get" class="d-flex searchB" style=" margin: 20px auto">
        <input type="text" style="border-radius:0" name="username" placeholder="Search for tree..." class="form-control">
        <button class="btn btn-success" style="background-color: #060367 !important; border-radius:0px">Search</button>
    </form>
        <div class="row text-center justify-content-center llll">
            <!-- <div class="col"> -->
            <div class="w-1">
                @php echo showSingleUserinTree($tree['a']); @endphp
            </div>
        </div>
        <div class="row text-center justify-content-center llll">
            <!-- <div class="col"> -->
            <div class="w-2">
                @php echo showSingleUserinTree($tree['b']); @endphp
            </div>
            <!-- <div class="col"> -->
            <div class="w-2 ">
                @php echo showSingleUserinTree($tree['c']); @endphp
            </div>
        </div>
        <div class="row text-center justify-content-center llll">
            <!-- <div class="col"> -->
            <div class="w-4 ">
                @php echo showSingleUserinTree($tree['d']); @endphp
            </div>
            <!-- <div class="col"> -->
            <div class="w-4 ">
                @php echo showSingleUserinTree($tree['e']); @endphp
            </div>
            <!-- <div class="col"> -->
            <div class="w-4 ">
                @php echo showSingleUserinTree($tree['f']); @endphp
            </div>
            <!-- <div class="col"> -->
            <div class="w-4 ">
                @php echo showSingleUserinTree($tree['g']); @endphp
            </div>
            <!-- <div class="col"> -->

        </div>
        <div class="row text-center justify-content-center llll">
            <!-- <div class="col"> -->
            <div class="w-8">
                @php echo showSingleUserinTree($tree['h']); @endphp
            </div>
            <!-- <div class="col"> -->
            <div class="w-8">
                @php echo showSingleUserinTree($tree['i']); @endphp
            </div>
            <!-- <div class="col"> -->
            <div class="w-8">
                @php echo showSingleUserinTree($tree['j']); @endphp
            </div>
            <!-- <div class="col"> -->
            <div class="w-8">
                @php echo showSingleUserinTree($tree['k']); @endphp
            </div>
            <!-- <div class="col"> -->
            <div class="w-8">
                @php echo showSingleUserinTree($tree['l']); @endphp
            </div>
            <!-- <div class="col"> -->
            <div class="w-8">
                @php echo showSingleUserinTree($tree['m']); @endphp
            </div>
            <!-- <div class="col"> -->
            <div class="w-8">
                @php echo showSingleUserinTree($tree['n']); @endphp
            </div>
            <!-- <div class="col"> -->
            <div class="w-8">
                @php echo showSingleUserinTree($tree['o']); @endphp
            </div>


        </div>
    </div>

@push('modal')
<div class="modal fade user-details-modal-area" id="exampleModalCentern" tabindex="-1" role="dialog"
     aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">@lang('Buy DSP Plan')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="@lang('Close')">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="user-details-modal">
                    <div class="user-details-header " style="text-align: center; display: block;">
                        <h5>{{ auth()->user()->getFullnameAttribute() }}  ( {{$refUser->username}} )</h5>
                    </div>

                    <div class="user-details-header " >
                    <form class="account-form" method="POST" action="{{route('user.dspplan.purchase')}}" onsubmit="return submitUserForm();">
                      @csrf
                      <input type="hidden" id="referrer_id" value="{{$refUser->id}}" name="referrer_id">
                      <input type="hidden" id="pos_id" value="{{$referrer->id}}" name="pos_id">
						 <input type="hidden" name="referral" class="referral form-control form--control"value="{{$refUser->username}}" placeholder="@lang('Enter referral username')*" id="ref_name" required
                                           readonly>
						<input id="username" type="hidden" class="form-control form--control" value="dsp{{($last_id+1)}}"  readonly name="username">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form--group">
                                    <label for="sponsor" class="form-label">@lang('Sponsor Id')</label>
                                    <input id="sponsor" type="text" class="form-control form--control" value="" name="sponsor" >
                                </div>
                            </div>
							
							<div class="col-md-6">
                                <div class="form--group ">
                                    <label for="position" class="form-label">@lang('Position')<span>*</span></label>
                                    <select name="position" class="position form-control form--control mt-2" id="position" required>
                                        <option value="">@lang('Select position')*</option>
                                        @foreach(mlmPositions() as $k=> $v)
                                            <option @if($position == $k) selected
                                                    @endif value="{{$k}}">@lang($v)</option>
                                        @endforeach
                                    </select>
                                    <!-- <input type="hidden" name="position" value="{{$position}}"> -->
                                    <span id="position-test">
                                    @php echo $joining; @endphp
                                    </span>
                                </div>
                            </div> 
                        </div>
						
						   <div class="row">
                                <div id="conf" style="box-shadow: rgba(0, 0, 0, 0.3) 0px 19px 38px, rgba(0, 0, 0, 0.22) 0px 15px 12px;display:none !important;width:300px; background:#060367; padding:20px; display: block; margin:70px auto;">
                                    <h4 style="margin-bottom:30px; color:red" class="text-center text-white">Are you sure?</h4>
                                    <div  class="d-flex justify-content-center">
                                    <button style="border-radius:0px !important; background:#0D4AC7 !important">Yes</button>
                                    <p class="btn btn-danger" style="border-radius:0px !important;cursor: pointer; width:90px; margin-left:20px" onclick="no_fun()">No</>
                                    </div>
                                </div>
                            </div>

                        <div class="row">
                        </div>

                        <div class="row">
                            <div class="col-lg-6 mt-2">
                                <div class="form--group">
                                    <label for="transfer_type" class="form-label">@lang('Payment Method')<span>*</span></label>
									<div class="row">
										<div class="col-6">
											<input type="radio"  id="html" name="transfer_type" value="wallet" required>
											 <label for="html">Wallet</label>
										</div>
										<div class="col-6">
											<input type="radio" class="ml-2" id="css" name="transfer_type" value="epin">
											<label for="css">E-Pin</label>
										</div>
									</div>	
                                    <!--<input type="radio" id="css" name="transfer_type" value="epin_credit" required>
                                    <label for="css">E-Pin Credit</label>-->
                                    
                                </div>
                            </div>
							
                        </div>
						<div class="row">
							<div class="col-lg-12" style="display: none" id="e_pin_value">
                                    <div class="form--group">
                                        <label for="sponusername" class="form-label">@lang('Enter your 12 digit E-Pin')<span>*</span></label>
                                        <input id="epin" type="text" data-inputmask="'mask': '************'" placeholder="xxxxxxxxxxxx" class="form-control form--control" value="" name="epin">
                                    </div>
                             </div>
						</div>

                        <!-- <div class="form--group">
                            @php //echo loadReCaptcha() @endphp
                        </div> -->

                        <!-- include($activeTemplate.'partials.custom_captcha') -->
						<strong class="text--success d-block text-center">@lang('Available DSP username:') dsp{{($last_id+1)}}</strong>

                       <div class="form--group button-wrapper d-flex justify-content-center align-content-center">
                            <button class="account--btn"  id="BT" onclick="confirm()" type="button">@lang('Buy DSP Plan')</button>
                        </div>
                    </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endpush

@push('modal')
<div class="modal fade user-details-modal-area" id="exampleModalCenter" tabindex="-1" role="dialog"
     aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">@lang('User Details')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="@lang('Close')">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="user-details-modal">
                    <div class="user-details-header ">
                        <div class="thumb"><img src="#" alt="*" class="tree_image w-h-100-p"
                            ></div>
                        <div class="content">
                            <span class="username"></span><br>
                            <a class="user-name tree_url tree_name" href=""></a>
                            <span class="user-status tree_status"></span>
                            <span class="user-status tree_plan"></span> <br>
                            <span class="dsp_username"></span>
                        </div>
                    </div>
                    <div class="user-details-body text-center">

                       
                        <div class="row cbec_div">
                            <div class="col-md-7 ">
                                <h6 class="my-3">@lang('Current Balance'): <span class="cur_bal"></span></h6>
                            </div>
                            <div class="col-md-5 ">
                                <h6 class="my-3">@lang('E-Pin Credit'): <span class="epin_credit"></span></h6>
                            </div>
                        </div>
                        <h6 class="my-3">@lang('Referred By'): 
                            <span class="tree_ref"></span>
                            (<span class="tree_refUsername"></span>)
                            </h6>


                        <table class="table table-bordered">
                            <tr>
                                <th>&nbsp;</th>
                                <th>@lang('LEFT')</th>
                                <th>@lang('RIGHT')</th>
                            </tr>

                           
                            <tr>
                                <td>@lang('Free Member')</td>
                                <td><span class="lfree"></span></td>
                                <td><span class="rfree"></span></td>
                            </tr>

                            <tr>
                                <td>@lang('Paid Member')</td>
                                <td><span class="lpaid"></span></td>
                                <td><span class="rpaid"></span></td>
                            </tr>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endpush



@endsection

@push('script')
	
	<script>
    //popup confirmation
    function confirm(){
        document.getElementById('conf').style.display = 'block';
    }
    function no_fun () {
        //alert('hi');
       return location.reload();
    }
</script>
	
    <script>
        "use strict";
        (function ($) {
            $('.showDetails').on('click', function () {
                var modal = $('#exampleModalCenter');
                console.log($(this).data('refbyusername'));
                $('.cur_bal').text($(this).data('cur_bal'));
                $('.epin_credit').text($(this).data('epin_credit'));
                //$('.tree_name').text($(this).data('name'));
                if($(this).data('name') == ' '){
                    $('.tree_name').text($(this).data('refby')+' dsp');
                }else{
                    $('.tree_name').text($(this).data('name'));
                }

                $('.tree_url').attr({"href": $(this).data('treeurl')});
                $('.tree_status').text($(this).data('status'));
                
                $('.dsp_username').text($(this).data('dspun'));
                $('.username').text($(this).data('username'));
                $('.tree_plan').text($(this).data('plan'));
                $('.tree_image').attr({"src": $(this).data('image')});
                $('.user-details-header').removeClass('Paid');
                $('.user-details-header').removeClass('Free');
                $('.user-details-header').addClass($(this).data('status'));
                //$('.tree_ref').text($(this).data('refby'));
                
                if($(this).data('dspun') == ''){
                    $('.cbec_div').hide();
                }else{$('.cbec_div').show();}

                if($(this).data('refby') == ' '){
                    $('.tree_ref').text('dsp');
                }else{
                    $('.tree_ref').text($(this).data('refby'));
                    $('.tree_refUsername').text($(this).data('refbyusername'));
                }            

                $('.lbv').text($(this).data('lbv'));
                $('.rbv').text($(this).data('rbv'));
                $('.lpaid').text($(this).data('lpaid'));
                $('.rpaid').text($(this).data('rpaid'));
                $('.lfree').text($(this).data('lfree'));
                $('.rfree').text($(this).data('rfree'));
                $('#exampleModalCenter').modal('show');
            });

            $('.showDetailsnew').on('click', function () {
                var modal = $('#exampleModalCentern');

                $('.tree_name').text($(this).data('name'));
                $('.tree_url').attr({"href": $(this).data('treeurl')});
                $('.tree_status').text($(this).data('status'));
                $('.tree_plan').text($(this).data('plan'));
                $('.tree_image').attr({"src": $(this).data('image')});
                $('.user-details-header').removeClass('Paid');
                $('.user-details-header').removeClass('Free');
                $('.user-details-header').addClass($(this).data('status'));
                $('.tree_ref').text($(this).data('refby'));
                $('.lbv').text($(this).data('lbv'));
                $('.rbv').text($(this).data('rbv'));
                $('.lpaid').text($(this).data('lpaid'));
                $('.rpaid').text($(this).data('rpaid'));
                $('.lfree').text($(this).data('lfree'));
                $('.rfree').text($(this).data('rfree'));
                $('#exampleModalCentern').modal('show');
            });

            $(document).on('change', '#position', function () {
                $('#sponsor').val('');
                updateHand();
            });

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
                        $('#pos_id').val(data.sponid);
                    }
                });
            }

            $(document).on('change', '#sponsor', function () {
                updateHandn();
            });

            function updateHandn() {
                var pos = $('#position').val();
                var sponsor = $('#sponsor').val();
                // var referrer_id = $('#referrer_id').val();
                var token = "{{csrf_token()}}";
                $.ajax({
                    type: "POST",
                    url: "{{route('get.sponsor.position')}}",
                    data: {
                        'sponsor': sponsor,
                        'position': pos,
                        '_token': token
                    },
                    success: function (data) {
                       if(!data.success){
                        document.getElementById("position").focus()
                       }
                        $("#position-test").html(data.msg);
                        $('#pos_id').val(data.sponid);
                    }
                });
            }

        })(jQuery);
    </script>

@endpush
@push('breadcrumb-plugins')
    <form action="{{route('user.other.tree.search')}}" method="GET" class="form-inline float-right bg--white">
        <div class="input-group has_append">
            <input type="text" name="username" class="form-control form--control" placeholder="@lang('Search by username')">
            <button class="btn btn--success" type="submit"><i class="fa fa-search"></i></button>
        </div>
    </form>
@endpush

@push('script')

    <script>
        'use strict';
        function myFunction(id) {
            var copyText = document.getElementById(id);
            copyText.select();
            copyText.setSelectionRange(0, 99999)
            document.execCommand("copy");
            notify('success', 'Url copied successfully ' + copyText.value);
        }
		
		 $('#css').on('click', function(e) {
        var e_pin = $(this).val()
        var wallat = $('#html').val()
        $('#e_pin_value').css('display', 'block');
    })
    $('#html').on('click', function(e) {
        var e_pin = $(this).val()
        var wallat = $('#html').val()
        $('#e_pin_value').css('display', 'none');

    })
    </script>

@endpush