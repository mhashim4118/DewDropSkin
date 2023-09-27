@extends('admin.layouts.app')

@section('panel')

<?php use App\Models\User;  ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                            <tr>
                                <th scope="col">@lang('Sl')</th>
                                <th scope="col">@lang('Plan Name')</th>
                                <th scope="col">@lang('User Name')</th>
                                <th scope="col">@lang('Price')</th>
                                <th scope="col">@lang('E-Pins')</th>
                                <th scope="col">@lang('Used By')</th>
                                <th scope="col">@lang('Status')</th>
                            </tr>
                            </thead>
                            <tbody>
                                @php($count =1) 
                            @foreach($stockist as $list)
                                <tr>
                                    <td>{{ $count++  }}</td>
									@if($list->stockists_id == 0)
									 <td>single E-pin</td>
									@else
                                    <td>{{ $list->Stockist->name }}</td>
									@endif
                                    <td>{{ $list->user->firstname }} {{ $list->user->lastname }} ({{$list->user->username}})</td>
                                    <td>{{ $list->pay_price }}</td>
                                    <td>{{ $list->e_pin }}</td>
                                    <td>@if($list->used_at==0)<span class="badge badge-success"> Not Used</span>@else <span class="badge badge-danger"> <?php $user=  User::find($list->used_at);  echo $user->firstname." ".$user->lastname." (".$user->username .")" ?></span>  @endif</td>
                                    <td>@if( $list->used==1 ) <span class="badge badge-danger"> Used</span>@else <span class="badge badge-success">Not Used</span> @endif </td>
                                </tr>
                             @endforeach   

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                <div class="card-footer py-4">
                    {{ $stockist->links('admin.partials.paginate') }}
                </div>
            </div>
        </div>
    </div>




    <div id="add-plan" class="modal  fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Add New plan')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    </div>


@endsection



@push('script')
    <script>
        "use strict";
        (function ($) {
            $('.edit').on('click', function () {
                var modal = $('#edit-plan');
                modal.find('.name').val($(this).data('name'));
                modal.find('.price').val($(this).data('price'));
                modal.find('.e_pin_qty').val($(this).data('e_pin_qty'));
                modal.find('.ref_bonus').val($(this).data('refferal_bonus'));
                modal.find('.stockist_bonus').val($(this).data('stockist_bonus'));
                modal.find('input[name=daily_ad_limit]').val($(this).data('daily_ad_limit'));
                if($(this).data('status')){
                    modal.find('.toggle').removeClass('btn--danger off').addClass('btn--success');
                    modal.find('input[name="status"]').prop('checked',true);
                }else{
                    modal.find('.toggle').addClass('btn--danger off').removeClass('btn--success');
                    modal.find('input[name="status"]').prop('checked',false);
                }
                modal.find('input[name=id]').val($(this).data('id'));
                modal.modal('show');
            });

            $('.add-plan').on('click', function () {
                var modal = $('#add-plan');
                modal.modal('show');
            });
        })(jQuery);

    </script>
@endpush

