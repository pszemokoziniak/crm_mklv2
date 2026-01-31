<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\OfertaStatus;

class LegacyOfertaStatusSeeder extends Seeder
{
    public function run()
    {
        $oldStatusy = DB::connection('old_crm')->table('mkl_status')->get();

        foreach ($oldStatusy as $oldStatus) {
            OfertaStatus::updateOrCreate(
                ['id' => $oldStatus->id],
                [
                    'name' => $oldStatus->status,
                ]
            );
        }
    }
}
