@if($deposit->Status->id == 1)
<button class="btn btn-sm  btn-outline-success">{{$deposit->Status->name}}</button>
@elseif($deposit->Status->id == 2)
<button class="btn btn-sm  btn-outline-danger">{{$deposit->Status->name}}</button>
@elseif($deposit->Status->id == 3)
<button class="btn btn-sm  btn-outline-info">{{$deposit->Status->name}}</button>
@elseif($deposit->Status->id == 4)
<button class="btn btn-sm  btn-outline-primary">{{$deposit->Status->name}}</button>
@endif