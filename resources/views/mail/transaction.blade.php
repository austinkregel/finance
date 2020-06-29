@component('mail::message')
# You were charged by {{ $transaction->name }}

We saw you were charged ${{ $transaction->amount }} by {{ $transaction->name }} to your account {{ $transaction->account->name }}
@endcomponent
