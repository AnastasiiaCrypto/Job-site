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

@section('content')
	@include('common.spacer')
	<div class="main-container">
		<div class="container">
			<div class="row">

				@if (isset($errors) and $errors->any())
					<div class="col-lg-12">
						<div class="alert alert-danger">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
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


				@if (config('settings.social_auth.social_login_activation'))
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center mb30">
						<div class="row row-centered">
							<div class="col-lg-6 col-md-8 col-sm-10 col-xs-12 col-centered small-gutter">
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 mb5">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 btn btn-lg btn-fb">
										<a href="{{ lurl('auth/facebook') }}" class="btn-fb"><i class="icon-facebook"></i> {!! t('Connect with Facebook') !!}</a>
									</div>
								</div>
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 mb5">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 btn btn-lg btn-danger">
										<a href="{{ lurl('auth/google') }}" class="btn-danger"><i class="icon-googleplus-rect"></i> {!! t('Connect with Google') !!}</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				@endif
				
				
				<div class="col-lg-5 col-md-8 col-sm-10 col-xs-12 login-box">
					<form id="loginForm" role="form" method="POST" action="{{ url()->current() }}">
						{!! csrf_field() !!}
						<input type="hidden" name="country" value="{{ config('country.code') }}">
						<div class="panel panel-default">
							
							<div class="panel-intro text-center">
								<h2 class="logo-title">
									<strong><span class="logo-icon"></span>{{ t('Log In') }}</strong>
								</h2>
							</div>
							
							<div class="panel-body">
								<?php
									$loginValue = (session()->has('login')) ? session('login') : old('login');
									$loginField = getLoginField($loginValue);
									if ($loginField == 'phone') {
										$loginValue = phoneFormat($loginValue, old('country', config('country.code')));
									}
								?>
								<!-- Login -->
								<div class="form-group <?php echo (isset($errors) and $errors->has('login')) ? 'has-error' : ''; ?>">
									<label for="login" class="control-label">{{ t('Login') . ' (' . getLoginLabel() . ')' }}:</label>
									<div class="input-icon"><i class="icon-user fa"></i>
										<input id="login" name="login" type="text" placeholder="{{ getLoginLabel() }}" class="form-control" value="{{ $loginValue }}">
									</div>
								</div>
								
								<!-- Password -->
								<div class="form-group <?php echo (isset($errors) and $errors->has('password')) ? 'has-error' : ''; ?>">
									<label for="password" class="control-label">{{ t('Password') }}:</label>
									<div class="input-icon"><i class="icon-lock fa"></i>
										<input id="password" name="password" type="password" class="form-control" placeholder="{{ t('Password') }}" autocomplete="off">
									</div>
								</div>
								
								@if (config('settings.security.recaptcha_activation'))
									<!-- g-recaptcha-response -->
									<div class="form-group required <?php echo (isset($errors) and $errors->has('g-recaptcha-response')) ? 'has-error' : ''; ?>">
										<div class="no-label">
											{!! Recaptcha::render(['lang' => config('app.locale')]) !!}
										</div>
									</div>
								@endif
								
								<!-- Submit -->
								<div class="form-group">
									<button id="loginBtn" class="btn btn-primary btn-block"> {{ t('Log In') }} </button>
								</div>
							</div>
							
							<div class="panel-footer">
								<label class="checkbox pull-left input-btn-padding">
									<input type="checkbox" value="1" name="remember" id="remember"> {{ t('Keep me logged in') }}
								</label>
								<p class="text-center pull-right">
									<a href="{{ lurl('password/reset') }}"> {{ t('Lost your password?') }} </a>
								</p>
								<div style=" clear:both"></div>
							</div>
						</div>
					</form>

					<div class="login-box-btm text-center">
						<p>{{ t('Don\'t have an account?') }} <br><a href="{{ lurl(trans('routes.register')) }}"><strong>{{ t('Sign Up') }} !</strong></a></p>
					</div>
				</div>
				
			</div>
		</div>
	</div>
@endsection

@section('after_scripts')
	<script>
		$(document).ready(function () {
			$("#loginBtn").click(function () {
				$("#loginForm").submit();
				return false;
			});
		});
	</script>
@endsection
