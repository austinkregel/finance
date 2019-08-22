@extends('errors::minimal')

@section('title', __('Not Found'))
@section('code', '404')
@section('message', empty($exception->getMessage()) ? __('Not Found') : $exception->getMessage())
