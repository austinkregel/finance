@extends('layouts.app')

@section('content')
    <calendar :user="{{ auth()->user() }}"></calendar>
@endsection
