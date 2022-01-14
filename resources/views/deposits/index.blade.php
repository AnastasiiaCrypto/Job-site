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
                            <h4>{{__('My Deposits')}}</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped" style="margin-bottom: 0;">
                                <thead>
                                    <tr>
                                        <th>{{__('Date')}}</th>
                                        <th>{{__('Method')}}</th>
                                        <th>{{__('Gross')}}</th>
                                        <th>{{__('Fee')}}</th>
                                        <th>{{__('Net')}}</th>

                                    </tr>
                                </thead>
								@if($deposits->total()>0)
									<tbody>
										@foreach($deposits as $deposit)
										<tr>
											<td>{{$deposit->created_at->format('d M Y')}}
												<br> @include ('deposits.partials.status')</td>
											<td>{{$deposit->Method->name}}</td>
											<td>{{$deposit->gross()}}</td>
											<td>{{$deposit->fee()}}</td>
											<td>{{$deposit->net()}}</td>
										</tr>
										@endforeach
									</tbody>
								@endif
                            </table>
                        </div>
                        @if($deposits->total()>0)
							<div class="card-footer">
								{{$deposits->links()}}
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