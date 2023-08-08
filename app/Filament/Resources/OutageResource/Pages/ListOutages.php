<?php

namespace App\Filament\Resources\OutageResource\Pages;

use App\Filament\Resources\OutageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOutages extends ListRecords
{
    protected static string $resource = OutageResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
