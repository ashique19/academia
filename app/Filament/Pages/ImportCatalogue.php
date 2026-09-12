<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

/**
 * @property-read Schema $form
 */
class ImportCatalogue extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowDownTray;

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Import catalogue';

    protected static ?int $navigationSort = 15;

    protected static ?string $title = 'Import catalogue';

    protected string $view = 'filament.pages.import-catalogue';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public ?string $lastOutput = null;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user !== null && $user->can('import_catalogue');
    }

    public function mount(): void
    {
        $this->form->fill([
            'dry_run' => true,
            'only' => 'all',
            'path' => null,
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Import options')
                    ->schema([
                        Toggle::make('dry_run')
                            ->label('Dry run')
                            ->helperText('Parse and validate without writing anything.')
                            ->default(true),
                        Select::make('only')
                            ->label('Section')
                            ->options([
                                'all' => 'All sections',
                                'reference' => 'Reference (countries, cities, modes)',
                                'trainers' => 'Trainers',
                                'courses' => 'Courses',
                                'sessions' => 'Sessions',
                                'glossary' => 'Glossary',
                            ])
                            ->required()
                            ->default('all'),
                        TextInput::make('path')
                            ->label('CSV path override')
                            ->helperText('Optional absolute or relative path to the CSV directory.'),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->footer([
                        Actions::make([
                            Action::make('runImport')
                                ->label('Run import')
                                ->action('runImport')
                                ->color('primary'),
                        ]),
                    ]),
            ]);
    }

    public function runImport(): void
    {
        $data = $this->form->getState();

        $parameters = [];

        if (! empty($data['dry_run'])) {
            $parameters['--dry-run'] = true;
        }

        if (($data['only'] ?? 'all') !== 'all' && filled($data['only'] ?? null)) {
            $parameters['--only'] = $data['only'];
        }

        if (filled($data['path'] ?? null)) {
            $parameters['--path'] = $data['path'];
        }

        $exitCode = Artisan::call('academia:import', $parameters);
        $this->lastOutput = Artisan::output();

        if ($exitCode === 0) {
            Notification::make()
                ->title($data['dry_run'] ?? false ? 'Dry run finished' : 'Import finished')
                ->body(str($this->lastOutput)->limit(400)->toString())
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Import failed')
                ->body(str($this->lastOutput)->limit(400)->toString())
                ->danger()
                ->send();
        }
    }
}
