@extends('admin.layouts.app')

@section('title', 'Add Automation Rule')
@section('page_title', 'Automation Rules')
@section('page_subtitle', 'Create a new trigger and action')

@section('content')
<div class="card card-shadow p-4">
    <div class="fw-semibold mb-3">Add Automation Rule</div>

    <form method="post" action="{{ route('automation-rules.store') }}">
        @csrf
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">Rule Name</label>
                <input class="form-control" type="text" name="name" value="{{ old('name') }}"
                       placeholder="Example: Air Pump" required>
            </div>
            <div class="col-12">
                <label class="form-label">Trigger</label>
                <input class="form-control" type="text" name="trigger" value="{{ old('trigger') }}"
                       placeholder="Example: DO < 5 mg/L" required>
            </div>
            <div class="col-12">
                <label class="form-label">Action</label>
                <input class="form-control" type="text" name="action" value="{{ old('action') }}"
                       placeholder="Example: Notify Telegram" required>
            </div>
        </div>

        <div class="mt-3 d-flex gap-2">
            <button class="btn btn-primary" type="submit">Save</button>
            <a class="btn btn-outline-secondary" href="{{ route('automation-rules.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
