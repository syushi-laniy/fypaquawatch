@extends('admin.layouts.app')

@section('title', 'Add Product')
@section('page_title', 'Products')
@section('page_subtitle', 'Create a new product')

@section('content')
<div class="card card-shadow p-4">
    <div class="fw-semibold mb-3">Add Product</div>

    <form method="POST" action="{{ route('admin.products.store') }}">
        @csrf
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label">Name</label>
                <input class="form-control" type="text" name="name" required>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Category</label>
                <input class="form-control" type="text" name="category">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Price (RM)</label>
                <input class="form-control" type="number" name="price" step="0.01" min="0" required>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Stock</label>
                <input class="form-control" type="number" name="stock" min="0" required>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Low Stock Threshold (optional)</label>
                <input class="form-control" type="number" name="low_stock_threshold" min="0">
            </div>
            <div class="col-12">
                <label class="form-label">Image URL (optional)</label>
                <input class="form-control" type="text" name="image_url">
            </div>
            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="product-active" checked>
                    <label class="form-check-label" for="product-active">
                        Active
                    </label>
                </div>
            </div>
        </div>

        <div class="mt-3 d-flex gap-2">
            <button class="btn btn-primary" type="submit">Save</button>
            <a class="btn btn-outline-secondary" href="{{ route('admin.products.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
