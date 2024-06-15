<?php

namespace App\Export\Module;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportModules implements WithMultipleSheets
{
    use Exportable;

    protected $sheets;

    public function __construct($sheets = [])
    {
        $this->sheets = $sheets;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        return $this->sheets;
    }
}