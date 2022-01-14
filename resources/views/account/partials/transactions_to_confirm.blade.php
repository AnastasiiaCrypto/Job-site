
  <div class="panel panel-default">
	@if($transactions_to_confirm->total() > 0)
      <div class="panel-heading" style="border-bottom: 0; ">
        <div class="alert alert-info " style="margin-bottom:0">
          <button type="button" data-dismiss="alert" aria-label="Close" class="close">
            <span aria-hidden="true">×</span>
          </button>
          <strong>{{__('One more step...')}}</strong> {{__('please confirm your last transaction !')}}
        </div>
      </div>
	@endif
      <div class="panel-body">
          <table class="table" style="margin-bottom: 0">
            <thead>
              <tr>
                <th>#</th>
                <th>{{__('Date')}}</th>
                <th>{{__('time to expire')}}</th>
                <th>{{__('Name')}}</th>
                <th>{{__('Gross')}}</th>
                <th>{{__('Fee')}}</th>
                <th>{{__('Net')}}</th>
                <th>{{__('Action')}}</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
			@if($transactions_to_confirm->total() > 0)
              @foreach($transactions_to_confirm as $transaction)


                  <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{$transaction->created_at->format('d M Y')}} <br> {{$transaction->created_at->diffForHumans()}}</td>
                    <td>
                      @if($transaction->transactionable_type == 'App\Models\Send')
                      {{__('Funds')}} <br>{{__('Availability')}}
                      @elseif($transaction->transactionable_type == 'App\Models\Purchase')
                      5 Min
                      @endif
                    </td>
                    <td>{{$transaction->activity_title}} @include('account.partials.name')</a></td>
                    <td>{{$transaction->gross()}}</td>
                    <td>{{$transaction->fee()}}</td>
                    <td>{{$transaction->net()}}</td>
                    <td>
                      @if($transaction->transactionable_type == 'App\Models\Send')
                      <form action="{{route('sendMoneyConfirm')}}" method="post">
                      @elseif($transaction->transactionable_type == 'App\Models\Purchase')
                      <form action="{{route('purchaseConfirm')}}" method="post">
                      @endif
                      
                      {{csrf_field()}}
                      <input type="hidden" name="tid" value="{{$transaction->id}}">
                      <input type="submit"  class="btn btn-default btn-xs pull-left" value="confirm">
                      </form>
                      <div class="clearfix"></div>
                    </td>
                    <td>
                       @if($transaction->transactionable_type == 'App\Models\Send')
                      <form action="{{route('sendMoneyConfirm')}}" method="post">
                      @elseif($transaction->transactionable_type == 'App\Models\Purchase')
                      <form action="{{route('purchaseConfirm')}}" method="post">

                      @endif

                      {{csrf_field()}}
                      <input type="hidden" name="tid" value="{{$transaction->id}}">
                      <input type="submit"  class="btn btn-link btn-xs pull-right" value="X">
                      </form>
                    </td>
                  </tr>
             
              
             @endforeach
			 @endif
          </tbody>
          </table>
      </div>
	  @if($transactions_to_confirm->total() > 0)
		  @if($transactions_to_confirm->LastPage() != 1)
			<div class="panel-footer">
				{{$transactions->links()}}
			</div>
		  @else
		  @endif
	@endif
  </div>
