@extends('customer.layout.customer_app')

@section('title', 'Dashboard Pelanggan')

@section('content')
    {{-- Redirect or include the canonical dashboard view to avoid duplicates --}}
    @includeIf('customer.dashboard.index')
@endsection