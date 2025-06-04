<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    /**
     * Display a listing of transactions
     */
    public function index(): View
    {
        $transactions = Transaction::with('user')->latest()->paginate(15);
        return view('admin.transactions.index', compact('transactions'));
    }

    /**
     * Show the form for creating a new transaction
     */
    public function create(): View
    {
        return view('admin.transactions.create');
    }

    /**
     * Store a newly created transaction
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'invoice_id' => 'nullable|string',
        ]);

        Transaction::create($validated);

        return redirect()->route('admin.transactions.index')->with('success', 'Transaction created successfully');
    }

    /**
     * Display the specified transaction
     */
    public function show(string $id): View
    {
        $transaction = Transaction::with('user')->findOrFail($id);
        return view('admin.transactions.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified transaction
     */
    public function edit(string $id): View
    {
        $transaction = Transaction::findOrFail($id);
        return view('admin.transactions.edit', compact('transaction'));
    }

    /**
     * Update the specified transaction
     */
    public function update(Request $request, string $id)
    {
        $transaction = Transaction::findOrFail($id);
        
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'invoice_id' => 'nullable|string',
        ]);

        $transaction->update($validated);

        return redirect()->route('admin.transactions.index')->with('success', 'Transaction updated successfully');
    }

    /**
     * Remove the specified transaction
     */
    public function destroy(string $id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();
        
        return redirect()->route('admin.transactions.index')->with('success', 'Transaction deleted successfully');
    }
}
