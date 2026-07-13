<?php

namespace App\Http\Controllers\Station;

/**
 * FR-006 — Station Kitchen (item makanan).
 * Seluruh logika di StationController; subclass hanya menentukan station.
 */
class KitchenController extends StationController
{
    protected function station(): string
    {
        return 'kitchen';
    }
}
