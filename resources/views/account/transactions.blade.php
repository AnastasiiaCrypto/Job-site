
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
					<div class="col-sm-9">
						<div class="inner-box">
							<h2 class="title-2"><i class="icon-money"></i> {{ t('Transactions') }} </h2>
							
							<div style="clear:both"></div>
							 @include('account.partials.transactions_to_confirm')
							
							<div style="clear:both"></div>
							
							@include('account.partials.transactions')
						</div>
					</div>
				</div>
				<!--/.page-content-->
				
			</div>
			<!--/.row-->
		</div>
		<!--/.container-->
	</div>
	<!-- /.main-container -->
@endsection

@section('after_scripts')
@endsection