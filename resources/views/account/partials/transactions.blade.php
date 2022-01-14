@if($transactions->total()>0)
  <div class="card border-light">
      <div class="card-header  bg-light">
        <h4>{{__('Recent Activity')}}</h4>
      </div>
      <div class="card-body">
          <table class="table table-striped"  style="margin-bottom: 0;">
            <thead>
              <tr>
                <th>{{__('Id')}}</th>
                <th>{{__('Date')}}</th>
                <th>{{__('Name')}}</th>
                <th>{{__('Gross')}}</th>
                <th>{{__('Fee')}}</th>
                <th>{{__('Net')}}</th>
                <th>{{__('Balance')}}</th>
                <th>{{__('Action')}}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($transactions as $transaction)
                  <tr>
                    <td>{{$transaction->id}}</td>
                    <td>{{$transaction->created_at->format('d M Y')}} <br> @include('account.partials.status')</td>
                    <td>{{$transaction->activity_title}} @include('account.partials.name')</td>
                    <td>{{$transaction->gross()}}</td>
                    <td>{{$transaction->fee()}}</td>
                    <td>{{$transaction->net()}}</td>
                    <td>{{$transaction->balance()}}</td>
                    <td><a href="#" class="button">view</a></td>
                  </tr>
             
              @endforeach
          </tbody>
          </table>
      </div>
      @if($transactions->LastPage() != 1)
        <div class="card-footer">
            {{$transactions->links()}}
        </div>
      @else
      @endif
  </div>
@endif