<table class="data-table">
    <thead>
        <tr>
            <th style="width: 50px;">#</th>
            <th>Value</th>
            <th>Display Value</th>
            @if(in_array($variant->type, ['select', 'radio', 'checkbox']))
                <th>Preview</th>
            @endif
            <th>Sort Order</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($items as $item)
        <tr>
            <td>{{ $loop->iteration + ($items->currentPage() - 1) * $items->perPage() }}</td>
            <td>
                <strong>{{ $item->value }}</strong>
            </td>
            <td>
                {{ $item->display_value ?? '—' }}
            </td>
            @if(in_array($variant->type, ['select', 'radio', 'checkbox']))
                <td>
                    <div class="item-preview">
                        @if($item->color_code)
                            <span class="color-swatch" style="background-color: {{ $item->color_code }};" title="{{ $item->color_code }}"></span>
                        @endif
                        @if($item->image)
                            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->value }}" class="item-image">
                        @endif
                        @if(!$item->color_code && !$item->image)
                            —
                        @endif
                    </div>
                </td>
            @endif
            <td>{{ $item->sort_order }}</td>
            <td>
                @if($item->is_active)
                    <span class="badge badge-success">Active</span>
                @else
                    <span class="badge badge-danger">Inactive</span>
                @endif
            </td>
            <td>
                <div class="action-buttons">
                    <button class="btn-sm btn-warning" onclick="editItem({{ $item->id }})">✏️ Edit</button>
                    <button class="btn-sm btn-danger" onclick="deleteItem({{ $item->id }})">🗑️ Delete</button>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="{{ in_array($variant->type, ['select', 'radio', 'checkbox']) ? '7' : '6' }}" style="text-align: center; padding: 3rem; color: #666;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">📦</div>
                <p style="font-size: 1.1rem; margin-bottom: 0.5rem;">No items added yet</p>
                <p style="font-size: 0.9rem; color: #999;">Click "Add New Item" to create variant options</p>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

<style>
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { background: #f8f9fa; padding: 1rem; text-align: left; font-weight: 600; border-bottom: 2px solid #dee2e6; }
.data-table td { padding: 1rem; border-bottom: 1px solid #dee2e6; }
.item-preview { display: flex; align-items: center; gap: 0.5rem; }
.color-swatch { width: 30px; height: 30px; border-radius: 4px; border: 2px solid #ddd; display: inline-block; }
.item-image { width: 40px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; }
.badge { display: inline-block; padding: 0.25rem 0.6rem; border-radius: 4px; font-size: 0.85rem; font-weight: 500; }
.badge-success { background: #d4edda; color: #155724; }
.badge-danger { background: #f8d7da; color: #721c24; }
.action-buttons { display: flex; gap: 0.5rem; }
.btn-sm { padding: 0.4rem 0.8rem; font-size: 0.875rem; border-radius: 4px; border: none; cursor: pointer; }
.btn-warning { background: #ffc107; color: #000; }
.btn-danger { background: #dc3545; color: white; }
</style>

<script>
function editItem(itemId) {
    // Future implementation
    alert('Edit functionality will be implemented in the next phase');
}

function deleteItem(itemId) {
    if (confirm('Are you sure you want to delete this item?')) {
        // Future implementation
        alert('Delete functionality will be implemented in the next phase');
    }
}
</script>