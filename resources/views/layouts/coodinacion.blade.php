{{-- Alias de transicion hacia layouts.coordinator --}}
@extends('layouts.coordinator')

@section('title')
    @yield('title', 'Coordinador')
@endsection

@section('dropdown-items')
    @yield('dropdown-items')
@endsection

@section('content')
    @yield('content')
@endsection

@section('styles')
    @yield('styles')
@endsection

@section('scripts')
    @yield('scripts')
@endsection
