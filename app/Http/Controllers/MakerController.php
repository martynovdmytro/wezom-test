<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
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
     * Display maker and related brands depends on data entered to autocomplete input.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $response = $this->makerService->index($request);

        return $response;
    }

}
