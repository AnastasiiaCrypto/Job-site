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
  <form action="{{route('post.exchange')}}" method="post" id="exchange_form" enctype="multipart/form-data">
    <div class="row">
	
        <div class="col-sm-3 custom-sidebar" style="padding: 0;">
						@include('partials.sidebar')
					</div>
        <div class="col-md-6 " style="padding-right: 0">
            <div class="panel panel-default" style="border:unset;" id="withdrawal_form">
                <div class="panel-body">
                  
                    {{csrf_field()}}
                    <input type="hidden" name="exchange_id" value="{{$exchange->id}}">
                    <div class="row">
                      <div class="col">
                        <div class="form-group {{ $errors->has('merchant_site_url') ? ' has-error' : '' }}">
                          <div class="form-group aaa">
                            <label for="first_currency_id"><h5>{{__('One')}} ( <span class="text-primary">1</span> ) {{$firstCurrencyName}} <span class="text-primary">{{$firstCurrencySymbol}}</span></h5> </label>
                            <select class="form-control d-none" id="first_currency_id" name="first_currency_id">
                                  <option value="{{$firstCurrency}}" data-value="{{$firstCurrency}}" selected >{{$firstCurrencyName}}</option>
                            </select>
                            <div class="nav-item dropdown">
                                <a id="CurrencyNavbarDropdown" class="nav-link dropdown-toggle btn btn-xs btn-outline btn-dark text-white" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre=""> {{$firstCurrencyName}} <span class="badge badge-light">{{$firstCurrencyCode}}</span></a>
                                <div class="dropdown-menu" aria-labelledby="CurrencyNavbarDropdown">
								
                                   @foreach($firstCurrenciesExchages as $currency)								   
                                   @if($currency->first_currency_id != $firstCurrency)
                                  <a class="dropdown-item" href="{{url('/')}}/exchange/first/{{$currency->first_currency_id}}/second/"> 
                                  {{$currency->firstCurrencyData()->name}}
                                  </a>
                                  @endif
                                  
                                  @endforeach
                                </div>
                            </div>
                            @if ($errors->has('first_currency_id'))
                              <span class="help-block">
                                  <strong>{{ $errors->first('first_currency_id') }}</strong>
                              </span>
                          @endif
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col">
                        <div class="form-group {{ $errors->has('merchant_site_url') ? ' has-error' : '' }}">
                          <div class="form-group bbb">
                            <label for="second_currency_id"><h5>{{__('Exchanges')}} {{__('to')}} ( <span class="text-primary"> {{$exchange->exchanges_to_second_currency_value}} </span>) {{$secondCurrencyName}} <span class="text-primary">{{$secondCurrencySymbol}}</span></h5></label>
                            <select class="form-control d-none" id="second_currency_id" name="second_currency_id">
                                  <option value="{{$secondCurrency}}" data-value="{{$secondCurrency}}" selected>{{$secondCurrencyName}}</option>
                            </select>
                            <div class="nav-item dropdown">
                                <a id="CurrencyNavbarDropdown" class="nav-link dropdown-toggle btn btn-xs btn-outline btn-primary text-white" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre=""> {{$secondCurrencyName}} <span class="badge badge-light">{{$secondCurrencyCode}}</span></a>
                                <div class="dropdown-menu" aria-labelledby="CurrencyNavbarDropdown">
                                   @foreach($secondCurrenciesExchanges as $currency)
                                   @if($currency->second_currency_id != $secondCurrency)
                                  <a class="dropdown-item" href="{{url('/')}}/exchange/first/{{$firstCurrency}}/second/{{$currency->second_currency_id}}"> 
                                  {{$currency->secondCurrencyData()->name}}
                                  </a>
                                  @endif
                                   
                                  @endforeach
                                </div>
                            </div>
                            @if ($errors->has('second_currency_id'))
                              <span class="help-block">
                                  <strong>{{ $errors->first('second_currency_id') }}</strong>
                              </span>
                          @endif
                          </div>
                        </div>
                      </div>
                      
                    </div>
                    <div class="clearfix"></div>
                    <div class="row">
                      
                      <div class="col">
                        <div class="form-group {{ $errors->has('amount') ? ' has-error' : '' }}">
                           <label for="amount"><h5>{{__('Amount to be exchanged')}}</h5></label>
                           <input type="text" name="amount" class="form-control" value="0"  v-on:keyup="exchange" v-on:change="exchange">
                            @if ($errors->has('amount'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('amount') }}</strong>
                                </span>
                            @endif
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col">
                        <div class="form-group {{ $errors->has('amount') ? ' has-error' : '' }}">
                           <label for="amount"><h1 class=" mb-0 mt-0"><span class="text-primary" id="exchange-total">@{{total}}</span><small>{{$secondCurrencySymbol}}</small></h1></label>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-lg-12">
                        <input type="submit" class="btn btn-outline-secondary btn-block btn-lg pull-right" value="Exchange" style="    border: 2px solid #908888;">
                      </div>
                    </div>
                    <div class="clearfix"></div>
                 
                </div>
            </div>
        </div>
	
        <div class="col-md-3 custom-sidebar" style="padding-right: 0;">
          <div class="card mb-3 bg-primary text-white">
              <div class="card-header"><h5>{{__('Balance')}}</h5></div>

              <div class="card-body">
                  <h5>{{__('Available')}}<span class="text-dark"> {{$wallet->getCurrencyName() }} </span></h5>
                  <h1>{{ \App\Helpers\Money::instance()->value($wallet->amount, $wallet->currency_id) }} {{html_entity_decode( $wallet->getCurrencySymbol()) }}</h1>
              </div>
              <div class="card-footer">
                  <div class="nav-item dropdown" style="    border: 1px solid;">
                      <a id="CurrencyNavbarDropdown" style="width:100%" class="nav-link dropdown-toggle btn btn-xs btn-outline btn-primary text-white" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre="">  <span class="badge badge-light">{{$secondCurrencyCode}}</span></a>
                      <div class="dropdown-menu"  aria-labelledby="CurrencyNavbarDropdown">
                         @forelse($secondCurrenciesExchanges as $currency)
                         @if($currency->second_currency_id!= $secondCurrency)
                        <a class="dropdown-item" href="{{url('/')}}/exchange/first/{{$firstCurrency}}/second/{{$currency->second_currency_id}}"> 
                        {{$currency->secondCurrencyData()->name}}
                        </a>
                        @endif
                         @empty
                        @endforelse
                      </div>
                  </div>
              </div>
          </div>
        </div>
    </div>
     </form>



</div>
				<!--/.page-content-->
				
			</div>
			<!--/.row-->
		</div>
		<!--/.container-->
	</div>
	<!-- /.main-container -->
	
	<script>
$(document).ready(function() {
 

var withdrawal_form = new Vue({
	el: '#exchange_form',
	data:{
		total: 0,
		rate: {{$exchange->exchanges_to_second_currency_value}}
	},
	methods: {
		exchange : function(evt){ 
			this.total =  (evt.target.value * this.rate); 
		} 
	}
});
$( "#first_currency_id" )
  .change(function () {
    $( "#first_currency_id option:selected" ).each(function() {
      window.location.replace("{{url('/')}}/exchange/first/" +$(this).val()+"/second");
  });
});

$( "#second_currency_id" )
  .change(function () {
    $( "#second_currency_id option:selected" ).each(function() {
      window.location.replace(window.location.href+'/'+$(this).val());
  });
})

});
</script>
@endsection




