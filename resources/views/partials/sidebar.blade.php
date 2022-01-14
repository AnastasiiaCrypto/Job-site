
    <div class="card mb-3 bg-dark text-white">
        <div class="card-header"><h4>Balance</h4></div>
		
        <div class="card-body">
            <h5>Available <span class="text-primary"> {{ Auth::user()->getAuthUserCurrency()->name }} </span></h5>
				
            <h1>{{ \App\Helpers\Money::instance()->value(Auth::user()->balance(), html_entity_decode(Auth::user()->getAuthUserCurrency()->symbol)) }}</h1>
        </div>
        <div class="card-footer">
            <div class="btn-group btn-group-justified" role="group" aria-label="...">
                <!--a class="btn btn-outline-light" href="{{route('withdrawal.form')}}">Withdrawal</a-->
                <a class="btn btn-outline-light" href="{{route('add.credit')}}" style="width: 100%;">Add Credit</a>
            </div>
        </div>
    </div>
    
    <!--div class="list-group">
       
        <a class="list-group-item list-group-item-action  active  " href="{{url('/')}}">{{__('Profile')}}</a>
        <a href="{{url('/')}}/my_tickets"  class="list-group-item list-group-item-action">Support</a>
        <a href="{{url('/')}}/mymerchants " class="list-group-item list-group-item-action">Developers API</a>
    </div-->
   
