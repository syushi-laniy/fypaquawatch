@extends('admin.layouts.app')

@section('title', 'Fish Analysis')
@section('page_title', 'Fish Analysis')
@section('page_subtitle', 'Review AI-assisted fish health assessments')

@section('content')
<div class="card card-shadow p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div class="fw-semibold">Analysis History</div>
        <form class="d-flex gap-2" method="GET" action="{{ route('admin.fish-analyses.index') }}">
            <input class="form-control form-control-sm" type="search" name="q" value="{{ request('q') }}"
                   placeholder="User, tank, result">
            <button class="btn btn-sm btn-primary" type="submit">Search</button>
            @if(request('q'))
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.fish-analyses.index') }}">Reset</a>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>User</th>
                    <th>Tank</th>
                    <th>Result</th>
                    <th>Status</th>
                    <th>Confidence</th>
                </tr>
            </thead>
            <tbody>
                @forelse($analyses as $analysis)
                    @php($healthy = strtoupper($analysis->status) === 'HEALTHY')
                    <tr>
                        <td class="text-nowrap">{{ $analysis->created_at->format('d M Y, h:i A') }}</td>
                        <td>{{ $analysis->user->name ?? 'Deleted user' }}</td>
                        <td>{{ $analysis->tank->name ?? 'Not linked' }}</td>
                        <td>{{ $analysis->disease_name }}</td>
                        <td>
                            <span class="badge text-bg-{{ $healthy ? 'success' : 'warning' }}">
                                {{ $analysis->status }}
                            </span>
                        </td>
                        <td>{{ $analysis->confidence_level }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No fish analyses found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($analyses->hasPages())
        <div class="mt-3">{{ $analyses->links() }}</div>
    @endif
</div>
@endsection
