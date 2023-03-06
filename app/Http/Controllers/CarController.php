<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCarRequest;
use App\Services\ApiService;
use App\Services\CarService;
use Illuminate\Http\Request;

class CarController extends Controller
{
    private $carService;

    public function __construct() {
        $this->carService = new CarService();
    }

    /**
     * Display all, searched or/and filtered data from cars table.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $response = $this->carService->index($request);

        return $response;
    }

    /**
     * Store a new data to cars table from the form.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function store(StoreCarRequest $request)
    {
        $validated = $request->validated();

        $response = $this->carService->add($validated, new ApiService());

        return $response;
    }

    /**
     * Return data from cars table by entered id.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $response = $this->carService->get($id);

        return $response;
    }

    /**
     * Update the specified data in cars table.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreCarRequest $request, int $id)
    {
        $validated = $request->validated();

        $response = $this->carService->edit($validated, $id);

        return $response;
    }

    /**
     * Remove the specified data from cars table.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $response = $this->carService->delete($id);

        return $response;
    }
}
