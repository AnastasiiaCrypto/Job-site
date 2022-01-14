{{--
 * JobClass - Geolocalized Job Board Script
 * Copyright (c) BedigitCom. All Rights Reserved
 *
 * Website: http://www.bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from Codecanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
--}}
@extends('layouts.master')

@section('search')
	@parent
	@include('pages.inc.contact-intro')
@endsection

@section('content')
	@include('common.spacer')
	<div class="main-container">
		<div class="container">
			<div class="row clearfix">
				
				@if (isset($errors) and $errors->any())
					<div class="col-lg-12">
						<div class="alert alert-danger">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<h5><strong>{{ t('Oops ! An error has occurred. Please correct the red fields in the form') }}</strong></h5>
							<ul class="list list-check">
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					</div>
				@endif

				@if (Session::has('flash_notification'))
					<div class="container" style="margin-bottom: -10px; margin-top: -10px;">
						<div class="row">
							<div class="col-lg-12">
								@include('flash::message')
							</div>
						</div>
					</div>
				@endif
				
				<div class="col-md-12">
					<div class="contact-form">
						<h5 class="list-title gray"><strong>{{ t('Contact Us') }}</strong></h5>

						<form class="form-horizontal" method="post" action="{{ lurl(trans('routes.contact')) }}">
							{!! csrf_field() !!}
							<fieldset>
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group required <?php echo (isset($errors) and $errors->has('first_name')) ? 'has-error' : ''; ?>">
											<div class="col-md-12">
												<input id="first_name" name="first_name" type="text" placeholder="{{ t('First Name') }}"
													   class="form-control" value="{{ old('first_name') }}">
											</div>
										</div>
									</div>

									<div class="col-sm-6">
										<div class="form-group required <?php echo (isset($errors) and $errors->has('last_name')) ? 'has-error' : ''; ?>">
											<div class="col-md-12">
												<input id="last_name" name="last_name" type="text" placeholder="{{ t('Last Name') }}"
													   class="form-control" value="{{ old('last_name') }}">
											</div>
										</div>
									</div>

									<div class="col-sm-6">
										<div class="form-group required <?php echo (isset($errors) and $errors->has('company_name')) ? 'has-error' : ''; ?>">
											<div class="col-md-12">
												<input id="company_name" name="company_name" type="text" placeholder="{{ t('Company Name') }}"
													   class="form-control" value="{{ old('company_name') }}">
											</div>
										</div>
									</div>

									<div class="col-sm-6">
										<div class="form-group required <?php echo (isset($errors) and $errors->has('email')) ? 'has-error' : ''; ?>">
											<div class="col-md-12">
												<input id="email" name="email" type="text" placeholder="{{ t('Email Address') }}" class="form-control"
													   value="{{ old('email') }}">
											</div>
										</div>
									</div>

									<div class="col-lg-12">
										<div class="form-group required <?php echo (isset($errors) and $errors->has('message')) ? 'has-error' : ''; ?>">
											<div class="col-md-12">
												<textarea class="form-control" id="message" name="message" placeholder="{{ t('Message') }}"
														  rows="7">{{ old('message') }}</textarea>
											</div>
										</div>

										<!-- Captcha -->
										@if (config('settings.security.recaptcha_activation'))
											<div class="form-group required <?php echo (isset($errors) and $errors->has('g-recaptcha-response')) ? 'has-error' : ''; ?>">
												<div class="col-md-12 control-label" for="g-recaptcha-response">
													{!! Recaptcha::render(['lang' => config('app.locale')]) !!}
												</div>
											</div>
										@endif

										<div class="form-group">
											<div class="col-md-12 ">
												<button type="submit" class="btn btn-primary btn-lg">{{ t('Submit') }}</button>
											</div>
										</div>
									</div>
								</div>
							</fieldset>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('after_scripts')
	<script src="{{ url('assets/js/form-validation.js') }}"></script>
@endsection
