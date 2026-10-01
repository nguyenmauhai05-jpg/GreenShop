@extends('admin.layouts.app')

@section('title', 'Dashboard - GreenShop Admin')
@section('page-title', 'Dashboard')

@section('styles')
    @vite('resources/css/admin/dashboard.css')
@endsection

@section('content')
<div class="admin-dashboard-page">
    @include('admin.dashboard.components.topbar')
    @include('admin.dashboard.components.stats')
    @include('admin.dashboard.components.revenue-stock')
    @include('admin.dashboard.components.top-products-activities')
</div>
@endsection
