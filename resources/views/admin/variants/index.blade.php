@extends('admin.layouts.app')

@section('title', 'Variants Management')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <h1>Variants</h1>
        <a href="{{ route('admin.variants.create') }}" class="btn-primary">Create Variant</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Filters -->
    <div class="filters-card">
        <form method="GET" id="filterForm">
            <div class="filter-row">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Search variants..." 
                    value="{{ request('search') }}"
                    class="filter-input"
                >
                
                <select name="type" class="filter-input">
                    <option value="">All Types</option>
                    <option value="select" {{ request('type') === 'select' ? 'selected' : '' }}>Select</option>
                    <option value="radio" {{ request('type') === 'radio' ? 'selected' : '' }}>Radio</option>
                    <option value="checkbox" {{ request('type') === 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                    <option value="text" {{ request('type') === 'text' ? 'selected' : '' }}>Text</option>
                    <option value="number" {{ request('type') === 'number' ? 'selected' : '' }}>Number</option>
                </select>

                <select name="is_active" class="filter-input">
                    <option value="">All Status</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>

                <button type="submit" class="btn-primary">Filter</button>
                <a href="{{ route('admin.variants.index') }}" class="btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    <!-- Variants Table -->
    <div class="card">
        <div id="variantsTableContainer">
            @include('admin.variants.partials.table', ['variants' => $variants])
        </div>
    </div>

    <!-- Pagination -->
    <div class="pagination-container">
        {{ $variants->links() }}
    </div>
</div>

<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.filters-card { background: #fff; padding: 1.5rem; margin-bottom: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.filter-row { display: flex; gap: 1rem; align-items: center; }
.filter-input { padding: 0.5rem 1rem; border: 1px solid #ddd; border-radius: 4px; }
.card { background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden; }
.btn-primary { padding: 0.6rem 1.5rem; background: #667eea; color: white; text-decoration: none; border-radius: 6px; border: none; cursor: pointer; }
.btn-secondary { padding: 0.6rem 1.5rem; background: #6c757d; color: white; text-decoration: none; border-radius: 6px; border: none; cursor: pointer; }
.alert { padding: 1rem; margin-bottom: 1.5rem; border-radius: 6px; }
.alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.pagination-container { margin-top: 1.5rem; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit filter form on change
    const filterForm = document.getElementById('filterForm');
    const filterInputs = filterForm.querySelectorAll('select');
    
    filterInputs.forEach(input => {
        input.addEventListener('change', () => filterForm.submit());
    });
});
</script>
@endsection