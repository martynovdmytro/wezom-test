<?php

namespace App\Services;


use App\Jobs\RefreshMaker;
use App\Models\Maker;
use Illuminate\Support\Facades\DB;

class MakerService
{
    public function index($request) {
        $input = $request->input('enter');
        $data = null;

        if (!empty($input)) {
            $data = Maker::select("name")
                         ->where('name', 'LIKE', '%'. $input. '%')
                         ->get();

        }

        return $data;
    }

    public function store() {
        RefreshMaker::dispatch();

        return 'ok';
    }
}