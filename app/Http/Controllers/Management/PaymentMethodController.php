<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $methods = PaymentMethod::orderBy('sort_order')->orderBy('name')->paginate(15);

        return view('payment-methods.index', compact('methods'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'provider' => ['nullable', 'string', 'max:255'],
            'scope' => ['required', 'in:local,global'],
            'account_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:255'],
            'currency' => ['required', 'string', 'max:10'],
            'instructions' => ['nullable', 'string', 'max:2000'],
        ]);
        PaymentMethod::create($data + ['is_active' => true]);

        return back()->with('success', __('messages.payment_method_saved'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'provider' => ['nullable', 'string', 'max:255'],
            'scope' => ['required', 'in:local,global'],
            'account_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:255'],
            'currency' => ['required', 'string', 'max:10'],
            'instructions' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $paymentMethod->update($data);

        return back()->with('success', __('messages.payment_method_saved'));
    }
}
