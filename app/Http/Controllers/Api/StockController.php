<?php

namespace App\Http\Controllers\Api;

use App\Stock;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StockController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'ticker' => 'required',
            'target_price' => 'required',
            'track_macd' => 'bool'
        ]);

        $stock = Stock::where('ticker', strtoupper($request->get('ticker')))->first();

        if(empty($stock)) {
            return response()->json([
                'ticker' => [
                    'No stock found by that ticker'
                ]
            ], 422);
        }

        auth()->user()->watchedStocks()->attach($stock->id, [
            'is_watched' => true
        ]);

        auth()->user()->alerts()->create([
            'stock_id' => $stock->id,
//            'user_stocks_id' => 1,
            'target_price' => $request->get('target_price'),
            'track_macd' => (int)$request->get('track_macd'),
        ]);

        return response()->json([
            'message' => 'Updated shit!'
        ]);
    }
    public function index()
    {
        return auth()->user()->watchedStocks;
    }
}
