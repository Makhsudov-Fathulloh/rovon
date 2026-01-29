<?php

namespace App\Helpers;

use App\Models\Role;
use App\Models\User;
use App\Models\Order;
use App\Models\PreOrder;
use App\Services\StatusService;

class TelegramHelper
{
    public static function send($chatId, $message)
    {
        $token = env('TELEGRAM_BOT_TOKEN');

        if (!$token || !$chatId) {
            return false;
        }

        $url = "https://api.telegram.org/bot{$token}/sendMessage";

        $data = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML'
        ];

        // Curl — file_get_contents emas, chunki ba'zan hosting bloklaydi
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    public static function notifyDefect(string $message)
    {
        // $roleIds = Role::whereIn('title', ['Admin', 'Manager', 'Developer'])->pluck('id');
        // $recipients = User::whereIn('role_id', $roleIds)
        //     ->whereNotNull('telegram_chat_id')
        //     ->pluck('telegram_chat_id');

        // foreach ($recipients as $chatId) {
        //     TelegramHelper::send($chatId, $message);
        // }

        $adminChatIds = array_filter(
            array_map('trim', explode(',', env('TELEGRAM_ADMINS')))
        );

        if (empty($adminChatIds)) {
            return;
        }

        foreach ($adminChatIds as $chatId) {
            TelegramHelper::send($chatId, $message);
        }
    }


    /**
     * Хабарни телефон рақами орқали юбориш (ёрдамчи метод)
     */
    public static function sendByPhone(string $phone, string $message)
    {
        // Телефон рақамни форматлаш (фақат рақамлар)
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Базадан шу рақамли ва Telegram уланган фойдаланувчини топиш
        $user = User::where(function($query) use ($phone) {
            $query->where('phone', $phone)
                ->orWhere('phone', '+' . $phone)
                ->orWhere('phone', 'like', '%' . substr($phone, -9));
        })
            ->whereNotNull('telegram_chat_id')
            ->first();

        if ($user && $user->telegram_chat_id) {
            return self::send($user->telegram_chat_id, $message);
        }

        return false;
    }


    public static function orderMessage(Order $order, string $type = 'create'): string
    {
        $title = $type === 'update'
            ? '✏️ <b>Буюртма янгиланди!</b>'
            : '🧾 <b>Янги буюртма!</b>';

        $currency = $order->currency == StatusService::CURRENCY_USD ? '$' : 'сўм';

        $fmt = function ($v, $decimals = 0) {
            $v = $v ?? 0;
            if ($decimals > 0) {
                return number_format((float)$v, $decimals, '.', ' ');
            }
            return number_format((float)$v, 0, '', ' ');
        };

        // Xavfsizligi uchun matnlarni HTML uchun escape qilamiz
        $orderId = htmlspecialchars("#{$order->id}", ENT_QUOTES, 'UTF-8');
        $username = htmlspecialchars($order->user->username ?? '—', ENT_QUOTES, 'UTF-8');

        // Items qismidan biroz summary quramiz (agar order->orderItems mavjud bo'lsa)
        $itemsText = '';
        if ($order->relationLoaded('orderItems') || $order->orderItems()->exists()) {
            $items = $order->orderItems ?? $order->orderItems()->get();
            // limit: 5 ta item ko'rsatamiz va qolganlar uchun +n
            $count = $items->count();
            $show = $items->take(5);
            $lines = [];

            // $unitDecimals = match ($order->unit) {
            //     StatusService::UNIT_PSC   => 0, // dona → 0
            //     StatusService::UNIT_METER => 2, // metr → 0.00
            //     StatusService::UNIT_KG    => 3, // kg → 0.000
            //     default                  => 0,
            // };
            // $priceDecimals = $order->currency == StatusService::CURRENCY_USD ? 2 : 0;

            foreach ($show as $it) {
                $pTitle = htmlspecialchars($it->title ?? ($it->productVariation->product->title ?? '—'), ENT_QUOTES, 'UTF-8');
                $q = $it->quantity;
                $pr = $fmt($it->price, $order->currency == StatusService::CURRENCY_USD ? 2 : 0);
                $lines[] = "• {$pTitle} — <code>{$q} x {$pr} {$currency}</code>";
            }
            if ($count > 5) {
                $lines[] = "… + " . ($count - 5) . " та махсулот";
            }
            $itemsText = implode("\n", $lines);
        }

        $date = $type === 'update' ? ($order->updated_at->format('d.m.Y H:i') ?? $order->created_at->format('d.m.Y H:i')) : $order->created_at->format('d.m.Y H:i');

        // Asosiy xabar: aniq, zamonaviy format
        $message = <<<HTML
            {$title}

            ━━━━━━━━━━━━━━━━━━━━━━━━
            <b>📦 Буюртма:</b> <code>{$orderId}</code>
            <b>👤 Мижоз:</b> {$username}

            <b>🧾 Умумий:</b> <code>{$fmt($order->total_price,$order->currency == StatusService::CURRENCY_USD ? 2 : 0)} {$currency}</code>
            <b>✅ Тўланган:</b> <code>{$fmt($order->total_amount_paid,$order->currency == StatusService::CURRENCY_USD ? 2 : 0)} {$currency}</code>
            <b>❗ Қарздорлик:</b> <code>{$fmt($order->remaining_debt,$order->currency == StatusService::CURRENCY_USD ? 2 : 0)} {$currency}</code>

            <b>📋 Махсулотлар:</b>
            {$itemsText}

            <b>🕒 Сана:</b> <code>{$date}</code>
            ━━━━━━━━━━━━━━━━━━━━━━━━
            <a href="https://{$_SERVER['HTTP_HOST']}/admin/order/{$order->id}">🔗 Буюртмани очиш</a>
        HTML;

        return $message;
    }


    public static function sendOrderToClients(Order $order, string $type = 'create')
    {
        if (!$order->relationLoaded('user')) {
            $order->load('user');
        }

        $user = $order->user;

        if (!$user || !$user->phone) {
            return false;
        }

        // Телефон орқали юборишга йўналтирамиз
        return self::sendByPhone($user->phone, self::orderMessage($order, $type));
    }


    public static function debtMessage(User $user, float $totalDebtBefore, float $paidAmount, float $remainingDebt, Int $currency, string $type = 'create'): string {
        $title = $type === 'update'
            ? '✏️ <b>Қарздорлик янгиланди</b>'
            : '💳 <b>Қарздорлик сўндирилди!</b>';

        $currency = $currency == StatusService::CURRENCY_USD ? '$' : 'сўм';

        $fmt = function ($v) use ($currency) {
            $v = (float)$v;
            if ($currency === '$') {
                return number_format($v, 2, '.', ' ');
            }
            return number_format($v, 0, '', ' ');
        };

        $username = htmlspecialchars($user->username ?? '—', ENT_QUOTES, 'UTF-8');
        $date = now()->format('d.m.Y H:i');

        return <<<HTML
        {$title}

        ━━━━━━━━━━━━━━━━━━━━━━━
        <b>👤 Мижоз:</b> {$username}

        <b>📌 Бошланғич қарз:</b> <code>{$fmt($totalDebtBefore)} {$currency}</code>
        <b>✅ Сўндирилди:</b> <code>{$fmt($paidAmount)} {$currency}</code>
        <b>❗ Қолдиқ қарз:</b> <code>{$fmt($remainingDebt)} {$currency}</code>

        <b>🕒 Сана:</b> <code>{$date}</code>
        ━━━━━━━━━━━━━━━━━━━━━━━
        HTML;
    }

    public static function sendDebtToUser(User $user, float $totalDebtBefore, float $paidAmount, float $remainingDebt, int $currency, string $type = 'create') {
        if (!$user->phone) {
            return false;
        }

        return self::sendByPhone(
            $user->phone,
            self::debtMessage($user, $totalDebtBefore, $paidAmount, $remainingDebt, $currency, $type)
        );
    }


    public static function notifyPreOrder(string $message)
    {
        // $roleIds = Role::whereIn('title', ['Admin', 'Manager', 'Moderator'])
        //     ->pluck('id');

        // $recipients = User::whereIn('role_id', $roleIds)
        //     ->whereNotNull('telegram_chat_id')
        //     ->pluck('telegram_chat_id');

        // foreach ($recipients as $chatId) {
        //     TelegramHelper::send($chatId, $message);
        // }

        $adminChatIds = array_filter(
            array_map('trim', explode(',', env('TELEGRAM_ADMINS')))
        );

        if (empty($adminChatIds)) {
            return;
        }

        foreach ($adminChatIds as $chatId) {
            TelegramHelper::send($chatId, $message);
        }
    }

    public static function preOrderMessage(PreOrder $pre, string $type = 'create')
    {
        $header = $type === 'update' ? '✏️ <b>Навбатдаги буюртма янгиланди!</b>'
            : '📝 <b>Янги навбатдаги буюртма!</b>';

        $title = htmlspecialchars($pre->title, ENT_QUOTES, 'UTF-8');
        $creator = htmlspecialchars($pre->customer->username ?? '—', ENT_QUOTES, 'UTF-8');
        $client  = htmlspecialchars($pre->user->username ?? '—', ENT_QUOTES, 'UTF-8');
        $date = now()->format('d.m.Y H:i');

        $items = $pre->preOrderItems;
        $lines = [];

        foreach ($items as $i) {
            $t = htmlspecialchars($i->code, ENT_QUOTES, 'UTF-8');
            $lines[] = "• {$t} — " . CountHelper::format($i->count, $i->unit);
        }

        $itemsText = implode("\n", $lines);

        return <<<HTML

        🕒 <b>Сана:</b> <code>{$date}</code>
        📝 <b>Янги навбатдаги буюртма!</b>

        <b>🧍‍♂️ Клиент:</b> {$client}
        <b>👨‍💼 Менежер:</b> {$creator}

        <b>📌 Номи:</b> {$title}
        <b>🔢 Пунктлар сони: <code>{$pre->count}</code> хил</b>
        ━━━━━━━━━━━━━━━━━━━━━━━

        <b>📦 Махсулотлар:</b>
        <code>{$itemsText}</code>

        <a href="https://{$_SERVER['HTTP_HOST']}/admin/pre-order/{$pre->id}">🔗 Буюртмани очиш</a>
        HTML;
    }
}
