<?php

namespace App\Http\Controllers\Backend;

use App\Models\ExchangeRates;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class ExchangeRateController extends Controller
{
    public function index()
    {
        $rates = ExchangeRates::orderBy('currency')->get();

        return view('backend.exchange-rates.index', compact('rates'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'currency' => 'required|string|max:3',
            'rate' => 'required|numeric|min:0',
        ]);

        ExchangeRates::updateOrCreate(
            ['currency' => strtoupper($validated['currency'])],
            ['rate' => $validated['rate']]
        );

        return redirect()->back()->with('success', 'Валюта курси янгиланди!');
    }
}
