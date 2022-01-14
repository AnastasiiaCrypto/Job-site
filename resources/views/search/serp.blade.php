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
<?php
	$fullUrl = url(request()->getRequestUri());
    $tmpExplode = explode('?', $fullUrl);
    $fullUrlNoParams = current($tmpExplode);
?>
@extends('layouts.master')

@section('search')
	@parent
	@include('search.inc.form')
@endsection

@section('content')
	<div class="main-container">
		
		@include('search.inc.breadcrumbs')
		@include('search.inc.categories')
		<?php if (\App\Models\Advertising::where('slug', 'top')->count() > 0): ?>
			@include('layouts.inc.advertising.top', ['paddingTopExists' => true])
		<?php
			$paddingTopExists = false;
		else:
			if (isset($paddingTopExists) and $paddingTopExists) {
				$paddingTopExists = false;
			}
		endif;
		?>
		@include('common.spacer')
		
		<div class="container">

			<div class="row">
				@if (Session::has('flash_notification'))
					<div class="container" style="margin-bottom: -10px; margin-top: -10px;">
						<div class="row">
							<div class="col-lg-12">
								@include('flash::message')
							</div>
						</div>
					</div>
				@endif
			</div>

			<div class="row">
				
				<!-- Sidebar -->
				@include('search.inc.sidebar')
				
				<!-- Content -->
				<div class="col-sm-9 page-content col-thin-left">
					<div class="category-list">
						<div class="tab-box clearfix">

							<!-- Nav tabs -->
							<div class="col-lg-12 box-title no-border">
								<div class="inner">
									<h2>
										<small>{{ $count->get('all') }} {{ t('Jobs Found') }}</small>
									</h2>
								</div>
							</div>

							<!-- Mobile Filter bar -->
							<div class="mobile-filter-bar col-lg-12">
								<ul class="list-unstyled list-inline no-margin no-padding">
									<li class="filter-toggle">
										<a class="">
											<i class="icon-th-list"></i> {{ t('Filters') }}
										</a>
									</li>
									<li>
										<div class="dropdown">
											<a data-toggle="dropdown" class="dropdown-toggle"><i class="caret "></i>{{ t('Sort by') }}</a>
											<ul class="dropdown-menu">
												<li>
													<a href="{!! qsurl($fullUrlNoParams, request()->except(['orderBy', 'distance'])) !!}" rel="nofollow">
														{{ t('Sort by') }}
													</a>
												</li>
												<li>
													<a href="{!! qsurl($fullUrlNoParams, array_merge(request()->except('orderBy'), ['orderBy'=>'relevance'])) !!}" rel="nofollow">
														{{ t('Relevance') }}
													</a>
												</li>
												<li>
													<a href="{!! qsurl($fullUrlNoParams, array_merge(request()->except('orderBy'), ['orderBy'=>'date'])) !!}" rel="nofollow">
														{{ t('Date') }}
													</a>
												</li>
												@if (isset($isCitySearch) and $isCitySearch and \App\Helpers\DBTool::checkIfMySQLFunctionExists(config('larapen.core.distanceCalculationFormula')))
													@for($iDist = 0; $iDist <= config('settings.listing.search_distance_max', 500); $iDist += config('settings.listing.search_distance_interval', 50))
													<li>
														<a href="{!! qsurl($fullUrlNoParams, array_merge(request()->except('distance'), ['distance' => $iDist])) !!}" rel="nofollow">
															{{ t('Around :distance :unit', ['distance' => $iDist, 'unit' => unitOfLength()]) }}
														</a>
													</li>
													@endfor
												@endif
											</ul>

										</div>
									</li>
								</ul>
							</div>
							<div class="menu-overly-mask"></div>
							<!-- Mobile Filter bar End-->


							<div class="tab-filter hide-xs">
								<select id="orderBy" class="selecter" data-style="btn-select" data-width="auto">
									<option value="{!! qsurl($fullUrlNoParams, request()->except(['orderBy', 'distance'])) !!}">{{ t('Sort by') }}</option>
									<option{{ (request()->get('orderBy')=='relevance') ? ' selected="selected"' : '' }}
											value="{!! qsurl($fullUrlNoParams, array_merge(request()->except('orderBy'), ['orderBy'=>'relevance'])) !!}">
										{{ t('Relevance') }}
									</option>
									<option{{ (request()->get('orderBy')=='date') ? ' selected="selected"' : '' }}
											value="{!! qsurl($fullUrlNoParams, array_merge(request()->except('orderBy'), ['orderBy'=>'date'])) !!}">
										{{ t('Date') }}
									</option>
									@if (isset($isCitySearch) and $isCitySearch and \App\Helpers\DBTool::checkIfMySQLFunctionExists(config('larapen.core.distanceCalculationFormula')))
										@for($iDist = 0; $iDist <= config('settings.listing.search_distance_max', 500); $iDist += config('settings.listing.search_distance_interval', 50))
											<option{{ (request()->get('distance', config('settings.listing.search_distance_default', 100))==$iDist) ? ' selected="selected"' : '' }}
													value="{!! qsurl($fullUrlNoParams, array_merge(request()->except('distance'), ['distance' => $iDist])) !!}">
												{{ t('Around :distance :unit', ['distance' => $iDist, 'unit' => unitOfLength()]) }}
											</option>
										@endfor
									@endif
								</select>
							</div>
							<!--/.tab-filter-->

						</div>
						<!--/.tab-box-->

						<div class="listing-filter hidden-xs">
							<div class="pull-left col-sm-10 col-xs-12">
								<div class="breadcrumb-list text-center-xs">
									{!! (isset($htmlTitle)) ? $htmlTitle : '' !!}
								</div>
							</div>
							<div class="pull-right col-sm-2 col-xs-12 text-right text-center-xs listing-view-action">
								@if (!empty(\Illuminate\Support\Facades\Input::all()))
									<?php $attr = ['countryCode' => config('country.icode')]; ?>
									<a class="clear-all-button text-muted" href="{!! lurl(trans('routes.v-search', $attr), $attr) !!}">{{ t('Clear all') }}</a>
								@endif
							</div>
							<div style="clear:both;"></div>
						</div>
						<!--/.listing-filter-->

						<div class="adds-wrapper jobs-list">
							@include('search.inc.posts')
						</div>
						<!--/.adds-wrapper-->

						<div class="tab-box save-search-bar text-center">
							@if (request()->filled('q') and request()->get('q') != '' and $count->get('all') > 0)
								<a name="{!! qsurl($fullUrlNoParams, request()->except(['_token', 'location'])) !!}" id="saveSearch" count="{{ $count->get('all') }}">
									<i class="icon-star-empty"></i> {{ t('Save Search') }}
								</a>
							@else
								<a href="#"> &nbsp; </a>
							@endif
						</div>
					</div>

					<div class="pagination-bar text-center">
						{!! $paginator->appends(request()->query())->render() !!}
					</div>
					<!--/.pagination-bar -->

					@if (!auth()->check())
						<div class="post-promo text-center">
							<h2> {{ t('Looking for a job?') }} </h2>
							<h5> {{ t('Upload your Resume and easily apply to jobs from any device!') }} </h5>
							<a href="{{ lurl(trans('routes.register')) . '?type=2' }}" class="btn btn-border btn-post btn-add-listing">
								{{ t('Add your Resume') }} <i class="icon-attach"></i>
							</a>
						</div>
						<!--/.post-promo-->
					@endif

				</div>
				
				<div style="clear:both;"></div>

				<!-- Advertising -->
				@include('layouts.inc.advertising.bottom')

			</div>

		</div>
	</div>
@endsection

@section('modal_location')
	@parent
	@include('layouts.inc.modal.location')
@endsection

@section('after_scripts')
	<script>
        $(document).ready(function () {
			$('#postType a').click(function (e) {
				e.preventDefault();
				var goToUrl = $(this).attr('href');
				redirect(goToUrl);
			});
			$('#orderBy').change(function () {
				var goToUrl = $(this).val();
				redirect(goToUrl);
			});
		});
	</script>
@endsection
