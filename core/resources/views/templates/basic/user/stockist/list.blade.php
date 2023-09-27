@extends($activeTemplate.'layouts.master')

@section('content')
<?php

use App\Models\User;  ?>
<div class="row">
    <div class="mb-3" style="text-align:right ;">
        <a href="#" class="cmn--btn active __subscribe" ><span>@lang('Buy E-Pins')</span></a>
    </div>
</div>
<div class="row">
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('Total E-Pins')</h6>
                    <h3 class="ammount theme-one">{{ $totalPin }}</h3>
                </div>
                <div class="icon"><i class="flaticon-fees"></i></div>
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('In Stock E-Pins')</h6>
                    <h3 class="ammount theme-one">{{ $stockPin }}</h3>
                </div>
                <div class="icon"><i class="flaticon-fees"></i></div>
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('Used E-Pins')</h6>
                    <h3 class="ammount theme-one">{{ $usedPin }} </h3>
                </div>
                <div class="icon"><i class="flaticon-fees"></i></div>
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('E-Pins Earning')</h6>
                    <h3 class="ammount theme-one">{{getAmount(auth()->user()->stockist_bonus) }} PKR</h3>
                </div>
                <div class="icon"><i class="flaticon-fees"></i></div>
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive--sm">
                    <table class="transection-table-2">
                        <thead>
                            <tr>
								<th scope="col">@lang('Sl No.')</th>
								<th scope="col">@lang('Used By')</th>
                                <th scope="col">@lang('Price')</th>
                                <th scope="col">@lang('E-Pin')</th>
                               <!-- <th scope="col">@lang('Detail')</th>-->
                                <th scope="col">@lang('Status')</th>
								<th scope="col">@lang('Payment Method')</th>
								<th scope="col"> @lang('Date') </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $payment_methord = array(
                                    'bank_account'=>'Current Balance',
                                     'epin_credit' => 'E_Pin',
                                     'pv' => 'PV',   
                                    )
                            ?>
                            @php($count = 1)
                            @foreach($stocklist as $key=>$list)
                            <tr  onclick="myFunction(<?php echo $list->id; ?>)">
								<td>{{$stocklist->total() - ($stocklist->firstItem() + $key) + 1 }}</td>
								<td>
                                    @if($list->used_at==0)
                                        <span class="badge badge-secondary" style="background-color:darkcyan;">
                                            Not Used
                                        </span>
                                    @else 
                                        <span class="badge badge-secondary" style="background-color:darkred;">
                                            <?php 
                                            $user =  User::find($list->used_at);
                                            if($user->firstname != null){
                                                echo $user->firstname . " " . $user->lastname . " (" . $user->username . ")";
                                            } else {
                                                $main_user = User::where('id', $user->ref_id)->first();
                                                echo $main_user->firstname . " " . $main_user->lastname . "'s (" . $user->username . ")";
                                            }
                                            ?>
                                        </span>
                                    @endif 
                                </td>
                                
                                <td>{{ number_format($plan[0]->price,2, '.', '')}}</td>
                                <td> <span id="e_pin_{{$list->id}}">************</span> <button id="btnShow" e_pinId="{{ $list->id}}" class="btn btn-success btn-sm"> <span>@lang('show')</span></button></td>
                                
                                <td>@if( $list->used==1 ) <span class="badge badge-secondary" style="background-color:darkred;"> Used</span>@else <span class="badge badge-secondary" style="background-color:darkcyan;">Not Used</span> @endif </td>
								<td> {{ $payment_methord[$list->payment_type]}} </td>
								<td> <?php   echo date('d-M-Y', strtotime($list->created_at));   ?></td>
								
                            </tr>
							 <tr id="extra-details<?php echo $list->id; ?>" class="text-center extra-details" style="background: aliceblue;border: 1px solid rgb(5 25 64 / 10%);">
								 <td colspan="3">
									 @if($list->stockists_id != 0)
									 <span class="text-left">Detail :{{ $list->stockist->name}} </span>
									@endif
								 </td>
								  <td colspan="4"> 
									 <span class="text-right">Date & time : <?php   echo date('h:i:s A / d-M-Y', strtotime($list->created_at));   ?> </span>
								 </td>
              
            				</tr>
                            @endforeach

                        </tbody>
                    </table><!-- table end -->
                </div>
                <div class="card-footer py-4">
                    {{ $stocklist->links('admin.partials.paginate') }}
                </div>
            </div>

        </div>
    </div>
</div>


@endsection

@push('modal')

<div class="modal fade" id="subscribe_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{route('user.pinPassword')}}" id="passwordData">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"> @lang('Enter Password')?</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="html">Password</label>
                        <input type="password" id="password" name="password" class="form-control">
                        <span class="text-danger" id="password_message"></span>
                        <input type="hidden" id="epinID" name="epinID" class="form-control">
                    </div>
                </div>

                <div class="modal-footer">
                    <input class="form-control form--control" type="hidden" class="d-none" name="plan_id" id="plan_id">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="submit" id='submit' class="btn btn--success subc-btn"> @lang('Submit')</button>
                </div>
            </form>

        </div>
    </div>
</div>

<div class="modal fade" id="subscribe_modal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{route('user.stockist.purchase')}}">
            @csrf
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel"> @lang('Confirm Purchase')?</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="pin_qty">No pins</label>
                    <input type="number" placeholder="Please Enter number of pins" name="pin_qty" id="pin_qty" class="form-control">
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-4">
                            <input type="radio" id="html" name="payment_type" value="bank_account" checked>
                            <label for="html">Current Balance</label>
                        </div>
                        <div class="col-4 text-center ">
                            <input type="radio" id="css" name="payment_type" value="epin_credit">
                            <label for="css" style="padding-left: 4px;"> E-pin credit </label>
                        </div>
                        <div class="col-4 ">
                            <input type="radio" id="pv" name="payment_type" value="pv">
                             <label for="pv" style="padding-left: 4px;">pv</label>
                        </div>
                    </div>
                </div>

            
            </div>

            <div class="modal-footer">
                
                    <input class="form-control form--control" type="hidden" class="d-none" name="plan_id" id="plan_id">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                    <button style="display: ;" type="submit" class="btn btn--success subc-btn"> @lang('Subscribe')</button>
            </div>

            </form>

        </div>
    </div>
</div>


@endpush
@push('script')
<script>
    $(document).on('click', '#btnShow', function() {
        var modal = $('#add-plan');
        $("#subscribe_modal").modal('show');
        id = $(this).attr('e_pinId')
        $('#epinID').val(id)
    })
    $('#submit').click(function(e) {
        e.preventDefault();
        var data = $('#passwordData').serialize();
        var url = $('#passwordData').attr('action')
        var password = $('#password').val();
        var epin_id = $('#epinID').val();
        $.ajax({
            url: url,
            type: 'POST',
            datatype: 'JSON',
            data: {
                "_token": "{{ csrf_token() }}",
                'password': password,
                'epin_id': epin_id
            },
            success: function(data) {
                console.log(data)
                if (data.status == false) {
                    $('#password_message').text(data.message)
                } else {
                    $('#' + data.attr).html(data.epin)
                    $("#subscribe_modal").modal('hide');
                    $('#password').val('')
                }
            }
        });

    })

    $('.__subscribe').on('click', function(e) {
        $("#subscribe_modal1").modal('show');
    })
	
	
	$('.extra-details').hide();

  function myFunction(id) {
    $(`#extra-details${id}`).toggle();
  }
</script>
@endpush