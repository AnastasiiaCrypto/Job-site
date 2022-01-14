<!-- Modal Change Country -->
<div class="modal fade modalHasList" id="selectCountry" tabindex="-1" role="dialog" aria-labelledby="selectCountryLabel"
	 aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span>
					<span class="sr-only">{{ t('Close') }}</span>
				</button>
				<h4 class="modal-title uppercase font-weight-bold" id="selectCountryLabel">
					<i class="icon-map"></i> {{ t('Select your Country') }}
				</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="row" style="padding: 0 20px">
						@if (isset($countryCols))
							@foreach ($countryCols as $key => $col)
								<ul class="cat-list col-sm-3 col-xs-6">
									@foreach ($col as $k => $country)
										<?php
											$countryLang = App\Helpers\Localization\Country::getLangFromCountry($country->get('languages'));
										?>
									<li>
										<img src="{{ url('images/blank.gif') . getPictureVersion() }}" class="flag flag-{{ ($country->get('icode')=='uk') ? 'gb' : $country->get('icode') }}" style="margin-bottom: 4px; margin-right: 5px;">
										
										<a href="{{ localUrl($country, '', true) }}">
											{{ str_limit($country->get('name'), 100) }}
										</a>
									</li>
									@endforeach
								</ul>
							@endforeach
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- /.modal -->
