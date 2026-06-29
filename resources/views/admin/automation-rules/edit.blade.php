@extends('admin.layouts.app')

@section('title', 'Edit Automation Rule')
@section('page_title', 'Automation Rules')
@section('page_subtitle', 'Update trigger and action details')

@section('content')
<div class="card card-shadow p-4">
    <div class="fw-semibold mb-3">Edit Automation Rule</div>

    <form method="post" action="{{ route('automation-rules.update', $rule) }}">
        @csrf
        @method('put')
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">Rule Name</label>
                <input class="form-control" type="text" name="name" value="{{ old('name', $rule->name) }}" required>
            </div>
            <div class="col-12">
                <label class="form-label">Trigger</label>
                <input class="form-control" type="text" name="trigger" value="{{ old('trigger', $rule->trigger) }}" required>
            </div>
            <div class="col-12">
                <label class="form-label">Action</label>
                <input class="form-control" type="text" name="action" value="{{ old('action', $rule->action) }}" required>
            </div>
        </div>

        <div class="mt-3 d-flex gap-2">
            <button class="btn btn-primary" type="submit">Update</button>
            <a class="btn btn-outline-secondary" href="{{ route('automation-rules.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
