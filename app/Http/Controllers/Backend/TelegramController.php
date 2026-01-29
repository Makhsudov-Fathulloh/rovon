<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class TelegramController extends Controller
{
    public function webhook(Request $request)
    {
        $data = $request->all();
        $message = $data['message'] ?? [];
        $chatId = $message['chat']['id'] ?? null;
        $text = trim($message['text'] ?? '');
        $contact = $message['contact'] ?? null;

        if (!$chatId) {
            return response()->noContent();
        }

        // 1️⃣ /start bosilganda telefon raqam so‘rash
        if ($text === '/start') {
            $this->askPhoneNumber($chatId);
            return response()->noContent();
        }

        // 2️⃣ Foydalanuvchi kontakt yuborsa
        if ($contact) {
            $phone = $contact['phone_number'] ?? null;

            if (!$phone) {
                $this->sendMessage($chatId, "❌ Телефон рақамингизни олишда хатолик юз берди.");
                return response()->noContent();
            }

            // 🔹 Telefon raqamni tozalash va +998 formatga keltirish
            $phone = preg_replace('/[^0-9]/', '', $phone);
            if (strlen($phone) === 9) $phone = '998' . $phone;
            $phone = '+' . ltrim($phone, '+');

            if (strlen($phone) === 12 && strpos($phone, '998') === 0) {
                $phone = '+' . $phone;
            } elseif (strlen($phone) === 9) {
                $phone = '+998' . $phone;
            }

            // 🔹 Adminga yuborish
            // $adminChatIds = explode(',', env('TELEGRAM_ADMINS'));

            $adminChatIds = array_filter(
                array_map('trim', explode(',', env('TELEGRAM_ADMINS')))
            );

            foreach ($adminChatIds as $adminChatId) {
                $this->sendMessage($adminChatId, "📩 Янги фойдаланувчи:\n📱 Рақам: {$phone}\n🆔 ChatID: {$chatId}");
            }

            // 🔹 Bazadan foydalanuvchini topish
            $user = User::where('phone', $phone)->first();

            if (!$user) {
                $this->sendMessage($chatId, "⚠️ Сизнинг рақамингиз тизимда топилмади: {$phone}");
                return response()->noContent();
            }

            // 🔹 Chat ID ni saqlash
            $user->update(['telegram_chat_id' => $chatId]);
            $this->sendMessage($chatId, "✅ Ассаламу алайкум, {$user->username}! Сизнинг аққаунтингиз bot билан боғланди.");

            return response()->noContent();
        }

        return response()->noContent();
    }

    private function askPhoneNumber($chatId)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $url = "https://api.telegram.org/bot{$token}/sendMessage";

        $keyboard = [
            'keyboard' => [
                [
                    [
                        'text' => '📱 Телеграм рақамни юбориш.',
                        'request_contact' => true
                    ]
                ]
            ],
            'one_time_keyboard' => true,
            'resize_keyboard' => true
        ];

        $postData = [
            'chat_id' => $chatId,
            'text' => "Ассаламy алайкум! Ботни улаш учун илтимос, телефон рақамингизни юборинг 👇.",
            'reply_markup' => json_encode($keyboard)
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    private function sendMessage($chatId, $text)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $url = "https://api.telegram.org/bot{$token}/sendMessage";

        $postData = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
