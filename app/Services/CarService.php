<?php

namespace App\Services;


use App\Models\Car;
use Illuminate\Support\Facades\DB;

class CarService
{
    public function index($request) {
        $response = null;
        $search = $request->input('search');
        $maker = $request->input('maker');
        $model = $request->input('model');
        $year = $request->input('year');
        if (isset($search)) {
            $searchData = DB::table('users')
                            ->join('cars', 'users.id', '=', 'cars.user_id')
                            ->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('number', 'LIKE', "%{$search}%")
                            ->orWhere('vin', 'LIKE', "%{$search}%")
                            ->get();
            $response = $searchData;
        } elseif (isset($maker) || isset($model) || isset($year)){
            $filterData = 'filtered by option';

            // todo filter maker
            // todo filter model
            // todo filter year

            $response = $filterData;
        } else {
            $response = $this->getAllCars();
        }

        return json_encode($response);
    }

    public function get ($id) {
        return $this->getCarById($id);
    }

    public function add ($request) {
        DB::beginTransaction();
        // логически одному юзеру может принадлежать несколько автомобилей
        // в идеале для привязки авто должен быть какой-то уникальный идентификатор, типа номера паспорта юзера
        // в задании этого не было, поэтому идентификация только по имени
        $carData = $this->getCarData($request['vin']);
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
                'maker' => $carData['maker'],
                'model' => $carData['model'],
                'year' => $carData['year'],
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

    public function edit ($request, $id) {
        $carData = $this->getCarData($request['vin']);
        $timestamp = date('Y-m-d H:i:s');
        $success = DB::table('cars')->where('id', $id)
                                    ->update([
            'number' => $request['number'],
            'color' => $request['color'],
            'maker' => $carData['maker'],
            'model' => $carData['model'],
            'year' => $carData['year'],
            'vin' => $request['vin'],
            'updated_at' => $timestamp
        ]);

        if ($success) {
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

    private function getCarData($vin) {
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
        $vinData = @stream_get_contents($fp);
        if($vinData == false)
        {
            return "error";
        }

        $decodedVinData = json_decode($vinData, true);

        if ($decodedVinData !== 'error') {
            foreach ($decodedVinData["Results"] as $result) {
                if ($result["Variable"] === "Make") {
                    $response['maker'] = $result["Value"];
                }
                if ($result["Variable"] === "Model") {
                    $response['model'] = $result["Value"];
                }
                if ($result["Variable"] === "Model Year") {
                    $response['year'] = $result["Value"];
                }
            }
        }

        return $response;
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