@if($transaction->Status->id == 1)
<button class="btn btn-sm btn-outline-success">{{$transaction->Status->name}}</button>
@elseif($transaction->Status->id == 2)
<button class="btn btn-sm btn-outline-danger">{{$transaction->Status->name}}</button>
@elseif($transaction->Status->id == 3)
<button class="btn btn-sm btn-outline-info">{{$transaction->Status->name}}</button>
@elseif($transaction->Status->id == 4)
<button class="btn btn-sm btn-outline-primary">{{$transaction->Status->name}}</button>
@endif