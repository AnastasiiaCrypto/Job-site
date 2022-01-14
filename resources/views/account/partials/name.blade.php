@if($transaction->activity_title == 'Payment Sent')
<br> <a href="#">{{__('To')}} {{$transaction->entity_name}}</a>
@elseif($transaction->activity_title == 'Payment Received')
<br> <a href="#">{{__('From')}} {{$transaction->entity_name}}</a>
@elseif($transaction->activity_title == 'Purchase')
<br> <a href="#">{{__('From')}} {{$transaction->entity_name}}</a>
@elseif($transaction->activity_title == 'Sale')
<br> <a href="#">{{__('In')}} {{$transaction->entity_name}} </a>
@elseif($transaction->activity_title == 'Currency Exchange')
<br> <a href="#"> @if($transaction->money_flow == '+') {{__('Exchanged To')}} @else {{__('Exchanged From')}} @endif {{$transaction->entity_name}}</a>
@endif
