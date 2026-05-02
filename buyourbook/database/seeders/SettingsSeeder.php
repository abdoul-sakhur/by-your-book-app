<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'delivery_fee'             => 3000,
            'free_delivery_threshold'  => 500000,
            'tawkto_widget_id'         => '',
        ];

        foreach ($defaults as $key => $value) {
            if (Setting::where('key', $key)->doesntExist()) {
                Setting::set($key, $value);
            }
        }
    }
}
