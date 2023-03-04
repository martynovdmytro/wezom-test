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

    private function vinDecoder ($vin) {
        $query = http_build_query(array($vin));
        $opts = array('http' =>
                          array(
                              'header' => "Content-Type: application/x-www-form-urlencoded\r\n".
                                  "Content-Length: ".strlen($query)."\r\n".
                                  "User-Agent:MyAgent/1.0\r\n",
                              'method' => 'GET',
                              'content' => $query
                          )
        );
        $apiURL = "https://vpic.nhtsa.dot.gov/api/vehicles/DecodeVinExtended/" . $vin . "?format=json";
        $context = stream_context_create($opts);
        $fp = fopen($apiURL, 'rb', false, $context);
        if(!$fp)
        {
            return "error";
        }
        $response = @stream_get_contents($fp);
        if($response == false)
        {
            return "error";
        }
        return json_decode($response, true);
    }

    public function add ($request) {
        $name = $request->input('name');
        $number = $request->input('number');
        $color = $request->input('color');
        $vin = $request->input('vin');
        $make = null;
        $model = null;
        $year = null;

        $decodedVinData = $this->vinDecoder($vin);

        if ($decodedVinData != 'error') {
            foreach ($decodedVinData["Results"] as $result) {
                if ($result["Variable"] === "Make") {
                    $make = $result["Value"];
                }
                if ($result["Variable"] === "Model") {
                    $model = $result["Value"];
                }
                if ($result["Variable"] === "Year") {
                    $year = $result["Value"];
                }
            }
        }

        DB::beginTransaction();
        // в идеале для привязки авто должен быть какой-то уникальный идентификатор, типа номера паспорта юзера
        // в задании этого не было, поэтому идентификация только по имени

        $userId = $this->getUserId($name);

        if (is_null($userId)) {
            $userId = DB::table('users')->insertGetId([
                'name' => $name,
            ]);
        }

        if (!$this->issetNumber($number)) {
            $carId = DB::table('cars')->insertGetId([
                'number' => $number,
                'color' => $color,
                'vin' => $vin
            ]);
        } else {
            DB::rollBack();
            return 'car number already exists';
        }

        $success = DB::insert('insert into car_user (car_id, user_id) values (?, ?)', [$carId, $userId]);

        if ($success) {
            DB::commit();
            return 'success';
        } else {
            DB::rollBack();
            return 'error';
        }
    }

    private function getUserId($name) {
        $id = null;
        $user = DB::table('users')
                 ->where('name', $name)
                 ->exists();

        if (isset($user)) {
            $id = DB::table('users')
                      ->where('name', $name)
                                      ->pluck('id')->first();
        }

        return $id;
    }

    private function issetNumber($number) {
        return DB::table('cars')
                  ->where('number', $number)
                  ->exists();
    }

    private function issetVin($vin) {
        return DB::table('cars')
                  ->where('number', $vin)
                  ->exists();
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