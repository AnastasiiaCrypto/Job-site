<?php
// From Company's Form
$classLeftCol = 'col-sm-3';
$classRightCol = 'col-sm-9';

if (isset($originForm)) {
	// From User's Form
	if ($originForm == 'user') {
		$classLeftCol = 'col-md-3';
		$classRightCol = 'col-md-7';
	}
	
	// From Post's Form
	if ($originForm == 'post') {
		$classLeftCol = 'col-md-3';
		$classRightCol = 'col-md-8';
	}
} else {
	// Required var
	$originForm = null;
}
?>
<div id="resumeFields">
	
	@if ($originForm != 'message')
		@if (isset($resume) and !empty($resume))
			<!-- name -->
			<div class="form-group <?php echo (isset($errors) and $errors->has('resume.name')) ? 'has-error' : ''; ?>">
				<label class="{{ $classLeftCol }} control-label" for="resume.name">{{ t('Name') }}</label>
				<div class="{{ $classRightCol }}">
					<input name="resume[name]"
						   placeholder="{{ t('Name') }}"
						   class="form-control input-md"
						   type="text"
						   value="{{ old('resume.name', (isset($resume->name) ? $resume->name : '')) }}">
				</div>
			</div>
		@endif
		
		<!-- filename -->
		<div class="form-group <?php echo (isset($errors) and $errors->has('resume.filename')) ? 'has-error' : ''; ?>">
			<label class="{{ $classLeftCol }} control-label" for="resume.filename"> {{ t('Your resume') }} </label>
			<div class="{{ $classRightCol }}">
				<div class="mb10">
					<input id="resumeFilename" name="resume[filename]" type="file" class="file">
				</div>
				<p class="help-block">{{ t('File types: :file_types', ['file_types' => showValidFileTypes('file')]) }}</p>
				@if (isset($resume))
					@if (!empty($resume) and !empty($resume->filename) and \Storage::exists($resume->filename))
					<div>
						<a class="btn btn-default" href="{{ \Storage::url($resume->filename) }}" target="_blank">
							<i class="icon-attach-2"></i> {{ t('Download current Resume') }}
						</a>
					</div>
					@endif
				@endif
			</div>
		</div>
	@else
		<!-- filename -->
		<div {!! (config('lang.direction')=='rtl') ? 'dir="rtl"' : '' !!} class="form-group required <?php echo (isset($errors) and $errors->has('resume.filename')) ? 'has-error' : ''; ?>">
			<label for="resume.filename" class="control-label">{{ t('Resume File') }} </label>
			<input id="resumeFilename" name="resume[filename]" type="file" class="file">
			<p class="help-block">{{ t('File types: :file_types', ['file_types' => showValidFileTypes('file')]) }}</p>
			@if (!empty($resume) and \Storage::exists($resume->filename))
				<div>
					<a class="btn btn-default" href="{{ \Storage::url($resume->filename) }}" target="_blank">
						<i class="icon-attach-2"></i> {{ t('Download current Resume') }}
					</a>
				</div>
			@endif
		</div>
	@endif

</div>

@section('after_styles')
	@parent
	<style>
		#resumeFields .select2-container {
			width: 100% !important;
		}
	</style>
@endsection

@section('after_scripts')
	@parent
	<script>
		/* Initialize with defaults (resume) */
		$('#resumeFilename').fileinput(
		{
			language: '{{ config('app.locale') }}',
			@if (config('lang.direction') == 'rtl')
				rtl: true,
			@endif
			showPreview: false,
			allowedFileExtensions: {!! getUploadFileTypes('file', true) !!},
			showUpload: false,
			showRemove: false,
			maxFileSize: {{ (int)config('settings.upload.max_file_size', 1000) }}
		});
	</script>
@endsection