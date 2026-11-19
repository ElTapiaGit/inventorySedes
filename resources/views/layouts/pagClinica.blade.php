{{-- Alias de transicion hacia layouts.coordinator con contexto de clinica --}}
@extends('layouts.coordinator')

@section('menu-context', 'clinica')

@section('title')
    @yield('title', 'Coordinador Clinica')
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
