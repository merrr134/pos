<?php

namespace App\Http\Controllers\Station;

/**
 * FR-007 — Station Barista (item minuman), mirror Kitchen dengan skema amber.
 * Seluruh logika di StationController; subclass hanya menentukan station.
 */
class BaristaController extends StationController
{
    protected function station(): string
    {
        return 'barista';
    }
}
