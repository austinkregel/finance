@component('mail::message')
# You budget {{ $budget->name }} has breached!

You set the amount ${{$budget->amount}}, but you have spent ${{$budget->total_spend}}
@endcomponent
