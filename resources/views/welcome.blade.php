@extends('layouts.app')

@section('content')
    <router-view :user="{{ auth()->check() ? auth()->user() : "null" }}"/>
@endsection

