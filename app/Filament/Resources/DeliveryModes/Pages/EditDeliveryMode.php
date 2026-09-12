<?php

namespace App\Filament\Resources\DeliveryModes\Pages;

use App\Filament\Resources\DeliveryModes\DeliveryModeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDeliveryMode extends EditRecord
{
    protected static string $resource = DeliveryModeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
