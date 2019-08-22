@extends('layouts.app')

@section('content')
    <home :user="{{ auth()->user() }}"></home>
@endsection
        
