@extends('admin.layouts.app')

@section('title', 'Edit Item: ' . $variantItem->value)

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div>
            <h1>Edit Item in "{{ $variant->name }}"</h1>
            <p style="color: #666; margin-top: 0.5rem;">Type: <strong>{{ ucfirst($variant->type) }}</strong></p>
        </div>
        <a href="{{ route('admin.variants.items.index', $variant) }}" class="btn-back">← Back to Items</a>
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
            <form action="{{ route('admin.variants.items.update', [$variant, $variantItem]) }}" method="POST" enctype="multipart/form-data" id="itemForm">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="value">Item Value <span class="required">*</span></label>
                    <input
                        type="text"
                        name="value"
                        id="value"
                        class="form-control @error('value') is-invalid @enderror"
                        value="{{ old('value', $variantItem->value) }}"
                        required
                        autofocus
                        placeholder="e.g., Small, Red, 64GB"
                    >
                    <small class="form-text">The actual value stored in the database</small>
                    @error('value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="display_value">Display Value</label>
                    <input
                        type="text"
                        name="display_value"
                        id="display_value"
                        class="form-control @error('display_value') is-invalid @enderror"
                        value="{{ old('display_value', $variantItem->display_value) }}"
                        placeholder="Optional formatted display text"
                    >
                    <small class="form-text">Human-friendly label (e.g., "Extra Large" instead of "XL")</small>
                    @error('display_value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if(in_array($variant->type, ['select', 'radio', 'checkbox']))
                    <div class="visual-options-section">
                        <h3 style="margin-bottom: 1rem; font-size: 1.1rem; color: #333;">Visual Options (Optional)</h3>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="color_code">Color Code</label>
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    <input
                                        type="color"
                                        name="color_code"
                                        id="color_code"
                                        value="{{ old('color_code', $variantItem->color_code ?? '#000000') }}"
                                        style="width: 60px; height: 40px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;"
                                    >
                                    <input
                                        type="text"
                                        id="color_code_text"
                                        class="form-control @error('color_code') is-invalid @enderror"
                                        value="{{ old('color_code', $variantItem->color_code) }}"
                                        placeholder="#000000"
                                        readonly
                                    >
                                </div>
                                <small class="form-text">Useful for color variants (e.g., Red, Blue)</small>
                                @error('color_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="image">Item Image</label>
                                @if($variantItem->image)
                                    <div style="margin-bottom: 0.5rem;">
                                        <img src="{{ asset('storage/' . $variantItem->image) }}" alt="{{ $variantItem->value }}"
                                             style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px; border: 2px solid #ddd;">
                                        <small style="display: block; color: #666; margin-top: 0.25rem;">Current image</small>
                                    </div>
                                @endif
                                <input
                                    type="file"
                                    name="image"
                                    id="image"
                                    class="form-control @error('image') is-invalid @enderror"
                                    accept="image/*"
                                >
                                <small class="form-text">Small icon/swatch (max 1MB, JPEG/PNG/WebP). Leave empty to keep current.</small>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="imagePreview" style="margin-top: 0.5rem;"></div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="form-group">
                    <label for="sort_order">Sort Order</label>
                    <input
                        type="number"
                        name="sort_order"
                        id="sort_order"
                        class="form-control @error('sort_order') is-invalid @enderror"
                        value="{{ old('sort_order', $variantItem->sort_order) }}"
                        min="0"
                    >
                    <small class="form-text">Display order (lower numbers appear first)</small>
                    @error('sort_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input
                            type="checkbox"
                            name="is_active"
                            id="is_active"
                            class="form-check-input"
                            value="1"
                            {{ old('is_active', $variantItem->is_active) ? 'checked' : '' }}
                        >
                        <label for="is_active" class="form-check-label">Active</label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Item</button>
                    <a href="{{ route('admin.variants.items.index', $variant) }}" class="btn btn-secondary">Cancel</a>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete()" style="margin-left: auto;">Delete Item</button>
                </div>
            </form>

            <form id="deleteForm" action="{{ route('admin.variants.items.destroy', [$variant, $variantItem]) }}" method="POST" style="display: none;">
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
.invalid-feedback { color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem; display: block; }
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
.page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; }
.btn-back { padding: 0.5rem 1rem; background: #6c757d; color: white; text-decoration: none; border-radius: 4px; display: inline-block; }
.alert-danger { background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; border: 1px solid #f5c6cb; }
.visual-options-section { background: #f8f9fa; padding: 1.5rem; border-radius: 6px; margin-bottom: 1.5rem; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorPicker = document.getElementById('color_code');
    const colorText = document.getElementById('color_code_text');
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');

    // Sync color picker with text input
    if (colorPicker && colorText) {
        colorPicker.addEventListener('input', function() {
            colorText.value = this.value;
        });
    }

    // Image preview
    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 1024 * 1024) {
                    alert('Image must be less than 1MB');
                    this.value = '';
                    imagePreview.innerHTML = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.innerHTML = `
                        <img src="${e.target.result}"
                             style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px; border: 2px solid #ddd;">
                    `;
                };
                reader.readAsDataURL(file);
            } else {
                imagePreview.innerHTML = '';
            }
        });
    }
});

function confirmDelete() {
    if (confirm('Are you sure you want to delete this variant item?')) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
@endsection
