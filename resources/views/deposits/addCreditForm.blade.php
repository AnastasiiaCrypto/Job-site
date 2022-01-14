
@extends('layouts.master')

@section('content')
	@include('common.spacer')
	<div class="main-container">
		<div class="container">
			<div class="row">
				<div class="col-sm-3 page-sidebar">
					@include('account.inc.sidebar')
				</div>
				<!--/.page-sidebar-->
				
				<div class="col-sm-9 page-content row">
				
				@include('partials.nav')
					
						<div class="col-sm-3 custom-sidebar" style="padding: 0;">
							@include('partials.sidebar')
						</div>
						
        <div class="col-md-9 " style="padding-right: 0">
            <h4>{{  __('Deposit Money') }}</h4>
            <hr>
            <div class="panel panel-default" style="border: unset;">

                <div class="panel-body">
                  <form action="{{route('post.credit')}}" method="post" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="form-group {{ $errors->has('merchant_site_url') ? ' has-error' : '' }}">
                          <div class="form-group">
                            <label for="deposit_method">{{  __('Deposit Currency')  }}</label>
                            <select class="form-control" id="deposit_currency" name="deposit_currency">
                              <option value="{{ Auth::user()->currency_id }}" data-value="{{ Auth::user()->currency_id}}" selected>{{ $currencyName }} </option>
							  
                              @foreach($currencies as $currency)
								
                                  <option value="{{$currency->id}}" data-value="{{$currency->id}}">{{$currency->name}}</option>
                             
                              @endforeach
                            </select>[ <span class="text-primary">{{$currencyCode}}</span> ]
                            @if ($errors->has('deposit_currency'))
                              <span class="help-block">
                                  <strong>{{ $errors->first('deposit_currency') }}</strong>
                              </span>
                          @endif
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group {{ $errors->has('merchant_site_url') ? ' has-error' : '' }}">
                          <div class="form-group">
                            <label for="deposit_method">{{__('Deposit Method')}}</label>
                            <select class="form-control" id="deposit_method" name="deposit_method">
                              @forelse($methods as $method)
                                  <option value="{{$method->id}}" @if($method->name == $current_method->name) selected @endif>{{$method->name}}</option>
                              @empty


                              @endforelse
                            </select>
                            @if ($errors->has('deposit_method'))
                              <span class="help-block">
                                  <strong>{{ $errors->first('deposit_method') }}</strong>
                              </span>
                          @endif
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-lg-12">
                        <label for="">{{__('How to proceed with')}} {{$current_method->name}} {{__('deposits')}} </label>
                        <div  class="alert alert-secondary" role="alert">
                            {!! $current_method->how_to !!}
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="row">
                      <div class="col">
                        <div class="form-group {{ $errors->has('message') ? ' has-error' : '' }}">
                          <label for="message">{{__('Message to the reviewer')}} </label>
                          <textarea name="message" id="message" cols="30" rows="10" class="form-control"></textarea>
                        </div>
                      </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="row">
                      <div class="col">
                        <div class="form-group {{ $errors->has('deposit_screenshot') ? ' has-error' : '' }}">
                          <label for="deposit_screenshot">{{$current_method->name}} {{__('Transaction Receipt Screenshot')}}</label>
                          <input type="file" class="form-control" id="deposit_screenshot" name="deposit_screenshot" value="{{ old('merchant_logo') }}" required>
                          @if ($errors->has('deposit_screenshot'))
                              <span class="help-block">
                                  <strong>{{ $errors->first('deposit_screenshot') }}</strong>
                              </span>
                          @endif
                        </div>
                      </div>
                      <div class="col mt-5 text-right">
                        <input type="submit" class="btn btn-outline-dark btn-lg float-right" value="{{__('Save Deposit')}}">
                      </div>
                    </div>
                    <div class="clearfix"></div>
                  </form>
                </div>
            </div>
        </div>
    </div>
</div>
			<!--/.row-->
		</div>
		<!--/.container-->
	</div>
	<!-- /.main-container -->
	
	<script>
$( "#deposit_method" )
  .change(function () {
    $( "#deposit_method option:selected" ).each(function() {
      window.location.replace("{{url('/')}}/addcredit/"+$(this).val());
    });
  });
  
  $( "#deposit_currency" )
  .change(function () {
    $( "#deposit_currency option:selected" ).each(function() {
      window.location.replace("{{url('/')}}/wallet/get/"+$(this).val());
    });
  })
</script>
@endsection
