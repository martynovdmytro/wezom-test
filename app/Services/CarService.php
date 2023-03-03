<?php

namespace App\Services;


use App\Models\Car;
use Illuminate\Support\Facades\DB;

class CarService
{
    public function getAllCars () {
        $cars = DB::table('cars')
                  ->paginate(5);
        // todo sort
        return $cars;
    }

    public function getCarById ($id) {
        $car = DB::table('cars')
                 ->where('id', $id)
                 ->get();

        return $car;
    }

    public function add ($request) {
        //
    }

    public function edit ($request) {
        //
    }

    public function delete ($id) {
        $result = DB::table('cars')
                    ->where('id', $id)
                                   ->delete();

        return $result;
    }
}