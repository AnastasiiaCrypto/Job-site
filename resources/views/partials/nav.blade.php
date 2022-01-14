@auth
<div class="row">
  <div class="col mb-5">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item {{ (Route::is('account.transactions') ? 'active' : '') }} ">
            <a class="nav-link" href="{{url('/')}}/account/transactions">{{__('Transactions')}}  </a>
          </li>
          <li class="nav-item">
             <a href="{{url('/')}}/exchange/first/0/second/0"  class="nav-link {{ (Route::is('exchange.form') ? 'active' : '') }}">{{__('Exchange')}}  </a>
          </li>
          <li class="nav-item">
            <a class="" href="#"></a>
             <a href="{{route('sendMoneyForm')}}" class="nav-link {{ (Route::is('sendMoneyForm') ? 'active' : '') }}">{{__('Send Money')}} @if(Route::is('sendMoneyForm'))<span class="sr-only">(current)</span>@endif</a>
          </li>
          
           <li class="nav-item">
            <a href="{{route('mydeposits')}}"  class="nav-link {{ (Route::is('mydeposits') ? 'active' : '') }}">{{__('My Deposits')}} @if(Route::is('mydeposits'))<span class="sr-only">(current)</span>@endif</a>
          
          </li>
           
        </ul>
        <ul class="navbar-nav ml-auto">
		
           @if(count(\App\Models\Currency::where('id', '!=', Auth::user()->currency_id)->get()))
             <li class="nav-item dropdown">
                <a id="CurrencyNavbarDropdown" class="nav-link dropdown-toggle btn btn-xs btn-outline btn-primary text-white" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                    {{ html_entity_decode(\App\Models\Currency::where('id', '=', Auth::user()->currency_id)->first()->symbol)}}
                </a>
                <div class="dropdown-menu" aria-labelledby="CurrencyNavbarDropdown">
                    @foreach(\App\Models\Currency::where('id', '!=', Auth::user()->currency_id)->get() as $currency )
                    <a class="dropdown-item" href="{{ url('/') }}/wallet/{{$currency->id}}" >
                        {{ $currency->name }}
                    </a>
					
                    @endforeach

                </div>
            </li>
            @endif
            
        </ul>
      </div>
    </nav>
  </div>
</div>
@endauth

