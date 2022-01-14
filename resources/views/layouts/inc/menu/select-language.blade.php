@if (count(LaravelLocalization::getSupportedLocales()) > 1)
	<!-- Language selector -->
	<li class="dropdown lang-menu">
		<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
			{{ strtoupper(config('app.locale')) }}
			<span class="caret hidden-sm"> </span>
		</button>
		<ul class="dropdown-menu" role="menu">
			@foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
				@if (strtolower($localeCode) != strtolower(config('app.locale')))
					<?php
					// Controller Parameters
					$attr = [];
					$attr['countryCode'] = config('country.icode');
					if (isset($uriPathCatSlug)) {
						$attr['catSlug'] = $uriPathCatSlug;
						if (isset($uriPathSubCatSlug)) {
							$attr['subCatSlug'] = $uriPathSubCatSlug;
						}
					}
					if (isset($uriPathCityName) && isset($uriPathCityId)) {
						$attr['city'] = $uriPathCityName;
						$attr['id'] = $uriPathCityId;
					}
					if (isset($uriPathUserId)) {
						$attr['id'] = $uriPathUserId;
						if (isset($uriPathUsername)) {
							$attr['username'] = $uriPathUsername;
						}
					}
					if (isset($uriPathUsername)) {
						if (isset($uriPathUserId)) {
							$attr['id'] = $uriPathUserId;
						}
						$attr['username'] = $uriPathUsername;
					}
					if (isset($uriPathCompanyId)) {
						$attr['id'] = $uriPathCompanyId;
					}
					if (isset($uriPathTag)) {
						$attr['tag'] = $uriPathTag;
					}
					if (isset($uriPathPageSlug)) {
						$attr['slug'] = $uriPathPageSlug;
					}
					
					// Default
					// $link = LaravelLocalization::getLocalizedURL($localeCode, null, $attr);
					$link = lurl(null, $attr, $localeCode);
					$localeCode = strtolower($localeCode);
					?>
					<li>
						<a href="{{ $link }}" tabindex="-1" rel="alternate" hreflang="{{ $localeCode }}">
							{{{ $properties['native'] }}}
						</a>
					</li>
				@endif
			@endforeach
		</ul>
	</li>
@endif