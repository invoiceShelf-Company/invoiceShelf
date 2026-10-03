<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['method', 'submitter', 'reviewer'])->latest()->paginate(15);

        return view('payments.index', compact('payments'));
    }

    public function create()
    {
        $methods = PaymentMethod::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        return view('payments.create', compact('methods'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'max:10'],
            'reference' => ['nullable', 'string', 'max:255'],
            'payer_name' => ['nullable', 'string', 'max:255'],
            'payer_account' => ['nullable', 'string', 'max:255'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'receipt' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ]);

        $path = $request->file('receipt')->store('payment-receipts', 'public');

        Payment::create([
            ...collect($data)->except('receipt')->all(),
            'facility_id' => $request->user()->facility_id,
            'submitted_by' => $request->user()->id,
            'receipt_path' => $path,
            'status' => 'pending',
        ]);

        return redirect()->route('payments.index')->with('success', __('messages.payment_submitted'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['method', 'submitter', 'reviewer']);

        return view('payments.show', compact('payment'));
    }

    public function review(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'review_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $payment->update([
            'status' => $data['status'],
            'review_notes' => $data['review_notes'] ?? null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', __('messages.payment_reviewed'));
    }

    public function receipt(Payment $payment)
    {
        abort_unless($payment->receipt_path, 404);

        return Storage::disk('public')->download($payment->receipt_path);
    }
}
