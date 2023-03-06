<?php

namespace App\Services;


use App\Jobs\RefreshMaker;
use App\Models\Brand;
use App\Models\Maker;
use Illuminate\Support\Facades\DB;

class MakerService
{
    public function index($request) {
        $input = $request->input('autocomplete');
        $maker = null;

        if (!empty($input)) {
            $maker = DB::select(
                "select makers.name AS maker_name, brands.name AS brand_name from `makers` 
                        left join `brands` on makers.id=brands.maker_id
                        where makers.name LIKE '%$input%'"
            );
        }

        return $maker;
    }
}