@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('ID')</th>
                                    <th>@lang('Username')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Percentage')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Date')</th>
                                    <th></th>
                                    <th scope="col">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($partner_earnings as $pe)
                                    <tr>
                                        <td data-label="@lang('ID')">
                                            {{ __($pe->id) }}
                                        </td>
                                        <td data-label="@lang('Username')">
                                            {{ __($pe->dds_username) }}
                                        </td>
                                        <td data-label="@lang('Name')">
                                            {{ __($pe->name) }}
                                        </td>

                                        <td data-label="@lang('Percentage')">
                                            {{ __($pe->percentage) }}
                                        </td>
                                        <td data-label="@lang('Amount')">
                                            {{ __($pe->amount) }} PKR
                                        </td>
                                        <td data-label="@lang('Name')">
                                            {{ showDateTime($pe->date) }}
                                        </td>
                                        <td></td>
                                        <td data-label="@lang('Action')">
                                            <a class="icon-btn edit text-white"
                                                href="{{ route('admin.report.edit_partner', $pe->id) }}"
                                                data-toggle="tooltip" data-original-title="Edit">
                                                <i class="la la-pencil"></i>
                                            </a>
                                            <a class="ml-1 icon-btn bg-danger edit text-white"
                                                href="{{ route('admin.report.delete_partner', $pe->id) }}"
                                                data-toggle="tooltip" data-original-title="Delete">
                                                <i class=" fa fa-trash"></i>
                                            </a>
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer py-4">
                    {{ paginateLinks($partner_earnings) }}
                </div>
            </div>
        </div>
    </div>

  <div id="matrixSettingModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Transfer Leadership Bonus to a User')</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.report.add_partner') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="matrix_height"
                                    class="form-control-label font-weight-bold">@lang('Username') <sup
                                        class="text--danger">*</sup></label>
                                <input type="text" id="username" class="form-control form-control-lg"
                                    name="username" placeholder="@lang(' Enter Username')" required="">

                            </div>
                            <div style="display:none" id="fullname_block">
                                <div class="form-group">
                                    <label for="matrix_height"
                                        class="form-control-label font-weight-bold">@lang('Full Name') <sup
                                            class="text--danger">*</sup></label>
                                    <input type="text" id="fullname" class="form-control form-control-lg"
                                        readonly name="name" placeholder="@lang(' Enter Username')" required="">

                                </div>
                                <div class="form-group">
                                    <label for="matrix_height"
                                        class="form-control-label font-weight-bold">@lang('Percentage') <sup
                                            class="text--danger">*</sup></label>
                                    <input type="text" class="form-control form-control-lg" name="percentage"
                                        placeholder="@lang(' Enter Percentage')" required="">

                                </div>
                                <div class="form-group">
                                    <label for="matrix_height"
                                        class="form-control-label font-weight-bold">@lang('Password') <sup
                                            class="text--danger">*</sup></label>
                                    <input type="text" class="form-control form-control-lg" name="password"
                                        placeholder="@lang(' Enter Password')" required="">

                                </div>
                            </div>
                            <div style="display:none" id="cannotTransferBalance">

                                <div class="form-group">
                                    <p class="text-danger">This User is already added!</p>
                                </div>
                            </div>


                        </div>
                        <div class="modal-footer">
                            <button type="button" id="checkUsername"
                                class="btn btn--dark">@lang('Search')</button>

                            <button type="submit" style="display: none;" id="transferBalance"
                                class="btn btn--primary">@lang('Add Partner')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
@endsection

@push('breadcrumb-plugins')
    <a href="javascript:void(0)" class="btn btn-md bg--10 text-white box--shadow1 text--small matrixSetting"><i
            class="las la-plus "></i>@lang('Add Partner')</a>
@endpush

@push('script')
    <script>
        "use strict";
        $('.matrixSetting').on('click', function() {
            $('#matrixSettingModal').modal('show');
        });

        
        $('#checkUsername').on('click', function() {
                var username = $('#username').val();
                var password = $("input[name=password]").val();

                var email = $("input[name=email]").val();



                $.ajax({

                    type: 'POST',

                    url: "{{ route('admin.report.checkUsername') }}",

                    data: {
                        "_token": "{{ csrf_token() }}",
                        "username": username
                    },
                    dataType: 'json',
                    success: function(data) {

                        if (data.message !== 'This User is already added!') {

                            $('#fullname_block').show();
                            $('#checkUsername').hide();
                            $('#transferBalance').show();
                            $('#fullname').val(data.name);
                            $('#username').attr('readOnly', true);
                            $('#cannotTransferBalance').hide();
                        } else {
                            $('#cannotTransferBalance').show();
                        }

                    }

                });


            })
    </script>
@endpush