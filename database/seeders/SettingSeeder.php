<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Shared\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * Site-wide settings editable from the Filament Settings resource.
 *
 * Values mirror config/academia.php defaults so the admin panel is populated
 * after a fresh seed, while runtime still falls back to config when a key is
 * absent.
 */
class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['site.trade_name', ['value' => 'Academia Training Solutions'], 'brand', true],
            ['site.legal_entity', ['value' => 'SlimCijfers Analytics B.V.'], 'brand', true],
            ['site.email', ['value' => 'info@academiatraining.eu'], 'contact', true],
            ['site.phone', ['value' => '+31 20 000 0000'], 'contact', true],
            ['site.sales_email', ['value' => 'sales@academiatraining.eu'], 'contact', false],
            ['seo.default_title', ['value' => 'Academia Training Solutions — Professional Training Across Europe'], 'seo', true],
            ['seo.default_description', ['value' => 'Practical, expert-led professional training delivered online, onsite and in classrooms across Europe. Over 500 courses in business, finance, technology, supply chain, HR and compliance.'], 'seo', true],
            ['seo.og_site_name', ['value' => 'Academia Training Solutions'], 'seo', true],
            ['seo.robots_default', ['value' => 'index,follow'], 'seo', true],
            ['home.hero_headline', ['value' => 'Professional training that earns its place in the working week'], 'content', true],
            ['home.hero_lede', ['value' => 'Expert-led courses online, onsite and in 26 European cities — with published group sizes, fixed prices and a named trainer on every joining pack.'], 'content', true],
        ];

        foreach ($settings as [$key, $value, $group, $isPublic]) {
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'group' => $group,
                    'is_public' => $isPublic,
                ]
            );
        }
    }
}
