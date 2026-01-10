<?php

namespace App\Listeners;

use App\Events\RawMaterialLowEvent;
use Illuminate\Support\Facades\Cache;


class SendRawMaterialLowNotification
{
    public function handle(RawMaterialLowEvent $event)
    {
        // Kamaygan materiallar ro‘yxatiga qo‘shib qo‘yamiz
        $list = Cache::get('low_raw_materials', []);

        $list[$event->rawMaterialVariation->id] = $event->rawMaterialVariation->code . ' - ' . $event->rawMaterialVariation->title . ' - ' . \App\Helpers\CountHelper::format($event->rawMaterialVariation->count, $event->rawMaterialVariation->unit) ?? '';
        Cache::put('low_raw_materials', $list, 5); // 5 daqiqa

        // Sessionga darhol yozamiz — ishlaydi:
        if (auth()->check() && in_array(auth()->user()->role->title, ['Admin', 'Manager', 'Moderator', 'Developer'])) {
            session()->flash('info', "Минимал микдордаги хомашёлар: <br>" . implode('<br>', $list));
        }
    }
}
