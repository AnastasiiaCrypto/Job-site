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
@endsection

@section('content')
	@include('common.spacer')
	<div class="main-container inner-page">
		<div class="container">
			<div class="section-content">
				<div class="row">

					@if (session('message'))
						<div class="alert alert-danger">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							{{ session('message') }}
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
					
					@include('home.inc.spacer')
					<h1 class="text-center title-1"><strong>{{ t('Sitemap') }}</strong></h1>
					<hr class="center-block small text-hr">
					
					<div class="container">
						<div class="col-lg-12 content-box layout-section">
							<div class="row row-featured row-featured-category">
								<div class="col-lg-12 box-title no-border">
									<div class="inner">
										<h2>
											<span class="title-3"><span style="font-weight: bold;">{{ t('List of Categories and Sub-categories') }}</span></span>
										</h2>
									</div>
								</div>
								
								<div style="clear: both;"></div>
								
								<div class="list-categories-children styled">
									@foreach ($cats as $key => $col)
										<div class="col-md-4 col-sm-4 {{ (count($cats) == $key+1) ? 'last-column' : '' }}">
											@foreach ($col as $iCat)
												
												<?php
													$randomId = '-' . substr(uniqid(rand(), true), 5, 5);
												?>
											
												<div class="cat-list">
													<h3 class="cat-title rounded">
														<?php $attr = ['countryCode' => config('country.icode'), 'catSlug' => $iCat->slug]; ?>
														<a href="{{ lurl(trans('routes.v-search-cat', $attr), $attr) }}">
															<i class="{{ $iCat->icon_class ?? 'icon-ok' }}"></i>
															{{ $iCat->name }} <span class="count"></span>
														</a>
														<span data-target=".cat-id-{{ $iCat->id . $randomId }}" data-toggle="collapse" class="btn-cat-collapsed collapsed">
														<span class="icon-down-open-big"></span>
													</span>
													</h3>
													<ul class="cat-collapse collapse in cat-id-{{ $iCat->id . $randomId }} long-list-home">
														@if (isset($subCats) and $subCats->has($iCat->tid))
															@foreach ($subCats->get($iCat->tid) as $iSubCat)
																<li>
																	<?php $attr =  ['countryCode' => config('country.icode'), 'catSlug' => $iCat->slug, 'subCatSlug' => $iSubCat->slug]; ?>
																	<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}">
																		{{ $iSubCat->name }}
																	</a>
																</li>
															@endforeach
														@endif
													</ul>
												</div>
											@endforeach
										</div>
									@endforeach
								</div>
							</div>
						</div>
					</div>
					
					@if (isset($cities))
						@include('home.inc.spacer')
						<div class="container">
							<div class="col-lg-12 content-box layout-section">
								<div class="row row-featured row-featured-category">
									<div class="col-lg-12 box-title no-border">
										<div class="inner">
											<h2>
											<span class="title-3">
												<span style="font-weight: bold;"><i class="icon-location-2"></i> {{ t('List of Cities in') }} {{ config('country.name') }}</span>
											</span>
											</h2>
										</div>
									</div>
									
									<div style="clear: both;"></div>
									
									<div class="list-categories-children">
										<ul>
											@foreach ($cities as $key => $cols)
												<ul class="cat-list col-xs-3 {{ ($cities->count() == $key+1) ? 'cat-list-border' : '' }}">
													@foreach ($cols as $j => $city)
														<li>
															<?php $attr = ['countryCode' => config('country.icode'), 'city' => slugify($city->name), 'id' => $city->id]; ?>
															<a href="{{ lurl(trans('routes.v-search-city', $attr), $attr) }}" title="{{ t('Free Ads') }} {{ $city->name }}">
																<strong>{{ $city->name }}</strong>
															</a>
														</li>
													@endforeach
												</ul>
											@endforeach
										</ul>
									</div>
								</div>
							</div>
						</div>
					@endif

				</div>
				@include('layouts.inc.social.horizontal')
			</div>
		</div>
	</div>
@endsection

@section('before_scripts')
	@parent
	<script>
		var maxSubCats = 15;
	</script>
@endsection

