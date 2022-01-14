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
}
?>
<div id="companyFields">
	<!-- name -->
	<div class="form-group required <?php echo (isset($errors) and $errors->has('company.name')) ? 'has-error' : ''; ?>">
		<label class="{{ $classLeftCol }} control-label" for="company.name">{{ t('Company Name') }} <sup>*</sup></label>
		<div class="{{ $classRightCol }}">
			<input name="company[name]"
				   placeholder="{{ t('Company Name') }}"
				   class="form-control input-md"
				   type="text"
				   value="{{ old('company.name', (isset($company->name) ? $company->name : '')) }}">
		</div>
	</div>

	<!-- logo -->
	<div class="form-group <?php echo (isset($errors) and $errors->has('company.logo')) ? 'has-error' : ''; ?>">
		<label class="{{ $classLeftCol }} control-label" for="company.logo"> {{ t('Logo') }} </label>
		<div class="{{ $classRightCol }}">
			<div {!! (config('lang.direction')=='rtl') ? 'dir="rtl"' : '' !!} class="file-loading mb10">
				<input id="logo" name="company[logo]" type="file" class="file">
			</div>
			<p class="help-block">{{ t('File types: :file_types', ['file_types' => showValidFileTypes('image')]) }}</p>
		</div>
	</div>

	<!-- description -->
	<div class="form-group required <?php echo (isset($errors) and $errors->has('company.description')) ? 'has-error' : ''; ?>">
		<label class="{{ $classLeftCol }} control-label" for="company.description">{{ t('Company Description') }} <sup>*</sup></label>
		<div class="{{ $classRightCol }}">
			<textarea class="form-control" name="company[description]" rows="10">{{ old('company.description', (isset($company->description) ? $company->description : '')) }}</textarea>
			<p class="help-block">{{ t('Describe the company') }} - ({{ t(':number characters maximum', ['number' => 1000]) }})</p>
		</div>
	</div>

	@if (isset($company) and !empty($company))
		<!-- country_code -->
		<div class="form-group required <?php echo (isset($errors) and $errors->has('company.country_code')) ? 'has-error' : ''; ?>">
			<label class="{{ $classLeftCol }} control-label" for="company.country_code">{{ t('Country') }}</label>
			<div class="{{ $classRightCol }}">
				<select id="countryCode" name="company[country_code]" class="form-control sselecter">
					<option value="0" {{ (!old('company.country_code') or old('company.country_code')==0) ? 'selected="selected"' : '' }}> {{ t('Select a country') }} </option>
					@foreach ($countries as $item)
						<option value="{{ $item->get('code') }}"
								{{ (old('company.country_code',
								(isset($company->country_code) ? $company->country_code : ((!empty(config('country.code'))) ? config('country.code') : 0)))==$item->get('code')) ? 'selected="selected"' : '' }}>
							{{ $item->get('name') }}
						</option>
					@endforeach
				</select>
			</div>
		</div>
	
		<!-- city_id -->
		<div class="form-group <?php echo (isset($errors) and $errors->has('company.city_id')) ? 'has-error' : ''; ?>">
			<label class="{{ $classLeftCol }} control-label" for="company.city_id">{{ t('City') }}</label>
			<div class="{{ $classRightCol }}">
				<select id="cityId" name="company[city_id]" class="form-control sselecter">
					<option value="0" {{ (!old('company.city_id') or old('company.city_id')==0) ? 'selected="selected"' : '' }}>
						{{ t('Select a city') }}
					</option>
				</select>
			</div>
		</div>
	
		<!-- address -->
		<div class="form-group <?php echo (isset($errors) and $errors->has('company.address')) ? 'has-error' : ''; ?>">
			<label class="{{ $classLeftCol }} control-label" for="company.address">{{ t('Address') }}</label>
			<div class="{{ $classRightCol }}">
				<div class="input-group">
					<span class="input-group-addon"><i class="icon-location"></i></span>
					<input name="company[address]" type="text"
						   class="form-control" placeholder=""
						   value="{{ old('company.address', (isset($company->address) ? $company->address : '')) }}">
				</div>
			</div>
		</div>
	
		<!-- phone -->
		<div class="form-group <?php echo (isset($errors) and $errors->has('company.phone')) ? 'has-error' : ''; ?>">
			<label class="{{ $classLeftCol }} control-label" for="company.phone">{{ t('Phone') }}</label>
			<div class="{{ $classRightCol }}">
				<div class="input-group">
					<span class="input-group-addon"><i class="icon-phone-1"></i></span>
					<input name="company[phone]" type="text"
						   class="form-control" placeholder=""
						   value="{{ old('company.phone', (isset($company->phone) ? $company->phone : '')) }}">
				</div>
			</div>
		</div>
	
		<!-- fax -->
		<div class="form-group <?php echo (isset($errors) and $errors->has('company.fax')) ? 'has-error' : ''; ?>">
			<label class="{{ $classLeftCol }} control-label" for="company.fax">{{ t('Fax') }}</label>
			<div class="{{ $classRightCol }}">
				<div class="input-group">
					<span class="input-group-addon"><i class="icon-print"></i></span>
					<input name="company[fax]" type="text"
						   class="form-control" placeholder=""
						   value="{{ old('company.fax', (isset($company->fax) ? $company->fax : '')) }}">
				</div>
			</div>
		</div>
	
		<!-- email -->
		<div class="form-group <?php echo (isset($errors) and $errors->has('company.email')) ? 'has-error' : ''; ?>">
			<label class="{{ $classLeftCol }} control-label" for="company.email">{{ t('Email') }}</label>
			<div class="{{ $classRightCol }}">
				<div class="input-group">
					<span class="input-group-addon"><i class="icon-mail"></i></span>
					<input name="company[email]" type="text"
						   class="form-control" placeholder=""
						   value="{{ old('company.email', (isset($company->email) ? $company->email : '')) }}">
				</div>
			</div>
		</div>
	
		<!-- website -->
		<div class="form-group <?php echo (isset($errors) and $errors->has('company.website')) ? 'has-error' : ''; ?>">
			<label class="{{ $classLeftCol }} control-label" for="company.website">{{ t('Website') }}</label>
			<div class="{{ $classRightCol }}">
				<div class="input-group">
					<span class="input-group-addon"><i class="icon-globe"></i></span>
					<input name="company[website]" type="text"
						   class="form-control" placeholder=""
						   value="{{ old('company.website', (isset($company->website) ? $company->website : '')) }}">
				</div>
			</div>
		</div>
	
		<!-- facebook -->
		<div class="form-group <?php echo (isset($errors) and $errors->has('company.facebook')) ? 'has-error' : ''; ?>">
			<label class="{{ $classLeftCol }} control-label" for="company.facebook">Facebook</label>
			<div class="{{ $classRightCol }}">
				<div class="input-group">
					<span class="input-group-addon"><i class="icon-facebook"></i></span>
					<input name="company[facebook]" type="text"
						   class="form-control" placeholder=""
						   value="{{ old('company.facebook', (isset($company->facebook) ? $company->facebook : '')) }}">
				</div>
			</div>
		</div>
	
		<!-- twitter -->
		<div class="form-group <?php echo (isset($errors) and $errors->has('company.twitter')) ? 'has-error' : ''; ?>">
			<label class="{{ $classLeftCol }} control-label" for="company.twitter">Twitter</label>
			<div class="{{ $classRightCol }}">
				<div class="input-group">
					<span class="input-group-addon"><i class="icon-twitter"></i></span>
					<input name="company[twitter]" type="text"
						   class="form-control" placeholder=""
						   value="{{ old('company.twitter', (isset($company->twitter) ? $company->twitter : '')) }}">
				</div>
			</div>
		</div>
	
		<!-- linkedin -->
		<div class="form-group <?php echo (isset($errors) and $errors->has('company.linkedin')) ? 'has-error' : ''; ?>">
			<label class="{{ $classLeftCol }} control-label" for="company.linkedin">Linkedin</label>
			<div class="{{ $classRightCol }}">
				<div class="input-group">
					<span class="input-group-addon"><i class="icon-linkedin"></i></span>
					<input name="company[linkedin]" type="text"
						   class="form-control" placeholder=""
						   value="{{ old('company.linkedin', (isset($company->linkedin) ? $company->linkedin : '')) }}">
				</div>
			</div>
		</div>
	
		<!-- googleplus -->
		<div class="form-group <?php echo (isset($errors) and $errors->has('company.googleplus')) ? 'has-error' : ''; ?>">
			<label class="{{ $classLeftCol }} control-label" for="company.googleplus">Google+</label>
			<div class="{{ $classRightCol }}">
				<div class="input-group">
					<span class="input-group-addon"><i class="icon-googleplus-rect"></i></span>
					<input name="company[googleplus]" type="text"
						   class="form-control" placeholder=""
						   value="{{ old('company.googleplus', (isset($company->googleplus) ? $company->googleplus : '')) }}">
				</div>
			</div>
		</div>
	
		<!-- pinterest -->
		<div class="form-group <?php echo (isset($errors) and $errors->has('company.pinterest')) ? 'has-error' : ''; ?>">
			<label class="{{ $classLeftCol }} control-label" for="company.pinterest">Pinterest</label>
			<div class="{{ $classRightCol }}">
				<div class="input-group">
					<span class="input-group-addon"><i class="icon-docs"></i></span>
					<input name="company[pinterest]" type="text"
						   class="form-control" placeholder=""
						   value="{{ old('company.pinterest', (isset($company->pinterest) ? $company->pinterest : '')) }}">
				</div>
			</div>
		</div>
	@endif
</div>

@section('after_styles')
	@parent
	<style>
		#companyFields .select2-container {
			width: 100% !important;
		}
		.file-loading:before {
			content: " {{ t('Loading') }}...";
		}
	</style>
@endsection

@section('after_scripts')
	@parent
	<script>
		/* Initialize with defaults (logo) */
		$('#logo').fileinput(
		{
			language: '{{ config('app.locale') }}',
			@if (config('lang.direction') == 'rtl')
				rtl: true,
			@endif
			showPreview: true,
			allowedFileExtensions: {!! getUploadFileTypes('image', true) !!},
			showUpload: false,
			showRemove: false,
			maxFileSize: {{ (int)config('settings.upload.max_file_size', 1000) }},
			@if (isset($company) and !empty($company->logo) and \Storage::exists($company->logo))
			initialPreview: [
				'{{ resize($company->logo, 'medium') }}'
			],
			initialPreviewAsData: true,
			initialPreviewFileType: 'image',
			/* Initial preview configuration */
			initialPreviewConfig: [
				{
					width: '120px'
				}
			],
			initialPreviewShowDelete: false
			@endif
		});
	</script>
	@if (isset($company) and !empty($company))
	<script>
		/* Translation */
		var lang = {
			'select': {
				'country': "{{ t('Select a country') }}",
				'admin': "{{ t('Select a location') }}",
				'city': "{{ t('Select a city') }}"
			}
		};

		/* Locations */
		var countryCode = '{{ old('company.country_code', (isset($company) ? $company->country_code : 0)) }}';
		var adminType = 0;
		var selectedAdminCode = 0;
		var cityId = '{{ old('company.city_id', (isset($company) ? $company->city_id : 0)) }}';
	</script>
	<script src="{{ url('assets/js/app/d.select.location.js') . vTime() }}"></script>
	@endif
@endsection