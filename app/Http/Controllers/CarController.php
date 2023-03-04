<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCarRequest;
use App\Services\CarService;
use Illuminate\Http\Request;

class CarController extends Controller
{
    private $carService;

    public function __construct() {
        $this->carService = new CarService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = $this->carService->getAllCars();

        return json_encode($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(StoreCarRequest $request)
    {
        $validated = $request->validated();

        $result = $this->carService->add($request);

        if (!$result) {
            return 'error';
        }

        return $result;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // todo id int val must be checked
        $result = $this->carService->getCarById($id);

        return json_encode($result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreCarRequest $request, $id)
    {
        // todo id int val must be checked
        $validated = $request->validated();

        $result = $this->carService->edit($request);

        if (!$result) {
            return 'error';
        }

        return $result;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // todo id int val must be checked
        $result = $this->carService->delete($id);

        if (!$result) {
            return 'error';
        }

        return $result;
    }
}