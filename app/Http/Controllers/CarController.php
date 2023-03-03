<?php

namespace App\Http\Controllers;

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
        // todo $result = $this->carService->getAllCars();

        // todo return $result;
        return $this->carService->test();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    // todo для теста с помощью постман:
    // todo в postman key - имя инпута, value - значение
    // todo в коде $request->input( key_в_postman )

    public function store(Request $request)
    {
        // todo validation
        // todo $result = $this->carService->add($request)
        // todo if ($result)
        // todo return success
        // todo else error

        return $request->input('name');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // todo validation
        // todo $result = $this->carService->getCarById($id);
        // todo return $result
        return 'show';
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // todo validation
        // todo $result = $thid->carService->edit( $request );
        // todo if ($result)
        // todo return success
        // todo else error

        return 'update';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // todo validation
        // todo $result = $this->carService->delete($id);
        // todo if ($result)
        // todo return success
        // todo else error
        return 'destroy';
    }
}
