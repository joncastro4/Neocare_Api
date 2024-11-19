<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Notification;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        Notification::factory(10)->create();
    }
}
