<table class="data-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Items Count</th>
            <th>Required</th>
            <th>Status</th>
            <th>Sort Order</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($variants as $variant)
        <tr>
            <td>
                <strong>{{ $variant->name }}</strong>
                <br>
                <small style="color: #666;">{{ $variant->slug }}</small>
            </td>
            <td>
                <span class="badge badge-{{ $variant->type }}">{{ ucfirst($variant->type) }}</span>
            </td>
            <td>
                <span class="badge badge-info">{{ $variant->items_count }} items</span>
            </td>
            <td>
                @if($variant->is_required)
                    <span class="badge badge-warning">Required</span>
                @else
                    <span class="badge badge-light">Optional</span>
                @endif
            </td>
            <td>
                @if($variant->is_active)
                    <span class="badge badge-success">Active</span>
                @else
                    <span class="badge badge-danger">Inactive</span>
                @endif
            </td>
            <td>{{ $variant->sort_order }}</td>
            <td>
                <div class="action-buttons">
                    <a href="{{ route('admin.variants.items.index', $variant) }}" class="btn-sm btn-primary" title="Manage Items">
                        📋 Items ({{ $variant->items_count }})
                    </a>
                    <a href="{{ route('admin.variants.items.create', $variant) }}" class="btn-sm btn-success" title="Add Item">
                        ➕ Add Item
                    </a>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align: center; padding: 2rem; color: #666;">
                No variants found. <a href="{{ route('admin.variants.create') }}">Create your first variant</a>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

<style>
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { background: #f8f9fa; padding: 1rem; text-align: left; font-weight: 600; border-bottom: 2px solid #dee2e6; }
.data-table td { padding: 1rem; border-bottom: 1px solid #dee2e6; }
.badge { display: inline-block; padding: 0.25rem 0.6rem; border-radius: 4px; font-size: 0.85rem; font-weight: 500; }
.badge-select { background: #e3f2fd; color: #1976d2; }
.badge-radio { background: #f3e5f5; color: #7b1fa2; }
.badge-checkbox { background: #e8f5e9; color: #388e3c; }
.badge-text { background: #fff3e0; color: #f57c00; }
.badge-number { background: #fce4ec; color: #c2185b; }
.badge-success { background: #d4edda; color: #155724; }
.badge-danger { background: #f8d7da; color: #721c24; }
.badge-warning { background: #fff3cd; color: #856404; }
.badge-info { background: #d1ecf1; color: #0c5460; }
.badge-light { background: #f8f9fa; color: #6c757d; }
.action-buttons { display: flex; gap: 0.5rem; }
.btn-sm { padding: 0.4rem 0.8rem; font-size: 0.875rem; border-radius: 4px; text-decoration: none; display: inline-block; border: none; cursor: pointer; }
.btn-primary { background: #667eea; color: white; }
.btn-success { background: #28a745; color: white; }
</style>