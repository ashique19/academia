<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Domain\Shared\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

/**
 * @property-read Schema $form
 */
class ManageSiteSettings extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Site settings';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'Site settings';

    protected string $view = 'filament.pages.manage-site-settings';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    /**
     * Defaults mirrored from SettingSeeder (group + is_public).
     *
     * @var array<string, array{group: string, is_public: bool}>
     */
    private const META = [
        'site.trade_name' => ['group' => 'brand', 'is_public' => true],
        'site.legal_entity' => ['group' => 'brand', 'is_public' => true],
        'site.email' => ['group' => 'contact', 'is_public' => true],
        'site.phone' => ['group' => 'contact', 'is_public' => true],
        'site.sales_email' => ['group' => 'contact', 'is_public' => false],
        'seo.default_title' => ['group' => 'seo', 'is_public' => true],
        'seo.default_description' => ['group' => 'seo', 'is_public' => true],
        'seo.og_site_name' => ['group' => 'seo', 'is_public' => true],
        'seo.robots_default' => ['group' => 'seo', 'is_public' => true],
        'home.hero_headline' => ['group' => 'content', 'is_public' => true],
        'home.hero_lede' => ['group' => 'content', 'is_public' => true],
    ];

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user !== null && ($user->can('update_setting') || $user->hasRole('super-admin'));
    }

    public function mount(): void
    {
        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $flat = [];

        foreach (array_keys(self::META) as $key) {
            $stored = Setting::query()->where('key', $key)->value('value');
            $flat[$key] = is_array($stored) ? (string) ($stored['value'] ?? '') : (string) ($stored ?? '');
        }

        // Dotted Filament field names expect nested state (site.trade_name → data.site.trade_name).
        $this->form->fill(Arr::undot($flat));
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Brand & contact')
                    ->columns(2)
                    ->schema([
                        TextInput::make('site.trade_name')->label('Trade name')->required(),
                        TextInput::make('site.legal_entity')->label('Legal entity')->required(),
                        TextInput::make('site.email')->label('Public email')->email()->required(),
                        TextInput::make('site.phone')->label('Phone')->tel(),
                        TextInput::make('site.sales_email')->label('Sales email')->email(),
                    ]),
                Section::make('SEO defaults')
                    ->columns(2)
                    ->schema([
                        TextInput::make('seo.default_title')->label('Default title')->columnSpanFull(),
                        Textarea::make('seo.default_description')->label('Default description')->rows(3)->columnSpanFull(),
                        TextInput::make('seo.og_site_name')->label('OG site name'),
                        TextInput::make('seo.robots_default')->label('Robots default'),
                    ]),
                Section::make('Home hero')
                    ->schema([
                        TextInput::make('home.hero_headline')->label('Headline')->columnSpanFull(),
                        Textarea::make('home.hero_lede')->label('Lede')->rows(3)->columnSpanFull(),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Save settings')
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ]);
    }

    public function save(): void
    {
        $flat = Arr::dot($this->form->getState());

        foreach (self::META as $key => $meta) {
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => ['value' => (string) ($flat[$key] ?? '')],
                    'group' => $meta['group'],
                    'is_public' => $meta['is_public'],
                ],
            );
        }

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }
}
