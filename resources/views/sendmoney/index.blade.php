
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
							<h4>{{__('Money Transfer')}}</h4>
							<hr>
							<div class="panel panel-default" style="border: unset;">

								<div class="panel-body">
								  <form action="{{route('sendMoney')}}" method="post" enctype="multipart/form-data">
									{{csrf_field()}}
									<div class="row">
									  <div class="col">
										<div class="form-group {{ $errors->has('amount') ? ' has-danger' : '' }}">
										  <label for="amount">{{__('Amount')}}</label>
										  <input type="number" class="form-control" id="amount" name="amount" value="{{old('amount')}}" required placeholder="5.00" pattern="[0-9]+([\.,][0-9]+)?" 
										  step="0.01" >
										   @if ($errors->has('amount'))
												<div class="">
													<strong>{{ $errors->first('amount') }}</strong>
												</div>
											@endif
										</div>
									  </div>
									  <div class="col">
										<div class="form-group {{ $errors->has('email') ? ' has-danger' : '' }}">
										  <label for="email">{{__('User email')}}</label>
										  <input type="email" class="form-control" id="email" name="email" required>
										   @if ($errors->has('email'))
												<div class="">
													<strong>{{ $errors->first('email') }}</strong>
												</div>
											@endif
										</div>
									  </div>
									</div>
									<div class="clearfix"></div>
									<div class="row">
									  <div class="col">
										<div class="form-group {{ $errors->has('description') ? ' has-danger' : '' }}">
										  <label for="description">{{__('Note for Recepient')}}</label>
										  <textarea class="form-control" rows="5" id="description" name="description" required></textarea>
										   @if ($errors->has('description'))
												<div class="">
													<strong>{{ $errors->first('description') }}</strong>
												</div>
											@endif
										</div>
									  </div>
									</div>
									<div class="clearfix"></div>
									<div class="row">
									  <div class="col">
										<input type="submit" class="btn btn-outline-dark btn-lg pull-right" value="{{__('Send Money')}}">
									  </div>
									</div>
									<div class="clearfix"></div>
								  </form>
								</div>
							</div>
						</div>
				
				<!--/.page-content-->
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