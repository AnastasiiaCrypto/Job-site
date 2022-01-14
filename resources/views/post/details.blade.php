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

<?php
// Phone
$phone = TextToImage::make($post->phone, IMAGETYPE_PNG, ['backgroundColor' => 'rgba(0,0,0,0.0)', 'color' => '#FFFFFF']);
$phoneLink = 'tel:' . $post->phone;
$phoneLinkAttr = '';
if (!auth()->check()) {
	if (config('settings.single.guests_can_apply_jobs') != '1') {
		$phone = t('Click to see');
		$phoneLink = '#quickLogin';
		$phoneLinkAttr = 'data-toggle="modal"';
	}
}

// Contact Recruiter URL
$applyJobURL = '#applyJob';
$applyLinkAttr = 'data-toggle="modal"';
if (!empty($post->application_url)) {
	$applyJobURL = $post->application_url;
	$applyLinkAttr = '';
}
if (!auth()->check()) {
	if (config('settings.single.guests_can_apply_jobs') != '1') {
		$applyJobURL = '#quickLogin';
		$applyLinkAttr = 'data-toggle="modal"';
	}
}
?>

@section('content')
	{!! csrf_field() !!}
	<input type="hidden" id="post_id" value="{{ $post->id }}">
	
	@if (Session::has('flash_notification'))
		@include('common.spacer')
		<?php $paddingTopExists = true; ?>
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					@include('flash::message')
				</div>
			</div>
		</div>
		<?php Session::forget('flash_notification.message'); ?>
	@endif
	
	<div class="main-container">
		
		<?php if (\App\Models\Advertising::where('slug', 'top')->count() > 0): ?>
			@include('layouts.inc.advertising.top', ['paddingTopExists' => (isset($paddingTopExists)) ? $paddingTopExists : false])
		<?php
			$paddingTopExists = false;
		endif;
		?>
		@include('common.spacer')

		<div class="container">
			<ol class="breadcrumb pull-left">
				<li><a href="{{ lurl('/') }}"><i class="icon-home fa"></i></a></li>
				<li><a href="{{ lurl('/') }}">{{ config('country.name') }}</a></li>
				@if (!empty($post->category->parent))
					<li>
						<?php $attr = ['countryCode' => config('country.icode'), 'catSlug' => $post->category->parent->slug]; ?>
						<a href="{{ lurl(trans('routes.v-search-cat', $attr), $attr) }}">
							{{ $post->category->parent->name }}
						</a>
					</li>
					@if ($post->category->parent->id != $post->category->id)
						<li>
							<?php $attr = ['countryCode' => config('country.icode'), 'catSlug' => $post->category->parent->slug, 'subCatSlug' => $post->category->slug]; ?>
							<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}">
								{{ $post->category->name }}
							</a>
						</li>
					@endif
				@else
					<li>
						<?php $attr = ['countryCode' => config('country.icode'), 'catSlug' => $post->category->slug]; ?>
						<a href="{{ lurl(trans('routes.v-search-cat', $attr), $attr) }}">
							{{ $post->category->name }}
						</a>
					</li>
				@endif
				<li class="active">{{ str_limit($post->title, 70) }}</li>
			</ol>
			<div class="pull-right backtolist">
				<a href="{{ URL::previous() }}">
					<i class="fa fa-angle-double-left"></i> {{ t('Back to Results') }}
				</a>
			</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="col-sm-9 page-content col-thin-right">
					<div class="inner inner-box ads-details-wrapper">
						<h2 class="enable-long-words">
							<strong>
								<?php $attr = ['slug' => slugify($post->title), 'id' => $post->id]; ?>
                                <a href="{{ lurl($post->uri, $attr) }}" title="{{ $post->title }}">
                                    {{ $post->title }}
                                </a>
                            </strong>
							<small class="label label-default adlistingtype">{{ t(':type Job', ['type' => $post->postType->name]) }}</small>
                            @if ($post->featured==1 and !empty($post->latestPayment))
								@if (isset($post->latestPayment->package) and !empty($post->latestPayment->package))
									<i class="icon-ok-circled tooltipHere" style="color: {{ $post->latestPayment->package->ribbon }};" title="" data-placement="right"
									   data-toggle="tooltip" data-original-title="{{ $post->latestPayment->package->short_name }}"></i>
								@endif
                            @endif
						</h2>
						<span class="info-row">
							<span class="date"><i class=" icon-clock"> </i> {{ $post->created_at_ta }} </span> -&nbsp;
							<span class="category">{{ (!empty($post->category->parent)) ? $post->category->parent->name : $post->category->name }}</span> -&nbsp;
							<span class="item-location"><i class="fa fa-map-marker"></i> {{ $post->city->name }} </span> -&nbsp;
							<span class="category">
								<i class="icon-eye-3"></i>&nbsp;
								{{ \App\Helpers\Number::short($post->visits) }} {{ trans_choice('global.count_views', getPlural($post->visits)) }}
							</span>
						</span>

						<div class="ads-details">
							<div class="row" style="padding-bottom: 20px;">
								<div class="ads-details-info jobs-details-info col-md-8 enable-long-words from-wysiwyg">
									<h5 class="list-title"><strong>{{ t('Job Details') }}</strong></h5>
									
									<!-- Description -->
                                    <div>
										{!! transformDescription($post->description) !!}
                                    </div>
									
									@if (!empty($post->company_description))
										<!-- Company Description -->
										<div style="margin-bottom: 50px;"></div>
										<h5 class="list-title"><strong>{{ t('Company Description') }}</strong></h5>
                                        <div>
										    {!! nl2br(createAutoLink(strCleaner($post->company_description))) !!}
                                        </div>
									@endif
								
									<!-- Tags -->
									@if (!empty($post->tags))
										<?php $tags = explode(',', $post->tags); ?>
										@if (!empty($tags))
											<div style="clear: both;"></div>
											<div class="tags">
												<h5 class="list-title"><strong>{{ t('Tags') }}</strong></h5>
												@foreach($tags as $iTag)
													<?php $attr = ['countryCode' => config('country.icode'), 'tag' => $iTag]; ?>
													<a href="{{ lurl(trans('routes.v-search-tag', $attr), $attr) }}">
														{{ $iTag }}
													</a>
												@endforeach
											</div>
										@endif
									@endif
								</div>
								<div class="col-md-4">
									<aside class="panel panel-body panel-details job-summery">
										<ul>
											@if (!empty($post->start_date))
											<li>
												<p class="no-margin">
													<strong>{{ t('Start Date') }}:</strong>&nbsp;
													{{ $post->start_date }}
												</p>
											</li>
											@endif
											<li>
												<p class="no-margin">
													<strong>{{ t('Company') }}:</strong>&nbsp;
													@if (!empty($post->company_id))
														<?php $attr = ['countryCode' => config('country.icode'), 'id' => $post->company_id]; ?>
														<a href="{!! lurl(trans('routes.v-search-company', $attr), $attr) !!}">
															{{ $post->company_name }}
														</a>
													@else
														{{ $post->company_name }}
													@endif
												</p>
											</li>
											<li>
												<p class="no-margin">
													<strong>{{ t('Salary') }}:</strong>&nbsp;
													@if ($post->salary_min > 0 or $post->salary_max > 0)
														@if ($post->salary_min > 0)
															{!! \App\Helpers\Number::money($post->salary_min) !!}
														@endif
														@if ($post->salary_max > 0)
															@if ($post->salary_min > 0)
																&nbsp;-&nbsp;
															@endif
															{!! \App\Helpers\Number::money($post->salary_max) !!}
														@endif
													@else
														{!! \App\Helpers\Number::money('--') !!}
													@endif
													@if (!empty($post->salaryType))
														{{ t('per') }} {{ $post->salaryType->name }}
													@endif
													
													@if ($post->negotiable == 1)
														<small class="label label-success"> {{ t('Negotiable') }}</small>
													@endif
												</p>
											</li>
											<li>
												<?php
													$postType = \App\Models\PostType::findTrans($post->post_type_id);
												?>
												@if (!empty($postType))
												<p class="no-margin">
													<strong>{{ t('Job Type') }}:</strong>&nbsp;
													<?php $attr = ['countryCode' => config('country.icode')]; ?>
													<a href="{{ lurl(trans('routes.v-search', $attr), $attr) . '?type[]=' . $post->post_type_id }}">
														{{ $postType->name }}
													</a>
												</p>
												@endif
											</li>
											<li>
												<p class="no-margin">
													<strong>{{ t('Location') }}:</strong>&nbsp;
													<?php $attr = [
															'countryCode' => config('country.icode'),
															'city'        => slugify($post->city->name),
															'id'          => $post->city->id
														]; ?>
													<a href="{!! lurl(trans('routes.v-search-city', $attr), $attr) !!}">
														{{ $post->city->name }}
													</a>
												</p>
											</li>
										</ul>
									</aside>
									<div class="ads-action">
										<ul class="list-border">
											@if (isset($post->company) and !empty($post->company))
												<li>
													<?php $attr = ['countryCode' => config('country.icode'), 'id' => $post->company->id]; ?>
													<a href="{{ lurl(trans('routes.v-search-company', $attr), $attr) }}">
														<i class="fa icon-town-hall"></i> {{ t('More jobs by :company', ['company' => $post->company->name]) }}
													</a>
												</li>
											@endif
											@if (isset($post->user) and !empty($post->user))
												<li>
													<?php $attr = ['countryCode' => config('country.icode'), 'id' => $post->user->id]; ?>
													<a href="{{ lurl(trans('routes.v-search-user', $attr), $attr) }}">
														<i class="fa fa-user"></i> {{ t('More jobs by :user', ['user' => $post->user->name]) }}
													</a>
												</li>
											@endif
											<li id="{{ $post->id }}">
												<a class="make-favorite">
												@if (auth()->check())
													@if (\App\Models\SavedPost::where('user_id', auth()->user()->id)->where('post_id', $post->id)->count() > 0)
														<i class="fa fa-heart"></i> {{ t('Saved Job') }}
													@else
														<i class="fa fa-heart-o"></i> {{ t('Save Job') }}
													@endif
												@else
													<i class="fa fa-heart-o"></i> {{ t('Save Job') }}
												@endif
                                                </a>
											</li>
											<li>
												<a href="{{ lurl('posts/' . $post->id . '/report') }}">
													<i class="fa icon-info-circled-alt"></i> {{ t('Report abuse') }}
												</a>
											</li>
										</ul>
									</div>
								</div>

								<br>&nbsp;<br>
							</div>
							<div class="content-footer text-left">
								@if (auth()->check())
									@if (auth()->user()->id == $post->user_id)
										<a class="btn btn-default" href="{{ lurl('posts/'.$post->id.'/edit') }}">
											<i class="fa fa-pencil-square-o"></i> {{ t('Edit') }}
										</a>
									@else
										@if ($post->email != '' and in_array(auth()->user()->user_type_id, [2]))
											<a class="btn btn-default" {!! $applyLinkAttr !!} href="{{ $applyJobURL }}">
												<i class="icon-mail-2"></i> {{ t('Apply Online') }}
											</a>
										@endif
									@endif
								@else
									@if ($post->email != '')
										<a class="btn btn-default" {!! $applyLinkAttr !!} href="{{ $applyJobURL }}">
											<i class="icon-mail-2"></i> {{ t('Apply Online') }}
										</a>
									@endif
								@endif
								@if ($post->phone_hidden != 1 and !empty($post->phone))
									<a href="{{ $phoneLink }}" {!! $phoneLinkAttr !!} class="btn btn-success showphone">
										<i class="icon-phone-1"></i>
										{!! $phone !!}{{-- t('View phone') --}}
									</a>
								@endif
								&nbsp;
							</div>
						</div>
					</div>
					<!--/.ads-details-wrapper-->
				</div>
				<!--/.page-content-->

				<div class="col-sm-3 page-sidebar-right">
					<aside>
						<div class="panel sidebar-panel panel-contact-seller">
							<div class="panel-heading">{{ t('Company Information') }}</div>
							<div class="panel-content user-info">
								<div class="panel-body text-center">
									<div class="seller-info">
										<div class="company-logo-thumb mb20">
											@if (isset($post->company) and !empty($post->company))
												<?php $attr = ['countryCode' => config('country.icode'), 'id' => $post->company->id]; ?>
												<a href="{{ lurl(trans('routes.v-search-company', $attr), $attr) }}">
													<img alt="Logo {{ $post->company_name }}" class="img-responsive" src="{{ resize($post->logo, 'big') }}">
												</a>
											@else
												<img alt="Logo {{ $post->company_name }}" class="img-responsive" src="{{ resize($post->logo, 'big') }}">
											@endif
										</div>
										@if (isset($post->company) and !empty($post->company))
											<h3 class="no-margin">
												<?php $attr = ['countryCode' => config('country.icode'), 'id' => $post->company->id]; ?>
												<a href="{{ lurl(trans('routes.v-search-company', $attr), $attr) }}">
													{{ $post->company->name }}
												</a>
											</h3>
										@else
											<h3 class="no-margin">{{ $post->company_name }}</h3>
										@endif
										<p>
											{{ t('Location') }}:&nbsp;
											<strong>
												<?php $attr = [
														'countryCode' => config('country.icode'),
														'city'        => slugify($post->city->name),
														'id'          => $post->city->id
													]; ?>
												<a href="{!! lurl(trans('routes.v-search-city', $attr), $attr) !!}">
													{{ $post->city->name }}
												</a>
											</strong>
										</p>
										@if ($post->user and !empty($post->user->created_at_ta))
											<p> {{ t('Joined') }}: <strong>{{ $post->user->created_at_ta }}</strong></p>
										@endif
										@if (isset($post->company) and !empty($post->company))
											@if (!empty($post->company->website))
												<p>
													{{ t('Web') }}:
													<strong>
														<a href="{{ $post->company->website }}" target="_blank" rel="nofollow">
															{{ getHostByUrl($post->company->website) }}
														</a>
													</strong>
												</p>
											@endif
										@endif
									</div>
									<div class="user-ads-action">
										@if (auth()->check())
											@if (auth()->user()->id == $post->user_id)
												<a href="{{ lurl('posts/' . $post->id . '/edit') }}" data-toggle="modal" class="btn btn-default btn-block">
													<i class="fa fa-pencil-square-o"></i> {{ t('Update the Details') }}
												</a>
												@if (isset($countPackages) and isset($countPaymentMethods) and $countPackages > 0 and $countPaymentMethods > 0)
													<a href="{{ lurl('posts/' . $post->id . '/payment') }}" data-toggle="modal" class="btn btn-success btn-block">
														<i class="icon-ok-circled2"></i> {{ t('Make It Premium') }}
													</a>
												@endif
											@else
												@if ($post->email != '' and in_array(auth()->user()->user_type_id, [2]))
													<a href="{{ $applyJobURL }}" {!! $applyLinkAttr !!} class="btn btn-default btn-block">
														<i class="icon-mail-2"></i> {{ t('Apply Online') }}
													</a>
												@endif
												@if ($post->phone_hidden != 1 and !empty($post->phone))
													<a href="{{ $phoneLink }}" {!! $phoneLinkAttr !!} class="btn btn-success btn-block showphone">
														<i class="icon-phone-1"></i>
														{!! $phone !!}{{-- t('View phone') --}}
													</a>
												@endif
											@endif
										@else
											@if ($post->email != '')
												<a href="{{ $applyJobURL }}" {!! $applyLinkAttr !!} class="btn btn-default btn-block">
													<i class="icon-mail-2"></i> {{ t('Apply Online') }}
												</a>
											@endif
											@if ($post->phone_hidden != 1 and !empty($post->phone))
												<a href="{{ $phoneLink }}" {!! $phoneLinkAttr !!} class="btn btn-success btn-block showphone">
													<i class="icon-phone-1"></i>
													{!! $phone !!}{{-- t('View phone') --}}
												</a>
											@endif
										@endif
									</div>
								</div>
							</div>
						</div>
						
						@if (config('settings.single.show_post_on_googlemap'))
							<div class="panel sidebar-panel">
								<div class="panel-heading">{{ t('Location\'s Map') }}</div>
								<div class="panel-content">
									<div class="panel-body text-left" style="padding: 0;">
										<div class="ads-googlemaps">
											<iframe id="googleMaps" width="100%" height="250" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src=""></iframe>
										</div>
									</div>
								</div>
							</div>
						@endif
						
						@if (isVerifiedPost($post))
							@include('layouts.inc.social.horizontal')
						@endif

						<div class="panel sidebar-panel">
							<div class="panel-heading">{{ t('Tips for candidates') }}</div>
							<div class="panel-content">
								<div class="panel-body text-left">
									<ul class="list-check">
										<li> {{ t('Check if the offer matches your profile') }} </li>
                                        <li> {{ t('Check the start date') }} </li>
										<li> {{ t('Meet the employer in a professional location') }} </li>
									</ul>
                                    <?php $tipsLinkAttributes = getUrlPageByType('tips'); ?>
									@if (!str_contains($tipsLinkAttributes, 'href="#"') and !str_contains($tipsLinkAttributes, 'href=""'))
									<p>
										<a class="pull-right" {!! $tipsLinkAttributes !!}>
											{{ t('Know more') }}
											<i class="fa fa-angle-double-right"></i>
										</a>
									</p>
                                    @endif
								</div>
							</div>
						</div>
					</aside>
				</div>
			</div>

		</div>
		
		@include('home.inc.featured', ['firstSection' => false])
		@include('layouts.inc.advertising.bottom', ['firstSection' => false])
		@if (isVerifiedPost($post))
			@include('layouts.inc.tools.facebook-comments', ['firstSection' => false])
		@endif
		
	</div>
@endsection

@section('modal_message')
	@if (auth()->check() or config('settings.single.guests_can_apply_jobs')=='1')
		@include('post.inc.compose-message')
	@endif
@endsection

@section('after_styles')
@endsection

@section('after_scripts')
    @if (config('services.googlemaps.key'))
        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.googlemaps.key') }}" type="text/javascript"></script>
    @endif
	
	<script>
		/* Favorites Translation */
        var lang = {
            labelSavePostSave: "{!! t('Save Job') !!}",
            labelSavePostRemove: "{{ t('Saved Job') }}",
            loginToSavePost: "{!! t('Please log in to save the Ads.') !!}",
            loginToSaveSearch: "{!! t('Please log in to save your search.') !!}",
            confirmationSavePost: "{!! t('Post saved in favorites successfully !') !!}",
            confirmationRemoveSavePost: "{!! t('Post deleted from favorites successfully !') !!}",
            confirmationSaveSearch: "{!! t('Search saved successfully !') !!}",
            confirmationRemoveSaveSearch: "{!! t('Search deleted successfully !') !!}"
        };

		$(document).ready(function () {
			@if (config('settings.single.show_post_on_googlemap'))
				/* Google Maps */
				getGoogleMaps(
				'{{ config('services.googlemaps.key') }}',
				'{{ (isset($post->city) and !empty($post->city)) ? addslashes($post->city->name) . ',' . config('country.name') : config('country.name') }}',
				'{{ config('app.locale') }}'
				);
			@endif
		})
	</script>
@endsection