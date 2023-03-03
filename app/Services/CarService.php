<?php

namespace App\Services;


use App\Models\Car;

class CarService
{
    public function getAllCars () {
        $cars = Car::all();
        // todo sort, pagination
        return $cars;
    }

    public function getCarById ($id) {
        //
    }

    public function add ($request) {
        //
    }

    public function edit ($request) {
        //
    }

    public function delete ($id) {
        //
    }
}