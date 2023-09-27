@extends($activeTemplate.'layouts.master')
@section('content')

<div class="row justify-content-center ">
  <div class="col-sm-12 mx-3 col-md-12 col-lg-12 col-xl-12 mb-4 dashboard-item">
    <div class="header-left" style="display: flex; justify-content: space-between">
      <div>
        <h3 class="ammount theme-one">Lucky Draw PROMO</h3>
        <h6><span>@lang('Requirement: 100 Orders')</span></h6>
      </div>
      <img style="width: 100px; margin: 0 " src="{{asset('assets/Rewards/1687106520.jpg')}}" alt="">
    </div>
    <?php
    $per = (100 - $ldpsCount);
    $left = 100 - $ldpsCount;
    ?>
    @if($left < 100)
    <div class="bg-dark">
      <h3 class="mt-3" style="width:<?php echo intval($ldpsCount) ?>%; height:10px; background: #0d6efd;"></h3>
      <p class="bg-white">you need {{$left}} more order to get this lucky draw PROMO</p>
    </div>
    @elseif($ldpsCount == 0)
    <div class="bg-dark">
      <h3 class="mt-3" style="width:0%; height:10px; background: #0d6efd;"></h3>
      <p class="bg-white">you need {{100}} more order to get this lucky draw PROMO</p>
    </div>
    @else
    <div class="bg-dark">
      <h3 class="mt-3" style="width:100%; height:10px; background: #0d6efd;"></h3>
      <p class="bg-white">This lucky draw PROMO has been achieved</p>
    </div>
    @endif
  </div>
</div>

<h4>My Orders</h4>
<table class="transection-table-2">
    <thead>
        <tr>
            <th scope="col">@lang('Sl')</th>
            <th scope="col">@lang('LDP')</th>
            <th scope="col">@lang('Product')</th>
            <th scope="col">@lang('Invoice')</th>
        </tr>
    </thead>
    <tbody>
        @if(count($ldps) == 0)
        <tr>
            <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
        </tr>
        @else
        @php $slCounter = $ldps->total(); @endphp
        @foreach($ldps as $ldp)
        <tr style="cursor: pointer; margin-bottom:20px; box-shadow: rgba(50, 50, 93, 0.25) 0px 13px 27px -5px, rgba(0, 0, 0, 0.3) 0px 8px 16px -8px;">
            <td data-label="@lang('Sl')">{{ $slCounter-- }}</td>
            <td data-label="@lang('LDP')">{{ __($ldp->dlp) }}</td>
            
            <?php $p = Illuminate\Support\Facades\DB::connection('mysql_store')->table('products')->where('id',66)->first(); ?>
            <td data-label="@lang('Product')">{{ $p->name }}</td>
            
            <td data-label="@lang('Invoice')" class="text-center">
                <a class="btn btn--primary px-4" target="_blank" href="https://dewdropskin.com/store/customer-generate-invoice/{{ $ldp->order_id }}">
                    <i class="fa fa-print"></i>
                </a>
            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
<div class="card-footer pb-4">
    {{ $ldps->links() }}
</div>
@endsection

@push('script')
<script>
$('.extra-details').hide();

function myFunction(id) {
    $(`#extra-details${id}`).toggle();
}
</script>
@endpush
