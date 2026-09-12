<?php

namespace App\Filament\Resources\CorporateInquiries\Pages;

use App\Filament\Resources\CorporateInquiries\CorporateInquiryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditCorporateInquiry extends EditRecord
{
    protected static string $resource = CorporateInquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
