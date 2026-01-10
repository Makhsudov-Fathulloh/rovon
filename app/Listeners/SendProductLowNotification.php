<?php

namespace App\Listeners;

use App\Events\ProductLowEvent;
use Illuminate\Support\Facades\Cache;


class SendProductLowNotification
{
    public function handle(ProductLowEvent $event)
    {
        // Kamaygan materiallar ro‘yxatiga qo‘shib qo‘yamiz
        $list = Cache::get('low_raw_materials', []);

        $list[$event->productVariation->id] = $event->productVariation->code . ' - ' . $event->productVariation->title . ' - ' . \App\Helpers\CountHelper::format($event->productVariation->count, $event->productVariation->unit) ?? '';
        Cache::put('low_raw_materials', $list, 5); // 5 daqiqa

        // Sessionga darhol yozamiz — ishlaydi:
        if (auth()->check() && in_array(auth()->user()->role->title, ['Admin', 'Manager', 'Moderator', 'Developer'])) {
            session()->flash('info', "Минимал микдордаги махсулотлар: <br>" . implode('<br>', $list));
        }
    }
}
