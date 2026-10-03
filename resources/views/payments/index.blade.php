@extends('layouts.app')
@section('title', __('Payments'))
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4"><div><div class="eyebrow">{{ __('Payments & Verification') }}</div><h1 class="page-title">{{ __('Payment History') }}</h1></div><a href="{{ route('payments.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> {{ __('Submit Payment') }}</a></div>
<div class="card"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>{{ __('Payment ID') }}</th><th>{{ __('Payment method') }}</th><th>{{ __('Amount') }}</th><th>{{ __('Reference') }}</th><th>{{ __('Submitted by') }}</th><th>{{ __('Status') }}</th><th>{{ __('Date') }}</th><th>{{ __('Action') }}</th></tr></thead><tbody>
@forelse($payments as $payment)
<tr><td>#{{ $payment->id }}</td><td><strong>{{ $payment->method->name }}</strong><small class="d-block text-muted">{{ $payment->method->provider }}</small></td><td>{{ number_format($payment->amount,2) }} {{ $payment->currency }}</td><td>{{ $payment->reference ?: '—' }}</td><td>{{ $payment->submitter->name }}</td><td><span class="badge text-bg-{{ $payment->status==='approved'?'success':($payment->status==='rejected'?'danger':'warning') }}">{{ __($payment->status === 'pending' ? 'Pending' : ucfirst($payment->status)) }}</span></td><td>{{ $payment->created_at->format('Y-m-d H:i') }}</td><td><a class="btn btn-sm btn-light" href="{{ route('payments.show',$payment) }}">{{ __('Details') }}</a></td></tr>
@empty<tr><td colspan="8" class="text-center py-5 text-muted">{{ __('No payments found.') }}</td></tr>@endforelse
</tbody></table></div><div class="p-3">{{ $payments->links() }}</div></div>
@endsection
