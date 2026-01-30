@extends('admin.layouts.app')

@section('title', 'Manage Variant Items: ' . $variant->name)

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div>
            <h1>{{ $variant->name }} Items</h1>
            <p style="color: #666; margin-top: 0.5rem;">
                Type: <strong>{{ ucfirst($variant->type) }}</strong> | 
                Status: <strong>{{ $variant->is_active ? 'Active' : 'Inactive' }}</strong>
            </p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('admin.variants.items.create', $variant) }}" class="btn-success">➕ Add New Item</a>
            <a href="{{ route('admin.variants.index') }}" class="btn-back">← Back to Variants</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div id="itemsTableContainer">
            @include('admin.variants.items.partials.table', ['items' => $items, 'variant' => $variant])
        </div>
    </div>

    <div class="pagination-container">
        {{ $items->links() }}
    </div>
</div>

<style>
.page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; }
.btn-success { padding: 0.6rem 1.5rem; background: #28a745; color: white; text-decoration: none; border-radius: 6px; display: inline-block; }
.btn-back { padding: 0.6rem 1.5rem; background: #6c757d; color: white; text-decoration: none; border-radius: 6px; display: inline-block; }
.alert-success { background: #d4edda; color: #155724; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; border: 1px solid #c3e6cb; }
.card { background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden; }
.pagination-container { margin-top: 1.5rem; }
</style>
@endsection