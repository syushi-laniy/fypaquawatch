@extends('admin.layouts.app')

@section('title', 'Products')
@section('page_title', 'Products')
@section('page_subtitle', 'Manage shop products and inventory')

@section('content')
<div class="card card-shadow p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div class="fw-semibold">Product List</div>
        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.products.create') }}">+ Add Product</a>
    </div>

    <form class="row g-2 align-items-end mb-3" method="GET" action="{{ route('admin.products.index') }}">
        <div class="col-12 col-md-5">
            <label class="form-label mb-1">Search</label>
            <input class="form-control" type="text" name="q" value="{{ request('q') }}"
                   placeholder="Name or category">
        </div>
        <div class="col-12 col-md-3">
            <label class="form-label mb-1">Status</label>
            <select class="form-select" name="status">
                <option value="">All</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="col-12 col-md-2">
            <label class="form-label mb-1">Stock</label>
            <select class="form-select" name="stock">
                <option value="">All</option>
                <option value="low" {{ request('stock') === 'low' ? 'selected' : '' }}>Low only</option>
            </select>
        </div>
        <div class="col-12 col-md-2 d-flex gap-2">
            <button class="btn btn-primary w-100" type="submit">Filter</button>
            <a class="btn btn-outline-secondary w-100" href="{{ route('admin.products.index') }}">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category ?? '-' }}</td>
                        <td>RM {{ number_format($product->price, 2) }}</td>
                        <td>
                            {{ $product->stock }}
                            @if($product->stock <= $product->low_stock_threshold)
                                <span class="badge text-bg-warning ms-1">Low</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge text-bg-{{ $product->is_active ? 'success' : 'secondary' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.products.edit', $product) }}">Edit</a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                      onsubmit="return confirm('Delete this product?');">
                                    @csrf
                                    @method('delete')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No products yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
