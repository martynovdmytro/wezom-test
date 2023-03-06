<?php

namespace App\Services;


use Illuminate\Support\Facades\DB;

class MakerService
{
    public function index() {
        return 'ms index';
    }

    public function store() {
        $url = "https://vpic.nhtsa.dot.gov/api/vehicles/getallmakes?format=json";
        $apiService = new ApiService($url);
        $response = $apiService->getApiData();
        $timestamp = date('Y-m-d H:i:s');

        foreach ($response["Results"] as $result) {
            DB::table('makers')->insert([
                'id' => $result["Make_ID"],
                'name' => $result["Make_Name"],
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ]);
        }

        return $response;
    }
}