@extends('admin.layouts.app')

@section('title', 'Edit Variant: ' . $variant->name)

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <h1>Edit Variant: {{ $variant->name }}</h1>
        <a href="{{ route('admin.variants.index') }}" class="btn-back">← Back to Variants</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.variants.update', $variant) }}" method="POST" id="variantForm">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Variant Name <span class="required">*</span></label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $variant->name) }}"
                        required
                        autofocus
                        placeholder="e.g., Size, Color, Storage"
                    >
                    <small class="form-text">The name of the variant (e.g., Color, Size, Material)</small>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="slug">Slug</label>
                    <input
                        type="text"
                        name="slug"
                        id="slug"
                        class="form-control @error('slug') is-invalid @enderror"
                        value="{{ old('slug', $variant->slug) }}"
                        placeholder="Auto-generated from name"
                    >
                    <small class="form-text">URL-friendly identifier (auto-generated if empty)</small>
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="type">Input Type <span class="required">*</span></label>
                    <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                        <option value="">-- Select Type --</option>
                        <option value="select" {{ old('type', $variant->type) === 'select' ? 'selected' : '' }}>Dropdown (Select)</option>
                        <option value="radio" {{ old('type', $variant->type) === 'radio' ? 'selected' : '' }}>Radio Buttons</option>
                        <option value="checkbox" {{ old('type', $variant->type) === 'checkbox' ? 'selected' : '' }}>Checkboxes</option>
                        <option value="text" {{ old('type', $variant->type) === 'text' ? 'selected' : '' }}>Text Input</option>
                        <option value="number" {{ old('type', $variant->type) === 'number' ? 'selected' : '' }}>Number Input</option>
                        <option value="range" {{ old('type', $variant->type) === 'range' ? 'selected' : '' }}>Range Slider</option>
                    </select>
                    <small class="form-text">How this variant will be displayed to users</small>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea
                        name="description"
                        id="description"
                        rows="3"
                        class="form-control @error('description') is-invalid @enderror"
                        placeholder="Optional description for this variant"
                    >{{ old('description', $variant->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="placeholder">Placeholder Text</label>
                        <input
                            type="text"
                            name="placeholder"
                            id="placeholder"
                            class="form-control @error('placeholder') is-invalid @enderror"
                            value="{{ old('placeholder', $variant->placeholder) }}"
                            placeholder="e.g., Select a size"
                        >
                        <small class="form-text">Shown in empty input fields</small>
                        @error('placeholder')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label for="sort_order">Sort Order</label>
                        <input
                            type="number"
                            name="sort_order"
                            id="sort_order"
                            class="form-control @error('sort_order') is-invalid @enderror"
                            value="{{ old('sort_order', $variant->sort_order) }}"
                            min="0"
                        >
                        <small class="form-text">Display order (lower numbers first)</small>
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="help_text">Help Text</label>
                    <input
                        type="text"
                        name="help_text"
                        id="help_text"
                        class="form-control @error('help_text') is-invalid @enderror"
                        value="{{ old('help_text', $variant->help_text) }}"
                        placeholder="Additional guidance for users"
                    >
                    <small class="form-text">Helpful hint displayed below the input</small>
                    @error('help_text')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input
                            type="checkbox"
                            name="is_required"
                            id="is_required"
                            class="form-check-input"
                            value="1"
                            {{ old('is_required', $variant->is_required) ? 'checked' : '' }}
                        >
                        <label for="is_required" class="form-check-label">
                            Required Field
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input
                            type="checkbox"
                            name="is_active"
                            id="is_active"
                            class="form-check-input"
                            value="1"
                            {{ old('is_active', $variant->is_active) ? 'checked' : '' }}
                        >
                        <label for="is_active" class="form-check-label">
                            Active
                        </label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Variant</button>
                    <a href="{{ route('admin.variants.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete()" style="margin-left: auto;">Delete Variant</button>
                </div>
            </form>

            <form id="deleteForm" action="{{ route('admin.variants.destroy', $variant) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</div>

<style>
.required { color: #dc3545; }
.form-group { margin-bottom: 1.5rem; }
.form-control { width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #ced4da; border-radius: 4px; }
.form-control:focus { outline: none; border-color: #667eea; box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); }
.is-invalid { border-color: #dc3545; }
.invalid-feedback { color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem; }
.form-text { display: block; margin-top: 0.25rem; font-size: 0.875rem; color: #6c757d; }
.form-row { display: flex; gap: 1rem; }
.col-md-6 { flex: 1; }
.form-check { display: flex; align-items: center; }
.form-check-input { margin-right: 0.5rem; }
.form-actions { margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #dee2e6; display: flex; gap: 1rem; }
.btn { padding: 0.6rem 1.5rem; border-radius: 6px; border: none; cursor: pointer; text-decoration: none; display: inline-block; }
.btn-primary { background: #667eea; color: white; }
.btn-secondary { background: #6c757d; color: white; }
.btn-danger { background: #dc3545; color: white; }
.card-body { padding: 2rem; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.btn-back { padding: 0.5rem 1rem; background: #6c757d; color: white; text-decoration: none; border-radius: 4px; }
.alert-danger { background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; border: 1px solid #f5c6cb; }
</style>

<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete this variant? This will also delete all its items and remove it from any category assignments.')) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
@endsection
