<?php

namespace App\Http\Controllers\Backend;

use App\Models\User;
use App\Models\Order;
use App\Models\UserDebt;
use App\Models\ExchangeRates;
use App\Models\ProductVariation;
use App\Models\RawMaterialVariation;
use App\Services\StatusService;
use App\Models\ExpenseAndIncome;
use Illuminate\Routing\Controller as BaseController;

class AdminController extends BaseController
{
    public function index()
    {
        $lowMaterials = RawMaterialVariation::checkLowStock();
        $lowProducts = ProductVariation::checkLowStock();

        if (!empty($lowMaterials)) {
            $message = "Минимал микдордаги хомашёлар: <br>";
            $items = implode('<br>', $lowMaterials);
            $fullMessage = $message . $items;

            if (auth()->check() && in_array(auth()->user()->role->title, ['Admin', 'Manager', 'Moderator', 'Developer'])) {
                session()->flash('info', $fullMessage);
                session()->flash('large_screen', true);
            }
        }

        if (!empty($lowProducts)) {
            $message = "Минимал микдордаги махсулотлар: <br>";
            $items = implode('<br>', $lowProducts);
            $fullMessage = $message . $items;

            if (auth()->check() && in_array(auth()->user()->role->title, ['Admin', 'Manager', 'Moderator', 'Developer'])) {
                session()->flash('warning', $fullMessage);
                session()->flash('large_screen', true);
            }
        }

        $userCount = User::where('role_id', \App\Models\Role::where('title', 'Client')->value('id'))->count();
        $employeeCount = User::whereNotIn('role_id', \App\Models\Role::whereIn('title', ['Developer', 'Client'])->pluck('id'))->count();

        $exchangeRate = ExchangeRates::where('currency', 'USD')->value('rate');

        $expenseTotalUzs = ExpenseAndIncome::where('type', ExpenseAndIncome::TYPE_EXPENSE)->where('currency', StatusService::CURRENCY_UZS)->whereYear('created_at', now()->year)->sum('amount');
        $expenseTotalUsd = ExpenseAndIncome::where('type', ExpenseAndIncome::TYPE_EXPENSE)->where('currency', StatusService::CURRENCY_USD)->whereYear('created_at', now()->year)->sum('amount');

        $orderTotalUzs = Order::where('currency', StatusService::CURRENCY_UZS)->whereYear('created_at', now()->year)->sum('total_price');
        $orderTotalUsd = Order::where('currency', StatusService::CURRENCY_USD)->whereYear('created_at', now()->year)->sum('total_price');

        $userDebtUzs = UserDebt::where('currency', StatusService::CURRENCY_UZS)->sum('amount');
        $userDebtUsd = UserDebt::where('currency', StatusService::CURRENCY_USD)->sum('amount');

        return view('backend.index', compact([
            'userCount',
            'employeeCount',
            'exchangeRate',
            'expenseTotalUzs',
            'expenseTotalUsd',
            'orderTotalUzs',
            'orderTotalUsd',
            'userDebtUzs',
            'userDebtUsd',
        ]));
    }

    public function charts()
    {
        return view('backend.charts.diagram');
    }
}
