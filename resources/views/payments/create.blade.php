@extends('layouts.app')
@section('title', __('Submit Payment'))
@section('content')
<div class="mb-4"><div class="eyebrow">{{ __('Electronic payment') }}</div><h1 class="page-title">{{ __('Submit Payment') }}</h1><p class="text-muted mb-0">{{ __('Send your payment details and attach the bank or electronic payment receipt for verification.') }}</p></div>
<div class="row g-4"><div class="col-lg-8"><form action="{{ route('payments.store') }}" method="POST" enctype="multipart/form-data" class="card p-4">@csrf
<div class="row g-3"><div class="col-md-6"><label class="form-label">{{ __('Payment method') }}</label><select name="payment_method_id" class="form-select" required><option value="">{{ __('Choose a payment method') }}</option>@foreach($methods as $method)<option value="{{ $method->id }}">{{ $method->name }} — {{ $method->currency }} ({{ __($method->scope==='global'?'Global':'Local') }})</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label">{{ __('Amount') }}</label><input name="amount" type="number" step="0.01" min="0.01" class="form-control" required></div>
<div class="col-md-4"><label class="form-label">{{ __('Currency') }}</label><input name="currency" class="form-control" value="USD" maxlength="10" required></div>
<div class="col-md-8"><label class="form-label">{{ __('Reference') }}</label><input name="reference" class="form-control" placeholder="Transaction ID / reference"></div>
<div class="col-md-6"><label class="form-label">{{ __('Payer Name') }}</label><input name="payer_name" class="form-control"></div>
<div class="col-md-6"><label class="form-label">{{ __('Payer Account') }}</label><input name="payer_account" class="form-control"></div>
<div class="col-12"><label class="form-label">{{ __('Purpose') }}</label><input name="purpose" class="form-control"></div>
<div class="col-12"><label class="form-label">{{ __('Upload Receipt') }}</label><input name="receipt" type="file" accept="image/jpeg,image/png,image/webp,application/pdf" class="form-control" required><div class="form-text">{{ __('Receipt must be an image or PDF up to 5MB') }}</div></div></div>
<div class="d-flex gap-2 mt-4"><button class="btn btn-primary"><i class="bi bi-send"></i> {{ __('Submit for Verification') }}</button><a href="{{ route('payments.index') }}" class="btn btn-light">{{ __('Cancel') }}</a></div></form></div>
<div class="col-lg-4"><div class="card p-4"><h5 class="fw-bold">{{ __('Available payment accounts') }}</h5>@forelse($methods as $method)<div class="border rounded-3 p-3 mt-3"><div class="d-flex justify-content-between"><strong>{{ $method->name }}</strong><span class="badge text-bg-light">{{ __($method->scope==='global'?'Global':'Local') }}</span></div><div class="small text-muted mt-2">{{ $method->provider }}</div><div class="mt-2"><strong>{{ $method->account_name }}</strong><br>{{ $method->account_number }}<br>{{ $method->currency }}</div>@if($method->instructions)<p class="small text-muted mt-2 mb-0">{{ $method->instructions }}</p>@endif</div>@empty<p class="text-muted">{{ __('No payment methods available') }}</p>@endforelse</div></div></div>
@endsection
