<?php

namespace App\Services;

use App\Jobs\ExportDataToXLS;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CarService
{
    public function index($request) {
        if ($request->has('search')) {
            $search = $request->input('search');
            $searchData = DB::table('users')
                            ->join('cars', 'users.id', '=', 'cars.user_id')
                            ->orderBy('cars.number', 'desc')
                            ->orderBy('cars.vin', 'desc')
                            ->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('number', 'LIKE', "%{$search}%")
                            ->orWhere('vin', 'LIKE', "%{$search}%")
                            ->get();
            $response = $searchData;
        } else {
            $response = DB::table('users')
                          ->join('cars', 'users.id', '=', 'cars.user_id')
                          ->orderBy('cars.number', 'desc')
                          ->orderBy('cars.vin', 'desc')
                          ->get();
        }

        if ($request->has('maker')) {
            $response = $response
                ->where('maker', $request->input('maker'));
        }

        if ($request->has('model')) {
            $response = $response
                ->where('model', $request->input('model'));
        }

        if ($request->has('year')) {
            $response = $response
                ->where('year', $request->input('year'));
        }

        if ($request->has('save')) {
            ExportDataToXLS::dispatch(
                $response,
            );
        }

        $response = $this->paginate($response, 10);

        return json_encode($response);
    }

    public function get ($id) {
        return json_encode($this->getCarById($id));
    }

    public function add ($request, $apiService) {
        DB::beginTransaction();
        $url = "https://vpic.nhtsa.dot.gov/api/vehicles/DecodeVinExtended/" . $request['vin'] . "?format=json";
        $carData = $apiService($url);
        $userId = $this->getUserId($request['name']);
        $timestamp = date('Y-m-d H:i:s');

        if (is_null($userId)) {
            $userId = DB::table('users')->insertGetId([
                'name' => $request['name'],
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ]);
        }

        if (!$this->issetNumber($request['number']) &&
            !$this->issetVin($request['vin']) &&
            $carData !== 'error')
        {
            foreach ($carData["Results"] as $result) {
                if ($result["Variable"] === "Make") {
                    $car['maker'] = $result["Value"];
                }
                if ($result["Variable"] === "Model") {
                    $car['model'] = $result["Value"];
                }
                if ($result["Variable"] === "Model Year") {
                    $car['year'] = $result["Value"];
                }
            }

            $success = DB::table('cars')->insert([
                'number' => $request['number'],
                'color' => $request['color'],
                'maker' => $car['maker'],
                'model' => $car['model'],
                'year' => $car['year'],
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
            return 'Error.';
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

    private function getCarById ($id) {
        $car = DB::table('cars')
                 ->where('id', $id)
                 ->get();

        return $car;
    }

    private function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (\Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1);

        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            $options);
    }
}