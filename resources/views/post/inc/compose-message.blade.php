<div class="modal fade" id="applyJob" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span>
					<span class="sr-only">{{ t('Close') }}</span>
				</button>
				<h4 class="modal-title"><i class=" icon-mail-2"></i> {{ t('Contact Employer') }} </h4>
			</div>
			<form role="form" method="POST" action="{{ lurl('posts/' . $post->id . '/contact') }}" enctype="multipart/form-data">
				{!! csrf_field() !!}
				<div class="modal-body">

					@if (isset($errors) and $errors->any() and old('messageForm')=='1')
						<div class="alert alert-danger">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<ul class="list list-check">
								@foreach($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif

					@if (auth()->check())
						<input type="hidden" name="from_name" value="{{ auth()->user()->name }}">
						@if (!empty(auth()->user()->email))
							<input type="hidden" name="from_email" value="{{ auth()->user()->email }}">
						@else
							<!-- from_email -->
							<div class="form-group required <?php echo (isset($errors) and $errors->has('from_email')) ? 'has-error' : ''; ?>">
								<label for="from_email" class="control-label">{{ t('E-mail') }}
									@if (!isEnabledField('phone'))
										<sup>*</sup>
									@endif
								</label>
								<div class="input-group">
									<span class="input-group-addon"><i class="icon-mail"></i></span>
									<input id="from_email" name="from_email" type="text" placeholder="{{ t('i.e. you@gmail.com') }}"
										   class="form-control" value="{{ old('from_email', auth()->user()->email) }}">
								</div>
							</div>
						@endif
					@else
						<!-- from_name -->
						<div class="form-group required <?php echo (isset($errors) and $errors->has('from_name')) ? 'has-error' : ''; ?>">
							<label for="from_name" class="control-label">{{ t('Name') }} <sup>*</sup></label>
							<input id="from_name" name="from_name" class="form-control" placeholder="{{ t('Your name') }}" type="text"
								   value="{{ old('from_name') }}">
						</div>
							
						<!-- from_email -->
						<div class="form-group required <?php echo (isset($errors) and $errors->has('from_email')) ? 'has-error' : ''; ?>">
							<label for="from_email" class="control-label">{{ t('E-mail') }}
								@if (!isEnabledField('phone'))
									<sup>*</sup>
								@endif
							</label>
							<div class="input-group">
								<span class="input-group-addon"><i class="icon-mail"></i></span>
								<input id="from_email" name="from_email" type="text" placeholder="{{ t('i.e. you@gmail.com') }}"
									   class="form-control" value="{{ old('from_email') }}">
							</div>
						</div>
					@endif
					
					<!-- from_phone -->
					<div class="form-group required <?php echo (isset($errors) and $errors->has('from_phone')) ? 'has-error' : ''; ?>">
						<label for="from_phone" class="control-label">{{ t('Phone Number') }}
							@if (!isEnabledField('email'))
								<sup>*</sup>
							@endif
						</label>
						<div class="input-group">
							<span class="input-group-addon"><i class="icon-phone-1"></i></span>
							<input id="from_phone" name="from_phone" type="text"
								   placeholder="{{ t('Phone Number') }}"
								   maxlength="60" class="form-control" value="{{ old('from_phone', (auth()->check()) ? auth()->user()->phone : '') }}">
						</div>
					</div>
					
					<!-- message -->
					<div class="form-group required <?php echo (isset($errors) and $errors->has('message')) ? 'has-error' : ''; ?>">
						<label for="message" class="control-label">{{ t('Message') }} <span class="text-count">(500 max)</span> <sup>*</sup></label>
						<textarea id="message" name="message" class="form-control required" placeholder="{{ t('Your message here...') }}" rows="5">{{ old('message') }}</textarea>
					</div>

					<!-- filename -->
					<div class="form-group">
						<label class="control-label" for="filename">{{ t('Resume') }} </label>
						<span class="help-block">{!! t('Select a Resume') !!}</span>
						<div id="resumeId" class="mb10 input-btn-padding">
							<?php
								$selectedResume = 0;
							?>
							@if (isset($resumes) and $resumes->count() > 0)
								@foreach ($resumes as $iResume)
									@continue(!\Storage::exists($iResume->filename))
									<?php
										if (old('resume_id', 0) == $iResume->id) {
											$selectedResume = $iResume->id;
										} else {
											$selectedResume = isset($lastResume) ? $lastResume->id : 0;
										}
									?>
									<label class="radio" for="resume">
										<input id="resumeId{{ $iResume->id }}"
											   name="resume_id"
											   value="{{ $iResume->id }}"
											   type="radio"
												{{ ($selectedResume == $iResume->id) ? 'checked="checked"' : '' }}>
										{{ $iResume->name }} - <a href="{{ \Storage::url($iResume->filename) }}" target="_blank">{{ t('Download') }}</a>
									</label>
								@endforeach
							@endif
							<label class="radio" for="resume">
								<input id="resumeId0"
									   name="resume_id"
									   value="0"
									   type="radio"
										{{ ($selectedResume == 0) ? 'checked="checked"' : '' }}>
								{{ '[+] ' . t('New Resume') }}
							</label>
						</div>
					</div>
					
					@include('account.resume._form', ['originForm' => 'message'])

					<!-- recaptcha -->
					@if (config('settings.security.recaptcha_activation'))
						<div class="form-group required <?php echo (isset($errors) and $errors->has('g-recaptcha-response')) ? 'has-error' : ''; ?>">
							<label class="control-label" for="g-recaptcha-response">{{ t('We do not like robots') }}</label>
							<div>
								{!! Recaptcha::render(['lang' => config('app.locale')]) !!}
							</div>
						</div>
					@endif
					
					<input type="hidden" name="country_code" value="{{ config('country.code') }}">
					<input type="hidden" name="post_id" value="{{ $post->id }}">
					<input type="hidden" name="messageForm" value="1">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{ t('Cancel') }}</button>
					<button type="submit" class="btn btn-success pull-right">{{ t('Send message') }}</button>
				</div>
			</form>
		</div>
	</div>
</div>
@section('after_styles')
	@parent
	<link href="{{ url('assets/plugins/bootstrap-fileinput/css/fileinput.min.css') }}" rel="stylesheet">
	@if (config('lang.direction') == 'rtl')
		<link href="{{ url('assets/plugins/bootstrap-fileinput/css/fileinput-rtl.min.css') }}" rel="stylesheet">
	@endif
	<style>
		.krajee-default.file-preview-frame:hover:not(.file-preview-error) {
			box-shadow: 0 0 5px 0 #666666;
		}
	</style>
@endsection

@section('after_scripts')
	@parent
	
	<script src="{{ url('assets/plugins/bootstrap-fileinput/js/plugins/sortable.min.js') }}" type="text/javascript"></script>
	<script src="{{ url('assets/plugins/bootstrap-fileinput/js/fileinput.min.js') }}" type="text/javascript"></script>
	@if (file_exists(public_path() . '/assets/plugins/bootstrap-fileinput/js/locales/'.config('app.locale').'.js'))
		<script src="{{ url('assets/plugins/bootstrap-fileinput/js/locales/'.config('app.locale').'.js') }}" type="text/javascript"></script>
	@endif
	<script>
		/* Resume */
		var lastResumeId = {{ old('resume_id', ((isset($lastResume) and \Storage::exists($lastResume->filename)) ? $lastResume->id : 0)) }};
		getResume(lastResumeId);
		
		$(document).ready(function () {
			@if (isset($errors) and $errors->any())
				@if ($errors->any() and old('messageForm')=='1')
					$('#applyJob').modal();
				@endif
			@endif
			
			/* Resume */
			$('#resumeId input').bind('click, change', function() {
				lastResumeId = $(this).val();
				getResume(lastResumeId);
			});
		});
	</script>
@endsection