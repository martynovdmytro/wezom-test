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
        $name = $request->input('name');
        $number = $request->input('number');
        $color = $request->input('color');
        $vin = $request->input('vin');
        $maker = null;
        $model = null;
        $year = null;
        $timestamp = date('Y-m-d H:i:s');

        $decodedVinData = $this->vinDecoder($vin);

        if ($decodedVinData !== 'error') {
            foreach ($decodedVinData["Results"] as $result) {
                if ($result["Variable"] === "Make") {
                    $maker = $result["Value"];
                }
                if ($result["Variable"] === "Model") {
                    $model = $result["Value"];
                }
                if ($result["Variable"] === "Model Year") {
                    $year = $result["Value"];
                }
            }
        }

        DB::beginTransaction();
        // логически одному юзеру может принадлежать несколько автомобилей
        // в идеале для привязки авто должен быть какой-то уникальный идентификатор, типа номера паспорта юзера
        // в задании этого не было, поэтому идентификация только по имени

        $userId = $this->getUserId($name);

        if (is_null($userId)) {
            $userId = DB::table('users')->insertGetId([
                'name' => $name,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ]);
        }

        if (!$this->issetNumber($number) && !$this->issetVin($vin)) {
            $success = DB::table('cars')->insert([
                'number' => $number,
                'color' => $color,
                'maker' => $maker,
                'model' => $model,
                'year' => $year,
                'vin' => $vin,
                'user_id' => $userId,
                'created_at' => $timestamp,
                'updated_at' => $timestamp

            ]);
            if ($success) {
                DB::commit();
                return 'success';
            } else {
                DB::rollBack();
                return 'error';
            }
        } else {
            DB::rollBack();
            return 'car number or vin code already exists';
        }
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
                 ->where('vin', $vin)
                 ->exists();
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
}