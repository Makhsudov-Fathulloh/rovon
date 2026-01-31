<?php

namespace App\Http\Controllers\Backend;

use App\Models\ExpenseAndIncome;
use App\Models\File;
use App\Models\Role;
use App\Models\User;
use App\Models\Order;
use App\Models\Shift;
use App\Models\Stage;
use App\Models\UserDebt;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\ExportService;
use App\Services\StatusService;
use App\Models\Search\UserSearch;
use App\Models\Search\OrderSearch;
use App\Services\DateFilterService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Models\Search\ShiftOutputWorkerSearch;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $searchModel = new UserSearch(new DateFilterService());
        $query = $searchModel->search($request)
            ->where('role_id', Role::where('title', 'Client')->value('id'));

        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = 'asc';
            if (Str::startsWith($sort, '-')) {
                $direction = 'desc';
                $sort = ltrim($sort, '-');
            }

            if (Schema::hasColumn('user', $sort)) {
                $query->orderBy($sort, $direction);
            }
        }

        $roleId = Role::where('title', 'Client')->value('id');
        $clients = User::where('role_id', $roleId)
            ->orderBy('id')
            ->pluck('username', 'id');

        $isFiltered = count($request->get('filters', [])) > 0;

        if ($isFiltered) {

        // Filterlangan user ID larni olish
        $filteredIds = $query->pluck('user.id');
        $userCount = $filteredIds->count();

        // Summalar
        $totalDebtUzs = UserDebt::whereIn('user_id', $filteredIds)
            ->where('currency', StatusService::CURRENCY_UZS)
            ->sum('amount');

        $totalDebtUsd = UserDebt::whereIn('user_id', $filteredIds)
            ->where('currency', StatusService::CURRENCY_USD)
            ->sum('amount');
        } else {
            $userCount = User::where('role_id', $roleId)->count();

            $totalDebtUzs = UserDebt::whereHas('user', function ($q) use ($roleId) {
                    $q->where('role_id', $roleId);
                })
                ->where('currency', StatusService::CURRENCY_UZS)
                ->sum('amount');

            $totalDebtUsd = UserDebt::whereHas('user', function ($q) use ($roleId) {
                    $q->where('role_id', $roleId);
                })
                ->where('currency', StatusService::CURRENCY_USD)
                ->sum('amount');
        }

        $users = $query->with('userDebt')->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('backend.user.index', compact(
            'users',
            'clients',
            'isFiltered',
            'userCount',
            'totalDebtUzs',
            'totalDebtUsd',
        ));
    }


    public function staff(Request $request)
    {
        $searchModel = new UserSearch(new DateFilterService());

        $clientId = Role::where('title', 'Client')->value('id');
        $developerId = Role::where('title', 'Developer')->value('id');
        $query = $searchModel->search($request)->whereNotIn('role_id', [$clientId, $developerId]);

        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = 'asc';
            if (Str::startsWith($sort, '-')) {
                $direction = 'desc';
                $sort = ltrim($sort, '-');
            }

            if (Schema::hasColumn('user', $sort)) {
                $query->orderBy($sort, $direction);
            }
        }

        $staffs = User::whereNotIn('role_id', [$clientId, $developerId])->orderBy('id')->pluck('username', 'id');
        $roles = Role::whereNotIn('title', ['Client', 'Developer'])->pluck('title', 'id');
        $staffId = Role::whereNotIn('title', ['Developer', 'Client'])->pluck('id');
        $isFiltered = count($request->get('filters', [])) > 0;

        if ($isFiltered) {
            // Filterlangan user ID larni olish
            $filteredIds = $query->pluck('user.id');

            $staffCount = $filteredIds->count();

            // Summalar
            $totalDebtUzs = UserDebt::whereIn('user_id', $filteredIds)
                ->where('currency', StatusService::CURRENCY_UZS)
                ->sum('amount');

            $totalDebtUsd = UserDebt::whereIn('user_id', $filteredIds)
                ->where('currency', StatusService::CURRENCY_USD)
                ->sum('amount');
        } else {
            $staffCount = User::whereIn('role_id', $staffId)->count();

            $totalDebtUzs = UserDebt::whereHas('user', function ($q) use ($staffId) {
                $q->whereIn('role_id', $staffId);
            })
                ->where('currency', StatusService::CURRENCY_UZS)
                ->sum('amount');

            $totalDebtUsd = UserDebt::whereHas('user', function ($q) use ($staffId) {
                $q->whereIn('role_id', $staffId);
            })
                ->where('currency', StatusService::CURRENCY_USD)
                ->sum('amount');
        }

        $users = $query->with('userDebt')->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('backend.user.staff', compact(
            'users',
            'staffs',
            'roles',
            'isFiltered',
            'staffCount',
            'totalDebtUzs',
            'totalDebtUsd',
        ));
    }


    public function show(User $user, Request $request, OrderSearch $orderSearch, ShiftOutputWorkerSearch $shiftOutputWorkerSearch)
    {
        $user->load('userDebt');

        $debts = $user->userDebt->groupBy('currency')->map(function ($items, $currency) {
            return [
                'currency' => $currency,
                'total_amount' => $items->sum('amount'),
            ];
        })->values();

        // Client
        if ($user->role && $user->role->title === 'Client') {

            $baseQuery = Order::where('user_id', $user->id);
            $query = $orderSearch->applyFilters(clone $baseQuery, $request);

            if ($request->filled('sort')) {
                $sort = $request->get('sort');
                $direction = 'asc';
                if (Str::startsWith($sort, '-')) {
                    $direction = 'desc';
                    $sort = ltrim($sort, '-');
                }
                if (Schema::hasColumn('order', $sort)) {
                    $query->orderBy($sort, $direction);
                }
            }

            $sellers = User::whereIn('id', Order::pluck('seller_id'))->orderBy('username')->pluck('username', 'id');

            // Filtirlangan yoki yo‘qligini tekshirish
            $isFiltered = !empty($request->get('filters', []));

            // Statistikani hisoblash
            $statsQuery = $isFiltered ? clone $query : clone $baseQuery;

            $orderCountUzs = (clone $statsQuery)->where('currency', StatusService::CURRENCY_UZS)->whereYear('created_at', now()->year)->count();
            $orderTotalPriceUzs = (clone $statsQuery)->where('currency', StatusService::CURRENCY_UZS)->whereYear('created_at', now()->year)->sum('total_price');
            $orderAmountPaidUzs = (clone $statsQuery)->where('currency', StatusService::CURRENCY_UZS)->whereYear('created_at', now()->year)->sum('total_amount_paid');
            $remainingDebtUzs = $debts->firstWhere('currency', StatusService::CURRENCY_UZS)['total_amount'] ?? 0;

            $orderCountUsd = (clone $statsQuery)->where('currency', StatusService::CURRENCY_USD)->whereYear('created_at', now()->year)->count();
            $orderTotalPriceUsd = (clone $statsQuery)->where('currency', StatusService::CURRENCY_USD)->whereYear('created_at', now()->year)->sum('total_price');
            $orderAmountPaidUsd = (clone $statsQuery)->where('currency', StatusService::CURRENCY_USD)->whereYear('created_at', now()->year)->sum('total_amount_paid');
            $remainingDebtUsd = $debts->firstWhere('currency', StatusService::CURRENCY_USD)['total_amount'] ?? 0;

            $exportQuery = clone $query;
            $exportOrders = $exportQuery->pluck('id')->toArray();

            $orders = $query->paginate(20)->withQueryString();

            return view('backend.user.show', compact(
                'user',
                'orders',
                'sellers',
                'orderCountUzs',
                'orderCountUsd',
                'orderTotalPriceUzs',
                'orderTotalPriceUsd',
                'orderAmountPaidUzs',
                'orderAmountPaidUsd',
                'remainingDebtUzs',
                'remainingDebtUsd',
                'debts',
                'exportOrders'
            ));
        }

        // Worker
        elseif ($user->role && $user->role->title === 'Worker') {
            $searchModel = new ShiftOutputWorkerSearch(new DateFilterService());
            $query = $searchModel->search($request);

            $query->where('user_id', $user->id);

            $sort = $request->get('sort');
            if (!empty($sort)) {
                $direction = 'asc';
                if (Str::startsWith($sort, '-')) {
                    $direction = 'desc';
                    $sort = ltrim($sort, '-');
                }

                if (Schema::hasColumn('shift_output_worker', $sort)) {
                    $query->orderBy($sort, $direction);
                } elseif ($sort === 'created_at') {
                    $query->orderBy('created_at', $direction);
                }
            }

            $shifts = Shift::whereHas('shiftOutputs.shiftOutputWorkers', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->orderBy('title')->pluck('title', 'id');

            $stages = Stage::whereHas('shiftOutputs.shiftOutputWorkers', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->orderBy('title')->pluck('title', 'id');

            // Filterlar tekshiruvi
            $isFiltered = count($request->get('filters', [])) > 0;

            if ($isFiltered) {
                // === 🔹 Kun bo‘yicha filter ===
                if (!empty($filters['created_at'])) {
                    $date = Carbon::parse($filters['created_at']);

                    $filteredQuery = (clone $query)->whereDate('created_at', $date->format('Y-m-d'));

                    $totalStageCount = $filteredQuery->sum('stage_count');
                    $totalDefectAmount = $filteredQuery->sum('defect_amount');
                    $totalPrice = $filteredQuery->sum('price');

                    // === 🔹 Oy bo‘yicha filter ===
                } elseif (!empty($filters['created_at'])) {
                    $date = Carbon::createFromFormat('m-Y', $filters['created_at']);

                    $filteredQuery = (clone $query)
                        ->whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month);

                    $totalStageCount = $filteredQuery->sum('stage_count');
                    $totalDefectAmount = $filteredQuery->sum('defect_amount');
                    $totalPrice = $filteredQuery->sum('price');

                } else {
                    $totalStageCount = $query->sum('stage_count');
                    $totalDefectAmount = $query->sum('defect_amount');
                    $totalPrice = $query->sum('price');
                }
            } else {
                $filteredQuery = (clone $query)->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month);

                $totalStageCount = $filteredQuery->sum('stage_count');
                $totalDefectAmount = $filteredQuery->sum('defect_amount');
                $totalPrice = $filteredQuery->sum('price');
            }

            $exportQuery = clone $query;
            $exportShiftOutputWorkers = $exportQuery->pluck('id')->toArray();

            $shiftOutputWorkers = $query->with('user.userDebt')->paginate(20)->withQueryString();

            return view('backend.user.show', compact(
                'user',
                'shifts',
                'stages',
                'shiftOutputWorkers',
                'totalStageCount',
                'totalDefectAmount',
                'totalPrice',
                'debts',
                'exportShiftOutputWorkers',
            ));
        }

        // Moderator
        if (Auth::user()->role->title == 'Moderator') {
            if ($user->role->title == 'worker' || $user->id == Auth::id()) {
                $searchModel = new ShiftOutputWorkerSearch(new DateFilterService());
                $query = $searchModel->search($request);

                $query->where('user_id', $user->id);

                $sort = $request->get('sort');
                if (!empty($sort)) {
                    $direction = 'asc';
                    if (Str::startsWith($sort, '-')) {
                        $direction = 'desc';
                        $sort = ltrim($sort, '-');
                    }

                    if (Schema::hasColumn('shift_output_worker', $sort)) {
                        $query->orderBy($sort, $direction);
                    } elseif ($sort === 'created_at') {
                        $query->orderBy('created_at', $direction);
                    }
                }

                // Filterlar tekshiruvi
                $filters = $request->get('filters', []);
                $isFiltered = count($filters) > 0;

                $filterDate = $filters['created_at'] ?? null;
                $date = $filterDate ? Carbon::parse($filterDate) : now();

                $shifts = Shift::whereHas('shiftOutputs.shiftOutputWorkers', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->orderBy('title')->pluck('title', 'id');

                $stages = Stage::whereHas('shiftOutputs.shiftOutputWorkers', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->orderBy('title')->pluck('title', 'id');

                $totalStageCount = (clone $query)->where('created_at', '>=', now()->startOfYear())->sum('stage_count');
                $totalDefectAmount = (clone $query)->where('created_at', '>=', now()->startOfYear())->sum('defect_amount');
                $totalPrice = (clone $query)->where('created_at', '>=', now()->startOfYear())->sum('price');

                $shiftOutputWorkers = $query->paginate(20)->withQueryString();

                return view('backend.user.show', compact(
                    'user',
                    'shifts',
                    'stages',
                    'shiftOutputWorkers',
                    'totalStageCount',
                    'totalDefectAmount',
                    'totalPrice',
                    'debts'
                ));
            } else {
                abort(403, 'Сиз факв=ат ишчи ходимларни кура оласиз!');
            }
        }

        return view('backend.user.show', compact('user', 'debts'));
    }


    public function excelExport(Request $request)
    {
        $searchModel = new ShiftOutputWorkerSearch(new DateFilterService());
        $query = $searchModel->search($request);

        return ExportService::excelExport($query, 'workers');
    }


    public function create()
    {
        $user = new User();
        $authRole = auth()->user()->role->title;

//        $rolesRoot = Role::whereNotIn('title', ['Developer'])->pluck('title', 'id');
//        $rolesAdmin = Role::whereNotIn('title', ['Admin', 'Developer', 'Manager'])->pluck('title', 'id');

        if ($authRole === 'Developer') {
            $roles = Role::whereNotIn('title', ['Developer'])->pluck('title', 'id');
        } elseif (in_array($authRole, ['Admin', 'Manager'])) {
            $roles = Role::whereIn('title', ['Master', 'Worker', 'Client'])->pluck('title', 'id');
        } else {
            $roles = Role::where('title', 'Client')->pluck('title', 'id');
        }

        $clientRoleId = Role::where('title', 'Client')->value('id');
        $clientRole = Role::where('title', 'Client')->value('title');

        return view('backend.user.create', compact(
            'user',
            'roles',
            'clientRoleId',
            'clientRole',
        ));
    }

    public function store(Request $request, User $user)
    {
        $authRole = auth()->user()->role->title;

        if ($authRole === 'Developer') {
            $allowedRoles = Role::whereNotIn('title', ['Developer'])->pluck('id')->toArray();
        } else {
            $allowedRoles = Role::whereIn('title', ['Master', 'Worker', 'Client'])->pluck('id')->toArray();
        }

        $request->merge([
            'debt' => $request->filled('debt') ? str_replace(' ', '', $request->debt) : 0,
        ]);

        $request->validate(
            [
                'first_name'     => 'nullable|string|max:100',
                'last_name'      => 'nullable|string|max:100',
                'username'       => 'required|string|max:64|unique:user,username',
                'address'        => 'nullable|string',
                'password_hash'  => 'nullable|string|min:6',
                'email'          => 'nullable|email',
                'photo'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'phone' => ['nullable', 'regex:/^\+998\s?\(\d{2}\)\s?\d{3}\s?\d{2}\s?\d{2}$/'],
                'telegram_chat_id' => 'nullable|integer',
                'currency'       => 'nullable|in:' . implode(',', [StatusService::CURRENCY_UZS, StatusService::CURRENCY_USD]),
                'debt'           => 'nullable|numeric|min:0',
                'role_id' => [
                    'required',
                    'integer',
                    'exists:role,id',
                    function ($attribute, $value, $fail) use ($allowedRoles) {
                        if (!in_array($value, $allowedRoles)) {
                            $fail('Сизда бундай рол бериш ҳуқуқи йўқ.');
                        }
                    },
                ],                'status'         => 'nullable|in:-1,0,1',
                'token'          => 'nullable|string',
                'auth_key'       => 'nullable|string',
            ],
            [
                'username.required' => 'Фойдаланувчи номи мажбурий.',
                'username.max' => 'Фойдаланувчи номи 64 белгидан ошмаслиги керак.',
                'phone.required' => 'Телефон раками мажбурий.',
            ]
        );

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->username = $request->username;
        $user->address = $request->address;
        $user->password_hash = Hash::make($request->password_hash ?? env('DEFAULT_USER_PASSWORD', 'castle4525'));
        $user->email = $request->email;

        if ($request->hasFile('photo')) {
            $uploadedFile = $request->file('photo');
            $path = $request->file('photo')->store('files', 'public');

            $file = new File();
            $file->name = $uploadedFile->getClientOriginalName();
            $file->path = $path;
            $file->extension = $uploadedFile->getClientOriginalExtension();
            $file->mime_type = $uploadedFile->getMimeType();
            $file->size = $uploadedFile->getSize();
            $file->date_create = time();
            $file->save();

            $user->photo = $file->id;
        }

        $user->phone = User::sanitizePhone($request->phone);
        $user->telegram_chat_id = $request->telegram_chat_id;
        $user->role_id = $request->role_id;
        $user->status = $request->status;
        $user->token = $request->token ?? Str::random(32);
        $user->auth_key = $request->auth_key ?? Str::random(32);

        $user->save();

        // 🔹 Qarzdorlik bo‘lsa — userDebt yaratiladi
        if ($request->debt > 0) {
            $user->userDebt()->create([
                'user_id' => $user->id,
                'amount' => $request->debt,
                'currency' => $request->currency,
                'source' => UserDebt::SOURCE_MANUAL,
            ]);
        }

        return redirect()->route('user.index')->with('success', 'Фойдаланувчи яратилди!');
    }

    public function storeAjax(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string|max:64|unique:user,username',
            'phone' => ['nullable', 'regex:/^\+998\s?\(\d{2}\)\s?\d{3}\s?\d{2}\s?\d{2}$/'],
            'role_id' => 'required|integer|exists:role,id',
        ]);

        $user = new User();
        $user->username = $data['username'];
        $user->phone = User::sanitizePhone($data['phone'] ?? null);
        $user->role_id = $data['role_id'];
        $user->password_hash = Hash::make(env('DEFAULT_USER_PASSWORD', 'castle4525'));
        $user->token = Str::random(32);
        $user->auth_key = Str::random(32);
        $user->status = StatusService::STATUS_ACTIVE;
        $user->save();

        return response()->json([
            'id' => $user->id,
            'username' => $user->username,
            'phone' => $user->phone,
        ]);
    }


    public function edit(User $user)
    {
        $authRole = auth()->user()->role->title;

//        $rolesRoot = Role::whereNotIn('title', ['Developer'])->pluck('title', 'id');
//        $rolesAdmin = Role::whereNotIn('title', ['Admin', 'Developer'])->pluck('title', 'id');

        if ($authRole === 'Developer') {
            $roles = Role::whereNotIn('title', ['Developer'])->pluck('title', 'id');
        } elseif (in_array($authRole, ['Admin', 'Manager'])) {
            $roles = Role::whereIn('title', ['Master', 'Worker', 'Client'])->pluck('title', 'id');
        } else {
            $roles = Role::where('title', 'Client')->pluck('title', 'id');
        }

        $clientRoleId = Role::where('title', 'Client')->value('id');
        $clientRole = Role::where('title', 'Client')->value('title');

        $userDebts = $user->userDebt->groupBy('currency'); // valyutaga qarab guruhlaymiz
        $debtUzs = $userDebts->get(StatusService::CURRENCY_UZS)?->sum('amount') ?? 0;
        $debtUsd = $userDebts->get(StatusService::CURRENCY_USD)?->sum('amount') ?? 0;

        return view('backend.user.update', compact(
            'user',
            'roles',
            'clientRoleId',
            'clientRole',
            'debtUzs',
            'debtUsd',
        ));
    }

    public function update(Request $request, User $user)
    {
        $authRole = auth()->user()->role->title;

        if ($authRole === 'Developer') {
            $allowedRoles = Role::whereNotIn('title', ['Developer'])->pluck('id')->toArray();
        } else {
            $allowedRoles = Role::whereIn('title', ['Master', 'Worker', 'Client'])->pluck('id')->toArray();
        }

        $request->merge([
            //            'debt' => $request->filled('debt') ? str_replace(' ', '', $request->debt) : 0,
            'debt_uzs' => $request->filled('debt_uzs') ? str_replace(' ', '', $request->debt_uzs) : 0,
            'debt_usd' => $request->filled('debt_usd') ? str_replace(' ', '', $request->debt_usd) : 0,
        ]);

        $request->validate(
            [
                'first_name'     => 'nullable|string|max:100',
                'last_name'      => 'nullable|string|max:100',
                'username'       => 'required|string|max:64|unique:user,username,' . $user->id,
                'address'       => 'nullable|string',
                'password_hash'  => 'nullable|string|min:6',
                'email'          => 'nullable|email',
                'photo'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'phone' => ['nullable', 'regex:/^\+998\s?\(\d{2}\)\s?\d{3}\s?\d{2}\s?\d{2}$/'],
                'telegram_chat_id' => 'nullable|integer',
                'currency'       => 'nullable|in:' . implode(',', [StatusService::CURRENCY_UZS, StatusService::CURRENCY_USD]),
                'debt_uzs'           => 'nullable|numeric|min:0',
                'debt_usd'           => 'nullable|numeric|min:0',
                'role_id' => [
                    'required',
                    'integer',
                    'exists:role,id',
                    function ($attribute, $value, $fail) use ($allowedRoles) {
                        if (!in_array($value, $allowedRoles)) {
                            $fail('Сизда бундай рол бериш ҳуқуқи йўқ.');
                        }
                    },
                ],                'status'         => 'nullable|in:-1,0,1',
                'token'          => 'nullable|string',
                'auth_key'       => 'nullable|string',
            ],
            [
                'username.required' => 'Фойдаланувчи номи мажбурий.',
                'username.max' => 'Фойдаланувчи номи 64 белгидан ошмаслиги керак.',
                'phone.required' => 'Телефон раками мажбурий.',
            ]
        );

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->username = $request->username;
        $user->address = $request->address;

        if ($request->filled('password_hash')) {
            $request->validate([
                'password_hash' => 'nullable|string|min:6',
                'current_password' => 'required_with:password_hash|string',
            ]);

            if (!Hash::check($request->current_password, $user->password_hash)) {
                return back()->withErrors(['current_password' => 'Эски пароль нотўғри'])->withInput();
            }

            $user->password_hash = Hash::make($request->password_hash);
        }

        $user->email = $request->email;

        if ($request->hasFile('photo')) {
            $uploadedFile = $request->file('photo');
            $path = $request->file('photo')->store('files', 'public');

            $file = new File();
            $file->name = $uploadedFile->getClientOriginalName();
            $file->path = $path;
            $file->extension = $uploadedFile->getClientOriginalExtension();
            $file->mime_type = $uploadedFile->getMimeType();
            $file->size = $uploadedFile->getSize();
            $file->date_create = time();
            $file->save();

            $user->photo = $file->id;
        }

        $user->phone = User::sanitizePhone($request->phone);
        $user->telegram_chat_id = $request->telegram_chat_id;
        $user->role_id = $request->role_id;
        $user->status = $request->status;
        $user->token = $request->token ?? Str::random(32);
        $user->auth_key = $request->auth_key ?? Str::random(32);


        // 🔹 1. UZS debt
        if ($request->filled('debt_uzs')) {
            $debtUzs = (float) str_replace(' ', '', $request->debt_uzs);
            if ($debtUzs > 0) {
                $userDebtUzs = $user->userDebt()->firstOrNew(['currency' => StatusService::CURRENCY_UZS]);
                $userDebtUzs->amount = ($userDebtUzs->amount ?? 0) + $debtUzs;
                $userDebtUzs->user_id = $user->id;
                $userDebtUzs->source = UserDebt::SOURCE_MANUAL;
                $userDebtUzs->save();
            }
        }

        // 🔹 2. USD debt
        if ($request->filled('debt_usd')) {
            $debtUsd = (float) str_replace(' ', '', $request->debt_usd);
            if ($debtUsd > 0) {
                $userDebtUsd = $user->userDebt()->firstOrNew(['currency' => StatusService::CURRENCY_USD]);
                $userDebtUsd->amount = ($userDebtUsd->amount ?? 0) + $debtUsd;
                $userDebtUsd->user_id = $user->id;
                $userDebtUsd->source = UserDebt::SOURCE_MANUAL;
                $userDebtUsd->save();
            }
        }

        $user->save();

        return redirect()->route('user.show', $user->id)->with('success', 'Фойдаланувчи янгиланди!');
    }


    public function destroy(User $user)
    {
        if (isset($user->avatar)) {
            Storage::disk('public')->delete($user->avatar->path);
            $user->avatar->delete();
        }
        $user->status = StatusService::STATUS_DELETED;
        $user->save();

        return response()->json([
            'message' => 'Фойдаланувчи ўчирилди!',
            'type' => 'delete',
            'redirect' => route('user.index')
        ]);
    }
}
