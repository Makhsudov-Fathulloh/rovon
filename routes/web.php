<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Backend\LogController;
use App\Http\Controllers\Backend\FileController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\Backend\OrderController;
use App\Http\Controllers\Backend\ShiftController;
use App\Http\Controllers\Backend\StageController;
use App\Http\Controllers\Backend\ExportController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\SectionController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\PreOrderController;
use App\Http\Controllers\Backend\SupplierController;
use App\Http\Controllers\Backend\TelegramController;
use App\Http\Controllers\Backend\OrderItemController;
use App\Http\Controllers\Backend\WarehouseController;
use App\Http\Controllers\Backend\CashReportController;
use App\Http\Controllers\Backend\RawMaterialController;
use App\Http\Controllers\Backend\ShiftOutputController;
use App\Http\Controllers\Backend\ShiftReportController;
use App\Http\Controllers\Backend\DefectReportController;
use App\Http\Controllers\Backend\ExchangeRateController;
use App\Http\Controllers\Backend\OrganizationController;
use App\Http\Controllers\Backend\PreOrderItemController;
use App\Http\Controllers\Backend\ProductReturnController;
use App\Http\Controllers\Backend\ProfitAndLossController;
use App\Http\Controllers\Backend\StageMaterialController;
use App\Http\Controllers\Backend\ExpenseAndIncomeController;
use App\Http\Controllers\Backend\ProductVariationController;
use App\Http\Controllers\Backend\ProductReturnItemController;
use App\Http\Controllers\Backend\ShiftOutputWorkerController;
use App\Http\Controllers\Backend\RawMaterialTransferController;
use App\Http\Controllers\Backend\RawMaterialVariationController;
use App\Http\Controllers\Backend\RawMaterialTransferItemController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Authenticate
Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('authenticate', [AuthController::class, 'authenticate'])->name('authenticate');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Telegram bot
Route::post('/telegram/webhook', [TelegramController::class, 'webhook']);

// Backend
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard')->middleware('role:Admin,Manager,Moderator,Developer');

    Route::resource('category', CategoryController::class)->middleware('role:Admin,Manager,Developer');

    Route::prefix('charts')->group(function () {
        Route::get('diagram', [AdminController::class, 'charts'])->name('charts.diagram')->middleware('role:Admin,Manager,Developer');
    });

    Route::post('/warehouse/add-count', [WarehouseController::class, 'addCount'])->name('warehouse.add-count');
    Route::get('/warehouse/{warehouse_id}/elements', [WarehouseController::class, 'list'])->name('warehouse.list')->middleware('role:Admin,Manager,Moderator,Developer');
    Route::middleware('role:Developer')->group(function () {
        Route::get('warehouse/create', [WarehouseController::class, 'create'])->name('warehouse.create');
        Route::delete('warehouse/{warehouse}', [WarehouseController::class, 'destroy'])->name('warehouse.destroy');
    });
    Route::resource('warehouse', WarehouseController::class)->except(['create', 'destroy'])->middleware('role:Admin,Manager,Moderator,Developer');

    Route::prefix('raw-material')->group(function () {
        Route::get('{raw_material_id}/variations', [RawMaterialVariationController::class, 'list'])->name('raw-material-variation.list');
        Route::get('{raw_material_id}/variations/create', [RawMaterialVariationController::class, 'create'])->name('raw-material-variation.create.custom');
        Route::post('{raw_material_id}/variations', [RawMaterialVariationController::class, 'store'])->name('raw-material-variation.store.custom');
    });
    Route::resource('raw-material', RawMaterialController::class)->middleware('role:Admin,Manager,Moderator,Developer');

    Route::post('raw-material-variation/{variation}/add-count', [RawMaterialVariationController::class, 'addCount'])->name('raw-material-variation.add-count');
    Route::resource('raw-material-variation', RawMaterialVariationController::class)->middleware('role:Admin,Manager,Moderator,Developer');

    Route::get('/raw-material-transfer/get-warehouses', [RawMaterialTransferController::class, 'getWarehouses'])->name('raw-material-transfer.get-warehouses');
    Route::get('/raw-material-transfer/raw-materials', [RawMaterialTransferController::class, 'getRawMaterials'])->name('raw-material-transfer.raw-materials');
    Route::resource('raw-material-transfer', RawMaterialTransferController::class)->middleware('role:Admin,Manager,Moderator,Developer');
    Route::get('/raw-material-transfer/{warehouse_id}/elements', [RawMaterialTransferItemController::class, 'list'])->name('raw-material-transfer-item.list')->middleware('role:Admin,Manager,Moderator,Developer');
    Route::resource('raw-material-transfer-item', RawMaterialTransferItemController::class)->middleware('role:Admin,Manager,Moderator,Developer');

    Route::prefix('product')->group(function () {
        Route::get('{product_id}/variations', [ProductVariationController::class, 'list'])->name('product-variation.list')->middleware('role:Admin,Manager,Moderator,Developer');
        Route::get('{product_id}/variations/create', [ProductVariationController::class, 'create'])->name('product-variation.create.custom')->middleware('role:Admin,Manager,Moderator,Developer');
        Route::post('{product_id}/variations', [ProductVariationController::class, 'store'])->name('product-variation.store.custom')->middleware('role:Admin,Manager,Moderator,Developer');
    });
    Route::resource('product', ProductController::class)->middleware('role:Admin,Manager,Moderator,Developer');

    Route::post('product-variation/{variation}/add-count', [ProductVariationController::class, 'addCount'])->name('product-variation.add-count')->middleware('role:Admin,Manager,Moderator,Developer');
    Route::resource('product-variation', ProductVariationController::class)->middleware('role:Admin,Manager,Moderator,Developer');

    Route::prefix('product-return')->group(function () {
        Route::get('{product_return_id}/items', [ProductReturnItemController::class, 'list'])->name('product-return-item.list')->middleware('role:Admin,Manager,Moderator,Developer');
    });
    Route::resource('product-return', ProductReturnController::class)->middleware('role:Admin,Manager,Moderator,Developer');

    Route::resource('product-return-item', ProductReturnItemController::class)->middleware('role:Admin,Manager,Moderator,Developer');


    Route::prefix('log')->group(function () {
        Route::get('/material', [LogController::class, 'material'])->name('log.material')->middleware('role:Admin,Manager,Moderator,Developer');
        Route::get('/product', [LogController::class, 'product'])->name('log.product')->middleware('role:Admin,Manager,Moderator,Developer');
    });

    Route::middleware('role:Developer')->group(function () {
        Route::get('organization/create', [OrganizationController::class, 'create'])->name('organization.create');
        Route::delete('organization/{organization}', [OrganizationController::class, 'destroy'])->name('organization.destroy');
    });
    Route::get('/organization/{organization}/sections', [OrganizationController::class, 'organizationSections'])->middleware('role:Admin,Manager,Moderator,Developer')->name('organization.sections');
    Route::resource('organization', OrganizationController::class)->except(['create', 'destroy'])->middleware('role:Admin,Manager,Moderator,Developer');

    Route::get('/section/by-organization/{organizationId}', [SectionController::class, 'getSections'])->name('section.byOrganization')->middleware('role:Admin,Manager,Moderator,Developer');
    Route::get('/section/{section_id}/shifts', [ShiftController::class, 'list'])->name('shift.list')->middleware('role:Admin,Manager,Moderator,Developer');
    Route::resource('section', SectionController::class)->middleware('role:Admin,Manager,Moderator,Developer');

    Route::get('shift/{shift}/outputs', [ShiftOutputController::class, 'list'])->name('shift-output.list')->middleware('role:Admin,Manager,Moderator,Developer');
    Route::get('shift/{shift}/outputs/create', [ShiftOutputController::class, 'create'])->name('shift-output.create.custom')->middleware('role:Admin,Manager,Moderator,Developer');
    Route::post('shift/{shift}/outputs', [ShiftOutputController::class, 'store'])->name('shift-output.store.custom')->middleware('role:Admin,Manager,Moderator,Developer');

    Route::resource('shift', ShiftController::class)->middleware('role:Admin,Manager,Moderator,Developer');

    Route::prefix('shift-output')->group(function () {
        Route::get('{shiftOutput}/edit', [ShiftOutputController::class, 'edit'])->name('shift-output.edit')->middleware('role:Admin,Manager,Moderator,Developer');
        Route::put('{shiftOutput}', [ShiftOutputController::class, 'update'])->name('shift-output.update')->middleware('role:Admin,Manager,Moderator,Developer');
    });
    Route::resource('shift-output', ShiftOutputController::class)->middleware('role:Admin,Manager,Moderator,Developer');

    Route::get('shift-output-worker/{shiftOutput}/workers', [ShiftOutputWorkerController::class, 'list'])->name('shift-output-worker.list')->middleware('role:Admin,Manager,Moderator,Developer');
    Route::resource('shift-output-worker', ShiftOutputWorkerController::class)->middleware('role:Admin,Manager,Moderator,Developer');

    Route::resource('defect-report', DefectReportController::class)->middleware('role:Admin,Manager,Moderator,Developer');

    Route::prefix('order')->group(function () {
        Route::get('{order_id}/items', [OrderItemController::class, 'list'])->name('order-item.list')->middleware('role:Admin,Manager,Developer');
        Route::get('{order}/items/create', [OrderItemController::class, 'create'])->name('order-item.create.custom')->middleware('role:Admin,Manager,Developer');
        Route::post('{order}/items', [OrderItemController::class, 'store'])->name('order-item.store.custom')->middleware('role:Admin,Manager,Developer');
        //        Route::get('{order}/items/{item}/edit', [OrderItemController::class, 'edit'])->name('order-item.edit')->middleware('role:Admin,Manager,Developer');
    });
    Route::get('order/create', [OrderController::class, 'create'])->name('order.create')->middleware('role:Admin,Manager,Moderator,Developer');
    Route::post('order', [OrderController::class, 'store'])->name('order.store')->middleware('role:Admin,Manager,Developer,Moderator');

    Route::resource('order', OrderController::class)->except(['create', 'store'])->middleware('role:Admin,Manager,Developer');
    Route::resource('order-item', OrderItemController::class)->middleware('role:Admin,Manager,Moderator,Developer');

    Route::prefix('pre-order')->group(function () {
        Route::get('{pre_order_id}/items', [PreOrderItemController::class, 'list'])->name('pre-order-item.list')->middleware('role:Admin,Manager,Moderator,Developer');;
        Route::post('{preOrder}/complete', [PreOrderController::class, 'complete'])->name('pre-order.complete')->middleware('role:Admin,Manager,Moderator,Developer');;
        Route::get('ajax/product', [PreOrderController::class, 'searchProduct'])->name('ajax.product');
    });

    Route::resource('pre-order', PreOrderController::class)->middleware('role:Admin,Manager,Moderator,Developer');;
    Route::resource('pre-order-item', PreOrderItemController::class)->middleware('role:Admin,Manager,Moderator,Developer');;

    Route::get('/expense-and-income/users-by-currency', [ExpenseAndIncomeController::class, 'getUsersByCurrency'])->name('expense-and-income.users-by-currency')->middleware('role:Admin,Manager,Moderator,Developer');
    Route::resource('expense-and-income', ExpenseAndIncomeController::class)->middleware('role:Admin,Manager,Moderator,Developer');

    Route::prefix('cash-report')->group(function () {
        Route::get('/', [CashReportController::class, 'index'])->name('cash-report.index')->middleware('role:Admin,Manager,Developer');
        Route::post('/open', [CashReportController::class, 'openDailyReport'])->name('cash-report.open')->middleware('role:Admin,Manager,Moderator,Developer');
        Route::post('/close', [CashReportController::class, 'closeDailyReport'])->name('cash-report.close')->middleware('role:Admin,Manager,Developer');
    });

    Route::post('supplier/{supplier}/item', [SupplierController::class, 'storeItem'])->name('supplier.item.store');
    Route::put('supplier-item/{item}', [SupplierController::class, 'updateItem'])->name('supplier.item.update');
    Route::delete('supplier-item/{item}', [SupplierController::class, 'destroyItem']) ->name('supplier.item.destroy');
    Route::resource('supplier', SupplierController::class)->except(['create']);

    Route::prefix('shift-report')->group(function () {
        Route::get('/', [ShiftReportController::class, 'index'])->name('shift-report.index')->middleware('role:Admin,Manager,Moderator,Developer');
        Route::post('/open', [ShiftReportController::class, 'openShiftReport'])->name('shift-report.open')->middleware('role:Admin,Manager,Moderator,Developer');
        Route::post('/close', [ShiftReportController::class, 'closeShiftReport'])->name('shift-report.close')->middleware('role:Admin,Manager,Moderator,Developer');
    });

    Route::get('/exchange-rates', [ExchangeRateController::class, 'index'])->name('exchange-rates.index');
    Route::post('/exchange-rates/update', [ExchangeRateController::class, 'update'])->name('exchange-rates.update');

    Route::resource('profit-and-loss', ProfitAndLossController::class)->middleware('role:Admin,Developer');

    Route::resource('file', FileController::class)->middleware('role:Admin,Manager,Moderator,Developer');

    Route::get('stage/by-section/{section}', [StageController::class, 'getPreStages'])->name('stage.bySection')->middleware('role:Admin,Manager,Moderator,Developer');;
    Route::resource('stage', StageController::class)->middleware('role:Admin,Manager,Developer');
    Route::resource('stage-material', StageMaterialController::class)->middleware('role:Admin,Manager,Developer');

    Route::prefix('user')->group(function () {
        Route::post('storeAjax', [UserController::class, 'storeAjax'])->name('user.storeAjax')->middleware('role:Admin,Manager,Moderator,Developer');
        Route::get('staff', [UserController::class, 'staff'])->name('user.staff')->middleware('role:Admin,Manager,Moderator,Developer');
    });
    Route::resource('user', UserController::class)->middleware('role:Admin,Manager,Moderator,Developer');

    Route::resource('role', RoleController::class)->middleware('role:Developer');

    Route::get('/export', [ExportController::class, 'export'])->name('export.file')->middleware('role:Admin,Manager,Moderator,Developer');
});


// Frontend

Route::get('/', function () {
    return redirect('/admin');
});

//Route::get('/', [\App\Http\Controllers\Frontend\SiteController::class, 'index'])->name('frontend');
