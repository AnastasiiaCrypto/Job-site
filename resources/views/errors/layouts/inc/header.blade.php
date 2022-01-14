<?php
// Search parameters
$queryString = (request()->getQueryString() ? ('?' . request()->getQueryString()) : '');

// Check if the Multi-Countries selection is enabled
$multiCountriesIsEnabled = false;
$multiCountriesLabel = '';

// Logo Label
$logoLabel = '';
if (getSegment(1) != trans('routes.countries')) {
	$logoLabel = config('settings.app.app_name') . ((!empty(config('country.name'))) ? ' ' . config('country.name') : '');
}
?>
<div class="container">
	<div class="header">
		<nav class="navbar navbar-site navbar-default" role="navigation">
			<div class="container">
				<div class="navbar-header">
					{{-- Toggle Nav --}}
					<button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle" type="button">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					
					{{-- Country Flag (Mobile) --}}
					@if (getSegment(1) != trans('routes.countries'))
						@if (isset($multiCountriesIsEnabled) and $multiCountriesIsEnabled)
							@if (!empty(config('country.icode')))
								@if (file_exists(public_path().'/images/flags/24/'.config('country.icode').'.png'))
									<button class="flag-menu country-flag visible-xs btn btn-default hidden" href="#selectCountry" data-toggle="modal" >
										<img src="{{ url('images/flags/24/'.config('country.icode').'.png') . getPictureVersion() }}" style="float: left;">
										<span class="caret hidden-xs"></span>
									</button>
								@endif
							@endif
						@endif
					@endif
					
					{{-- Logo --}}
					<a href="{{ url(config('app.locale') . '/') }}" class="navbar-brand logo logo-title">
						<img src="{{ \Storage::url(config('settings.app.logo', 'app/default/logo.png')) . getPictureVersion() }}" style="width:auto; height:40px; float:left; margin:0 5px 0 0;"/>
					</a>
				</div>
				<div class="navbar-collapse collapse">
					<ul class="nav navbar-nav navbar-left">
						{{-- Country Flag --}}
						@if (getSegment(1) != trans('routes.countries'))
							@if (config('settings.geo_location.country_flag_activation'))
								@if (!empty(config('country.icode')))
									@if (file_exists(public_path().'/images/flags/32/'.config('country.icode').'.png'))
										<li class="flag-menu country-flag tooltipHere hidden-xs" data-toggle="tooltip" data-placement="{{ (config('lang.direction') == 'rtl') ? 'bottom' : 'right' }}">
											@if (isset($multiCountriesIsEnabled) and $multiCountriesIsEnabled)
												<a href="#selectCountry" data-toggle="modal">
													<img class="flag-icon" src="{{ url('images/flags/32/'.config('country.icode').'.png') . getPictureVersion() }}" style="float: left;">
													<span class="caret"></span>
												</a>
											@else
												<a style="cursor: default;">
													<img class="flag-icon" src="{{ url('images/flags/32/'.config('country.icode').'.png') . getPictureVersion() }}" style="float: left;">
												</a>
											@endif
										</li>
									@endif
								@endif
							@endif
						@endif
					</ul>
					<ul class="nav navbar-nav navbar-right">
						@if (!auth()->check())
							<li><a href="{{ url(config('app.locale') . '/' . trans('routes.login')) }}"><i class="icon-user fa"></i> {{ t('Log In') }}</a></li>
							<li><a href="{{ url(config('app.locale') . '/' . trans('routes.register')) }}"><i class="icon-user-add fa"></i> {{ t('Register') }}</a></li>
							<li class="postadd">
								<a class="btn btn-block btn-post btn-add-listing" href="{{ url(config('app.locale') . '/posts/create') }}">
									<i class="fa fa-plus-circle"></i> {{ t('Create a Job ad') }}
								</a>
							</li>
						@else
							<li>
								@if (app('impersonate')->isImpersonating())
									<a href="{{ route('impersonate.leave') }}">
										<i class="icon-logout hidden-sm"></i> {{ t('Leave') }}
									</a>
								@else
									<a href="{{ url(config('app.locale') . '/logout') }}">
										<i class="glyphicon glyphicon-off"></i> {{ t('Log Out') }}
									</a>
								@endif
							</li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<i class="icon-user fa"></i>
									<span>{{ auth()->user()->name }}</span>
									<i class="icon-down-open-big fa"></i>
								</a>
								<ul class="dropdown-menu user-menu">
									<li class="active"><a href="{{ url(config('app.locale') . '/account') }}"><i class="icon-home"></i> {{ t('Personal Home') }} </a></li>
									@if (in_array(auth()->user()->user_type_id, [1]))
										<li><a href="{{ url(config('app.locale') . '/account/companies') }}"><i class="icon-town-hall"></i> {{ t('My companies') }} </a></li>
										<li><a href="{{ url(config('app.locale') . '/account/my-posts') }}"><i class="icon-th-thumb"></i> {{ t('My ads') }} </a></li>
										<li><a href="{{ url(config('app.locale') . '/account/pending-approval') }}"><i class="icon-hourglass"></i> {{ t('Pending approval') }} </a></li>
										<li><a href="{{ url(config('app.locale') . '/account/archived') }}"><i class="icon-folder-close"></i> {{ t('Archived ads') }} </a></li>
										<li><a href="{{ url(config('app.locale') . '/account/conversations') }}"><i class="icon-mail-1"></i> {{ t('Conversations') }} </a></li>
										<li><a href="{{ url(config('app.locale') . '/account/transactions') }}"><i class="icon-money"></i> {{ t('Transactions') }} </a></li>
									@endif
									@if (in_array(auth()->user()->user_type_id, [2]))
										<li><a href="{{ url(config('app.locale') . '/account/resumes') }}"><i class="icon-attach"></i> {{ t('My resumes') }} </a></li>
										<li><a href="{{ url(config('app.locale') . '/account/favourite') }}"><i class="icon-heart"></i> {{ t('Favourite jobs') }} </a></li>
										<li><a href="{{ url(config('app.locale') . '/account/saved-search') }}"><i class="icon-star-circled"></i> {{ t('Saved searches') }} </a></li>
										<li><a href="{{ url(config('app.locale') . '/account/conversations') }}"><i class="icon-mail-1"></i> {{ t('Conversations') }} </a></li>
									@endif
								</ul>
							</li>
							@if (in_array(auth()->user()->user_type_id, [1]))
								<li class="postadd">
									<a class="btn btn-block btn-post btn-add-listing" href="{{ url(config('app.locale') . '/posts/create') }}">
										<i class="fa fa-plus-circle"></i> {{ t('Create a Job ad') }}
									</a>
								</li>
							@endif
						@endif

						@if (!empty(config('lang.abbr')))
							@if (count(LaravelLocalization::getSupportedLocales()) > 1)
								<!-- Language selector -->
								<li class="dropdown lang-menu">
									<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
										{{ strtoupper(config('app.locale')) }}
										<span class="caret"></span>
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
						@endif
					</ul>

				</div>
			</div>
		</nav>
	</div>
</div>