<?php

namespace App\Http\Controllers;

use App\Services\MakerService;
use Illuminate\Http\Request;

class MakerController extends Controller
{

    private $makerService;

    public function __construct()
    {
        $this->makerService = new MakerService();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // todo вывод всех записей из таблицы makers и привязанных к ним models
        $response = $this->makerService->index();

        return $response;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // todo запись makers в базу
        $response = $this->makerService->store();

        return $response;
    }
}
