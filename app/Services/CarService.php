<?php

namespace App\Services;


use Illuminate\Support\Facades\DB;

class CarService
{
    public function index($request) {
        $searchData = $request->input('search');
        if (isset($searchData)) {
            return 'success';
        } else {
            return json_encode($this->getAllCars());
        }
    }

    public function get ($id) {
        return $this->getCarById($id);
    }

    public function add ($request) {
        DB::beginTransaction();
        $decodedVinData = $this->vinDecoder($request['vin']);

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

        // логически одному юзеру может принадлежать несколько автомобилей
        // в идеале для привязки авто должен быть какой-то уникальный идентификатор, типа номера паспорта юзера
        // в задании этого не было, поэтому идентификация только по имени

        $userId = $this->getUserId($request['name']);
        $timestamp = date('Y-m-d H:i:s');

        if (is_null($userId)) {
            $userId = DB::table('users')->insertGetId([
                'name' => $request['name'],
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ]);
        }

        if (!$this->issetNumber($request['number']) && !$this->issetVin($request['vin'])) {
            $success = DB::table('cars')->insert([
                'number' => $request['number'],
                'color' => $request['color'],
                'maker' => $maker,
                'model' => $model,
                'year' => $year,
                'vin' => $request['vin'],
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
        $result = false;

        if ($result) {
            return 'success';
        } else {
            return 'error';
        }
    }

    public function delete ($id) {
        $result = DB::table('cars')
                    ->where('id', $id)
                                   ->delete();

        if ($result) {
            return 'success';
        } else {
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

    private function getAllCars () {
        $cars = DB::table('cars')
                  ->paginate(5);
        return $cars;
    }

    private function getCarById ($id) {
        $car = DB::table('cars')
                 ->where('id', $id)
                 ->get();

        return $car;
    }
}