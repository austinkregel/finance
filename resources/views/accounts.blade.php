@extends('layouts.app')

@section('content')
    <accounts :user="{{ auth()->user() }}"></accounts>
@endsection
