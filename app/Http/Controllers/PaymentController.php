<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $q = Payment::with(['child','parent']);
        if ($cid = $request->query('child_id'))  $q->where('child_id', $cid);
        if ($pid = $request->query('parent_id')) $q->where('parent_id', $pid);
        if ($status = $request->query('status')) $q->where('status', $status);
        return response()->json($q->orderBy('due_date','desc')->paginate(15));
    }

    public function show(Payment $payment)
    {
        return response()->json($payment->load(['child','parent']));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'child_id'  => ['required','exists:children,id'],
            'parent_id' => ['required','exists:parents,id'],
            'amount'    => ['required','numeric','min:0.01'],
            'currency'  => ['nullable','string','max:8'],
            'method'    => ['required', Rule::in(['cash','card','bank'])],
            'status'    => ['required', Rule::in(['pending','paid','failed','refunded'])],
            'due_date'  => ['nullable','date'],
            'paid_at'   => ['nullable','date'],
            'reference' => ['nullable','string','max:100'],
            'notes'     => ['nullable','string'],
        ]);

        $payment = Payment::create($data);
        return response()->json($payment->load(['child','parent']), 201);
    }

    public function update(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'amount'    => ['sometimes','numeric','min:0.01'],
            'currency'  => ['nullable','string','max:8'],
            'method'    => ['sometimes', Rule::in(['cash','card','bank'])],
            'status'    => ['sometimes', Rule::in(['pending','paid','failed','refunded'])],
            'due_date'  => ['nullable','date'],
            'paid_at'   => ['nullable','date'],
            'reference' => ['nullable','string','max:100'],
            'notes'     => ['nullable','string'],
        ]);

        $payment->update($data);
        return response()->json($payment->load(['child','parent']));
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return response()->noContent();
    }
}
