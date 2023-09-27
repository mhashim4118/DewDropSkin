@extends('admin.layouts.app')

@section('panel')

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
                                <th scope="col">@lang('Price')</th>
                                <th scope="col">@lang('E-Pins qty')</th>
                                <th scope="col">@lang('Referral Bouns')</th>
                                <th scope="col">@lang('Stockist Bouns')</th>
                                <th scope="col">@lang('Status')</th>
                                <th scope="col">@lang('Action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($stockist as $key => $plan)
                                <tr>
                                    <td data-label="@lang('Sl')">{{$key+1}}</td>
                                    <td data-label="@lang('Name')">{{ __($plan->name) }}</td>
                                    <td data-label="@lang('Price')">{{ getAmount($plan->price) }} {{$general->cur_text}}</td>
                                    <td data-label="@lang('e_pin_qty')">{{ $plan->e_pin_qty }}</td>
                                    <td data-label="@lang('refferal_bonus')"> {{ getAmount($plan->refferal_bonus) }} {{$general->cur_text}}</td>

                                    <td data-label="@lang('stockist_bonus')">
                                       {{ getAmount($plan->stockist_bonus) }} {{$general->cur_text}}
                                    </td>
                                    <td data-label="@lang('Status')">
                                        @if($plan->status == 1)
                                            <span class="text--small badge font-weight-normal badge--success">@lang('Active')</span>
                                        @else
                                            <span class="text--small badge font-weight-normal badge--danger">@lang('Inactive')</span>
                                        @endif
                                    </td>

                                    <td data-label="@lang('Action')">
                                        <button type="button" class="icon-btn edit" data-toggle="tooltip"
                                                data-id="{{ $plan->id }}"
                                                data-name="{{ $plan->name }}"
                                                data-status="{{ $plan->status }}"
                                                data-e_pin_qty="{{ $plan->e_pin_qty }}"
                                                data-price="{{ getAmount($plan->price) }}"
                                                data-refferal_bonus="{{ getAmount($plan->refferal_bonus) }}"
                                                data-stockist_bonus="{{ getAmount($plan->stockist_bonus) }}"
                                                data-original-title="Edit">
                                            <i class="la la-pencil"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse

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


{{--    edit modal--}}
    <div id="edit-plan" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Edit Stockist')</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>
                <form method="post" action="{{route('admin.stockist.update')}}">
                    @csrf
                    <div class="modal-body">

                        <input class="form-control plan_id" type="hidden" name="id">

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label class="font-weight-bold"> @lang('Plan Name') :</label>
                                <input class="form-control name" name="name" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="font-weight-bold"> @lang('Price') </label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span
                                            class="input-group-text">{{$general->cur_sym}} </span></div>
                                    <input type="text" class="form-control  price" id="price_update" name="price" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold"> @lang('E-Pins qty')</label>
                            <input class="form-control e_pin_qty" name="e_pin_qty" required>
                            <small class="text--red">@lang('If a user who subscribed to this plan, then he will get the E-Pins.')</small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">@lang('Referral Bouns')</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span
                                    class="input-group-text">{{$general->cur_sym}} </span></div>
                                    <input type="text" class="form-control  ref_bonus" id="ref_bonus_update" name="ref_bonus"
                                    required>
                                    <small class="text--red">@lang('If a user who subscribed to this plan, refers someone and if the referred user buys a plan, then he will get referral bonus.')</small>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="font-weight-bold">@lang('Stockist Bouns')</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">{{$general->cur_sym}} </span></div>
                                <input type="text" class="form-control  stockist_bonus" id="stockist_bonus_update" name="stockist_bonus"
                                    required>
                            </div>
                            <small class="small text--red">@lang('When someone buys this plan, then he will get this bonus.')</small>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label class="font-weight-bold">@lang('Status')</label>
                                <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Active')" data-off="@lang('Inactive')" name="status" checked>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-block btn--primary">@lang('Update')</button>
                    </div>
                </form>
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
                <form method="post" action="{{route('admin.stockist.store')}}">
                    @csrf
                    <div class="modal-body">

                        <input class="form-control plan_id" type="hidden" name="id">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label class="font-weight-bold"> @lang('Plan Name') :</label>
                                <input type="text" class="form-control " name="name" id="name" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="font-weight-bold"> @lang('Price') </label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span
                                            class="input-group-text">{{$general->cur_sym}} </span></div>
                                    <input type="text" class="form-control  " name="price" id="price" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold"> @lang('E-Pins qty')</label>
                            <input class="form-control " type="number" min="0" name="e_pin_qty" id="e_pin_qty" required>

                            <small class="text--red">@lang('If a user who subscribed to this plan, then he will get the E-Pins.')</small>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold"> @lang('Referral Bonus')</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span
                                        class="input-group-text">{{$general->cur_sym}} </span></div>
                                <input type="text" class="form-control  " name="ref_bonus" id="ref_bonus" required>
                                <small class="small text--red">@lang('If a user who subscribed to this plan, refers someone and if the referred user buys a plan, then he will get referral bonus.')</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold"> @lang('Stockist Bonus')</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span
                                        class="input-group-text">{{$general->cur_sym}} </span></div>
                                <input type="text" class="form-control  " name="stockist_bonus" id="stockist_bonus" required>
                            </div>
                            <small class="small text--red">@lang('When someone buys this plan, then he will get this bonus.')</small>
                        </div>

                        <div class="form-row">
                            <div class="form-group col">
                                <label class="font-weight-bold">@lang('Status')</label>
                                <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Active')" data-off="@lang('Inactive')" name="status" checked>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn-block btn btn--primary">@lang('Submit')</button>
                    </div>
                </form>

            </div>
        </div>
    </div>


@endsection



@push('breadcrumb-plugins')
    <a href="javascript:void(0)" class="btn btn-sm btn--success add-plan"><i class="fa fa-fw fa-plus"></i>@lang('Add New')</a>

@endpush

@push('script')
    <script>
        "use strict";
        (function ($) {
            $('.edit').on('click', function () {
                var modal = $('#edit-plan');
                console.log(modal)
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

