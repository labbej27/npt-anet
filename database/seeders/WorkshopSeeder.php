<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkshopSession;
use Illuminate\Support\Carbon;

class WorkshopSeeder extends Seeder
{
    public function run(): void
    {
        $start = Carbon::now()->startOfWeek(Carbon::MONDAY);

        // Créer 12 semaines de mercredis
        for ($w = 0; $w < 12; $w++) {
            $wednesday = (clone $start)->addWeeks($w)->next(Carbon::WEDNESDAY);

            foreach ([['14:00','15:00'], ['15:00','16:00'], ['16:00','17:00']] as [$from,$to]) {
                WorkshopSession::firstOrCreate(
                    ['date' => $wednesday->toDateString(), 'start_time' => $from],
                    [
                        'end_time' => $to,
                        'capacity' => 5,
                        'location' => "Mairie d'Anet",
                        'topic' => 'Inclusion numérique – logiciels libres',
                    ]
                );
            }
        }
    }
}
