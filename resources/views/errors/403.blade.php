@extends('errors::illustrated-layout')

@section('title', __('Tidak Diizinkan!'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'Tidak Diizinkan!'))
