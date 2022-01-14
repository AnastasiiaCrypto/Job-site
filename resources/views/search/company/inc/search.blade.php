<?php
use Illuminate\Support\Facades\Input;

// Keywords
$keywords = rawurldecode(Input::get('q'));
?>
<div class="container">
	<div class="search-row-wrapper">
		<div class="container">
			<?php $attr = ['countryCode' => config('country.icode')]; ?>
			<form id="seach" name="search" action="{{ lurl(trans('routes.v-companies-list', $attr), $attr) }}" method="GET">
				<div class="col-sm-10" style="padding-left: 1px; padding-right: 1px;">
					<input name="q" class="form-control keyword" type="text" placeholder="{{ t('Company Name') }}" value="{{ $keywords }}">
				</div>

				<div class="col-sm-2" style="padding-left: 1px; padding-right: 1px;">
					<button class="btn btn-block btn-primary"><i class="fa fa-search"></i></button>
				</div>
				{!! csrf_field() !!}
			</form>
		</div>
	</div>
	<!-- /.search-row  width: 24.6%;-->
</div>