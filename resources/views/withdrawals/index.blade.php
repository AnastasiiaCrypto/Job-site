
 @extends('layouts.master')  
 @section('content')  
 @include('common.spacer') 
<div class="main-container">
    <div class="container">
        <div class="row">
            <div class="col-sm-3 page-sidebar">
                @include('account.inc.sidebar')
            </div>
            <!--/.page-sidebar-->

            <div class="col-sm-9 page-content row">

                @include('partials.nav')
                <div class="col-sm-3 custom-sidebar" style="padding: 0;">
                    @include('partials.sidebar')
                </div>

                <div class="col-md-9 " style="padding-right: 0">

                    
                    <div class="card border-light">
                        <div class="card-header bg-light">
                            <h4>{{__('My withdrawals')}}</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped" style="margin-bottom: 0;">
                                <thead>
                                    <tr>
                                        <th>{{__('Date')}}</th>
                                        <th>{{__('Method')}}</th>
                                        <th>{{__('Platform ID')}} ( {{__('your Id on choosen Method platform')}} )</th>
                                        <th>{{__('Gross')}}</th>
                                        <th>{{__('Fee')}}</th>
                                        <th>{{__('Net')}}</th>
                                    </tr>
                                </thead>
								<tbody>
                                    @forelse($withdrawals as $withdrawal)
                                        <tr>
                                            <td>{{$withdrawal->created_at->format('d M Y')}} <br> @include ('withdrawals.partials.status')</td>
                                            <td>{{$withdrawal->Method->name}}</td>
                                            <td>{{$withdrawal->platform_id}}</td>
                                            <td>{{$withdrawal->gross()}}</td>
                                            <td>{{$withdrawal->fee()}}</td>
                                            <td>{{$withdrawal->net()}}</td>
                                        </tr>
                                    @empty
                                    @endforelse
								</tbody>
                            </table>
                        </div>
                        @if($withdrawals->LastPage() != 1)
                        <div class="card-footer">
                            {{$withdrawals->links()}}
                        </div>
                        @else
                        @endif
                    </div>
                    

                </div>

            </div>

            </div>
			<!--/.row-->
		</div>
		<!--/.container-->
	</div>
	<!-- /.main-container -->
@endsection

@section('after_scripts')
@endsection
