<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MakerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // todo вывод всех записей из таблицы makers и привязанных к ним models

        return 'index makers';
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
        return 'store makers';
    }
}
