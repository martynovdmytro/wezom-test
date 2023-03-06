<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class CarsExport implements FromCollection
{
    private $response;

    public function __construct($response)
    {
        $this->response = $response;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->response;
    }
}
