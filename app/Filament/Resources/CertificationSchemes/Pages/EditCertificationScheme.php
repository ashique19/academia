<?php

namespace App\Filament\Resources\CertificationSchemes\Pages;

use App\Filament\Resources\CertificationSchemes\CertificationSchemeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCertificationScheme extends EditRecord
{
    protected static string $resource = CertificationSchemeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
