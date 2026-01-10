<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ExchangeRates;
use Illuminate\Support\Facades\Http;

class UpdateExchangeRates extends Command
{
    protected $signature = 'exchange:update';
    protected $description = 'Марказий банк API дан валюта курсларини янгилайди!';

    public function handle()
    {
        $this->info('⏳ Марказий банк API дан малумот олинмокда...');

        try {
            // API manzili
            $response = Http::get('https://cbu.uz/uz/arkhiv-kursov-valyut/json/');

            if ($response->failed()) {
                $this->error('❌ API дан маълумот олинмади');
                return Command::FAILURE;
            }

            $data = $response->json();

            foreach ($data as $item) {
                // Faqat kerakli valyutalarni saqlaymiz (USD, EUR, RUB)
                if (in_array($item['Ccy'], ['USD', 'EUR', 'RUB'])) {
                    ExchangeRates::updateOrCreate(
                        [
                            'currency' => $item['Ccy'],
                        ],
                        [
                            'rate' => $item['Rate'],
                        ]
                    );
                }
            }

            $this->info('✅ Валюта курслари муваффақиятли янгиланди!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('⚠️ Хатолик: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
