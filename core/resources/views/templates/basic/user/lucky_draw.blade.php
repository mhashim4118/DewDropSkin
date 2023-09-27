@extends($activeTemplate.'layouts.master')
@section('content')

<div class="row justify-content-center ">
  <div class="col-sm-12 mx-3 col-md-12 col-lg-12 col-xl-12 mb-4 dashboard-item">
    <div class="header-left" style="display: flex; justify-content:space-between">
      <div>
       
        <h3 class=" ammount theme-one">Lucky Draw PROMO</h3>
        <h6> <span>@lang('Requirement: 100 Orders')</span></h6>
      </div>
      <img style="width: 100px; margin: 0 " src="{{asset('assets/Rewards/Mobile.png')}}" alt="">

    </div>
    <?php
    $per = (100 - $allOrders) ;
    $left = 100 - $allOrders;
    ?>
    @if($left < 100) 
    <div class="bg-dark">
      <h3 class="mt-3" style="width:<?php echo intval($allOrders) ?>%; height:10px; background: #0d6efd;"></h3>
      <p class="bg-white">you need {{$left}} more order to get this lucky draw PROMO</p>
    </div>
    @elseif($allOrders == 0) 
    <div class="bg-dark">
      <h3 class="mt-3" style="width:0%; height:10px; background: #0d6efd;"></h3>
      <p class="bg-white">you need {{100}} more order to get this lucky draw PROMO</p>
    </div>
    @else
    <div class="bg-dark">
      <h3 class="mt-3" style="width:100%; height:10px; background: #0d6efd;"></h3>
      <p class="bg-white">This lucky draw PROMO has been achived</p>
    </div>
    @endif
  </div>
</div>


<h4>My Refferals Orders <a style="float:right; color:#37ACE2 !important" href="{{route('user.lucky-draw-myorders')}}"> My Orders <i class="fas fa-arrow-right"></i></a></h4>
<table class="transection-table-2">
    <thead>
        <tr>
          <th scope="col">@lang('Sl')</th>
          <th scope="col">@lang('Username')</th>
			<th>Full Name</th>
			<th>City</th>
			<th scope="col">@lang('LDP')</th>
          <th scope="col">@lang('Product')</th>

        </tr>
    </thead>
    <tbody>

		@if($allOrders == 0)
			    <tr>
					  <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
				</tr>
		@else 
	
		@php $i = 1; @endphp
      @foreach(array_reverse($uniqueUsernames) as $key => $o)


      <tr style="cursor: pointer; margin-bottom:20px; box-shadow: rgba(50, 50, 93, 0.25) 0px 13px 27px -5px, rgba(0, 0, 0, 0.3) 0px 8px 16px -8px;">
          <td data-label="@lang('Sl')">{{ $i++ }}</td>
          <td data-label="@lang('Username')">{{ __($o->username) }}</td><?php $user = \App\Models\User::where('username', $o->username)->first(); ?><td data-label="@lang('Full Name')">{{$user->firstname}} {{$user->lastname }}</td><td data-label="@lang('City')">{{$user->address->city}}</td>
         <td data-label="@lang('LDP')">{{ __($o->dlp) }}</td>
         <?php $p = Illuminate\Support\Facades\DB::connection('mysql_store')->table('products')->where('id',66)->first(); ?>
          <td data-label="@lang('Product')">{{ $p->name }}</td>
   
         
      
      </tr>
		
  
      @endforeach

@endif
  </tbody>
</table>

@endsection


@push('script')
  <script>
  $('.extra-details').hide();

  function myFunction(id) {
    $(`#extra-details${id}`).toggle();
  }
      
  </script>
@endpush