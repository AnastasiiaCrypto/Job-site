<div class="modal fade" id="quickLogin" tabindex="-1" role="dialog">
	<div class="modal-dialog  modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span>
					<span class="sr-only">{{ t('Close') }}</span>
				</button>
				<h4 class="modal-title"><i class="icon-login fa"></i> {{ t('Log In') }} </h4>
			</div>
			<form role="form" method="POST" action="{{ lurl(trans('routes.login')) }}">
				{!! csrf_field() !!}
				<div class="modal-body">

					@if (isset($errors) and $errors->any() and old('quickLoginForm')=='1')
						<div class="alert alert-danger">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<ul class="list list-check">
								@foreach($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif
					
					@if (config('settings.social_auth.social_login_activation'))
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center mb30">
							<div class="row row-centered">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-centered small-gutter">
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 mb5">
										<div class="col-xs-12 btn btn-lg btn-fb">
											<a href="{{ lurl('auth/facebook') }}" class="btn-fb"><i class="icon-facebook"></i> Facebook</a>
										</div>
									</div>
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 mb5">
										<div class="col-xs-12 btn btn-lg btn-danger">
											<a href="{{ lurl('auth/google') }}" class="btn-danger"><i class="icon-googleplus-rect"></i> Google</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					@endif
					
					<?php
						$loginValue = (session()->has('login')) ? session('login') : old('login');
						$loginField = getLoginField($loginValue);
						if ($loginField == 'phone') {
							$loginValue = phoneFormat($loginValue, old('country', config('country.code')));
						}
					?>
					<!-- Login -->
					<div class="form-group <?php echo (isset($errors) and $errors->has('login')) ? 'has-error' : ''; ?>">
						<label for="login" class="control-label">{{ t('Login') . ' (' . getLoginLabel() . ')' }}</label>
						<div class="input-icon"><i class="icon-user fa"></i>
							<input id="mLogin" name="login" type="text" placeholder="{{ getLoginLabel() }}" class="form-control" value="{{ $loginValue }}">
						</div>
					</div>
					
					<!-- Password -->
					<div class="form-group <?php echo (isset($errors) and $errors->has('password')) ? 'has-error' : ''; ?>">
						<label for="password" class="control-label">{{ t('Password') }}</label>
						<div class="input-icon"><i class="icon-lock fa"></i>
							<input id="mPassword" name="password" type="password" class="form-control" placeholder="{{ t('Password') }}" autocomplete="off">
						</div>
					</div>
					
					<div class="form-group <?php echo (isset($errors) and $errors->has('remember')) ? 'has-error' : ''; ?>">
						<label class="checkbox pull-left input-btn-padding" style="font-weight: normal;">
							<input type="checkbox" value="1" name="remember" id="mRemember"> {{ t('Keep me logged in') }}
						</label>
						<p class="pull-right" style="margin-top: 10px;">
							<a href="{{ lurl('password/reset') }}"> {{ t('Lost your password?') }} </a> / <a href="{{ lurl(trans('routes.register')) }}">{{ t('Register') }}</a>
						</p>
						<div style=" clear:both"></div>
					</div>
					
					@if (config('settings.security.recaptcha_activation'))
						<!-- recaptcha -->
						<div class="form-group required <?php echo (isset($errors) and $errors->has('g-recaptcha-response')) ? 'has-error' : ''; ?>">
							<label class="control-label" for="g-recaptcha-response">{{ t('We do not like robots') }}</label>
							<div>
								{!! Recaptcha::render(['lang' => config('app.locale')]) !!}
							</div>
						</div>
					@endif
					
					<input type="hidden" name="quickLoginForm" value="1">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{ t('Cancel') }}</button>
					<button type="submit" class="btn btn-success pull-right">{{ t('Log In') }}</button>
				</div>
			</form>
		</div>
	</div>
</div>
