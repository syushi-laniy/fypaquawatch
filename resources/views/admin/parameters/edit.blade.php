@extends('admin.layouts.app')

@section('title', 'Edit Parameter')
@section('page_title', 'Parameters')
@section('page_subtitle', 'Update parameter details')

@section('content')
<div class="card card-shadow p-4">
    <div class="fw-semibold mb-3">Edit Parameter</div>

    <form method="post" action="{{ route('parameters.update', $parameter) }}">
        @csrf
        @method('put')
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">Parameter Name</label>
                <input class="form-control" type="text" name="name" value="{{ old('name', $parameter->name) }}" required>
            </div>
            <div class="col-12">
                <label class="form-label">Unit</label>
                <input class="form-control" type="text" name="unit" value="{{ old('unit', $parameter->unit) }}">
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="Active" {{ old('status', $parameter->status) === 'Active' ? 'selected' : '' }}>
                        Active
                    </option>
                    <option value="Inactive" {{ old('status', $parameter->status) === 'Inactive' ? 'selected' : '' }}>
                        Inactive
                    </option>
                </select>
            </div>
        </div>

        <div class="mt-3 d-flex gap-2">
            <button class="btn btn-primary" type="submit">Update</button>
            <a class="btn btn-outline-secondary" href="{{ route('parameters.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
