<?php

namespace App\Jobs;

use App\Models\Brand;
use App\Models\Maker;
use App\Services\ApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class RefreshMaker implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ApiService $apiService)
    {
        Maker::truncate();
        Brand::truncate();
        $getAllMakersUrl = "https://vpic.nhtsa.dot.gov/api/vehicles/getallmakes?format=json";
        $response = $apiService->getApiData($getAllMakersUrl);
        $timestamp = date('Y-m-d H:i:s');

        foreach ($response["Results"] as $result) {
            DB::table('makers')->insert([
                'id' => $result["Make_ID"],
                'name' => $result["Make_Name"],
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ]);
            $getBrandsUrl = "https://vpic.nhtsa.dot.gov/api/vehicles/getmodelsformakeid/".$result["Make_ID"]."?format=json";
            $brands = $apiService->getApiData($getBrandsUrl);
            foreach ($brands["Results"] as $brand) {
                DB::table('brands')->insert([
                    'name' => $brand["Model_Name"],
                    'maker_id' => $result["Make_ID"]
                ]);
            }
        }
    }
}
