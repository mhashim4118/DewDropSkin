@extends($activeTemplate.'layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12 mb-5">
            <div class="card  border-0 shadow">
                <div class="card-header bg--primary-gradient">
                    <h4 class="card-title font-weight-normal text-white">@lang('Referrer Link')</h4>
                </div>
                <div class="card-body">
                    <h4 class="card-title font-weight-normal">@lang('Join left')</h4>
                    <form id="copyBoard" >
                        <div class="row align-items-center">
                            <div class="col-md-10">
                                <input value="{{route('user.register')}}/?ref={{auth()->user()->username}}&position=left" type="url" id="ref" class="form-control form--control from-control-lg" readonly>
                            </div>
                            <div class="col-md-2">
                                <button   type="button" onclick="myFunction('ref')" id="copybtn" class="cmn--btn btn-block active"> <span><i class="fa fa-copy"></i> @lang('Copy')</span></button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <h4 class="card-title font-weight-normal">@lang('Join right')</h4>
                    <form id="copyBoard2" >
                        <div class="row align-items-center">
                            <div class="col-md-10 my-1">
                                <input value="{{route('user.register')}}?ref={{auth()->user()->username}}&position=right" type="url" id="ref2" class="form-control form--control from-control-lg" readonly>
                            </div>
                            <div class="col-md-2 my-1">
                                <button   type="button" onclick="myFunction('ref2')" id="copybtn2" class="cmn--btn btn-block btn-sm active"> <span><i class="fa fa-copy"></i> @lang('Copy')</span></button>
                            </div>
                        </div>
                    </form>
                </div>
				
				  <div class="card-body">
                    <h4 class="card-title">@lang('Generate Link')</h4>
                    <div class="row align-items-center">
                        
                        <div class="col-lg-5 my-1">

                            <input type="text" class="form-control" id="username" placeholder="Enter username">
                        </div>
                        <div class="col-lg-5 my-1">
							<select class="form-control" id="position">
                                <option>Select position</option>
                                <option value="left">Left</option>
                                <option value="right">Right</option>
                            </select>
                        </div>
                        <div class="col-lg-2 my-1">

                           <button class="btn-block" style="border-radius:0px; width: 100%;" onclick="generateLink()">Generate</button>
                        </div>
                    <!-- <form action=""> -->
                   
                    </div>

                    <form id="copyBoard3" style="display: none;">
                        <div class="row align-items-center mt-3">
                            <div class="col-md-10 my-1">
                                <input id="ref3" type="url" readonly  class="form-control form--control from-control-lg" >
                            </div>
                            <div class="col-md-2 my-1">
                                <button type="button" onclick="myFunction('ref3')" id="copybtn3" class="cmn--btn btn-block btn-sm active"> <span><i class="fa fa-copy"></i> @lang('Copy')</span></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-12 ">
            <div class="card b-radius--10 overflow-hidden border-0">
                <div class="card-body p-0">

                    <table class="transection-table-2">
                        <thead>
                        <tr>
                            <th scope="col">@lang('S No.')</th>
                            <th scope="col">@lang('Username')</th>
                            <th scope="col">@lang('Name')</th>
                            <th scope="col">@lang('Email')</th>
                            <th scope="col">@lang('Join Date')</th>
                        </tr>
                        </thead>
                        <tbody >
                        @forelse($logs as $key=>$data)
                            <tr style="margin-bottom:20px; 
									   box-shadow: rgba(50, 50, 93, 0.25) 0px 13px 27px -5px, rgba(0, 0, 0, 0.3) 0px 8px 16px -8px;">
                                <td data-label="@lang('S No.')" >{{ ($logs->total() - ($logs->firstItem() + $key)) + 1}}</td>
                                <td data-label="@lang('Username')">{{$data->username}}</td>
                                <td data-label="@lang('Name')">{{$data->fullname}}</td>
                                <td data-label="@lang('Email')">{{$data->email}}</td>
                                <td data-label="@lang('Join Date')">
                                    @if($data->created_at != '')
                                    {{ showDateTime($data->created_at) }}
                                    @else
                                    @lang('Not Assign')
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                            </tr>
                        @endforelse

                        </tbody>
                    </table>
					{{$logs->links()}}
                </div>
                <div class="card-footer py-4 bg-white bg-transparent border-0">
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')

    <script>
        function generateLink(){
            var username = document.getElementById('username');
            var position = document.getElementById('position');
            if(position.value !== 'Select Position' && username.value !== null){
                document.getElementById('copyBoard3').style.display = 'block';
                var link = document.getElementById('ref3');
                link.setAttribute('value','{{route("user.register")}}?ref='+username.value+'&position='+position.value);// = 'kk';
            } else {
                alert('Please provide proper values!');
            }
           
        }
    </script>

    <script>
        'use strict';
        function myFunction(id) {
            var copyText = document.getElementById(id);
            copyText.select();
            copyText.setSelectionRange(0, 99999)
            document.execCommand("copy");
            notify('success', 'Url copied successfully ' + copyText.value);
        }
    </script>

@endpush


