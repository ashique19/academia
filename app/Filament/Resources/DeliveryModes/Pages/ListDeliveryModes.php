<?php

namespace App\Filament\Resources\DeliveryModes\Pages;

use App\Filament\Resources\DeliveryModes\DeliveryModeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeliveryModes extends ListRecords
{
    protected static string $resource = DeliveryModeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
