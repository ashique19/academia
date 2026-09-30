<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Public identity shown on the site.
 *
 * The legal trade name and the market-facing brand are different settings.
 * Phone, KvK and VAT stay hidden while they are blank or still the known
 * zero placeholders — this class does not invent replacements.
 */
final class PublicSite
{
    public static function name(): string
    {
        $name = trim((string) config('academia.public_name'));

        return $name !== '' ? $name : 'Academia Training EU';
    }

    public static function defaultTitle(): string
    {
        $title = trim((string) config('academia.default_title'));

        return $title !== '' ? $title : self::name().' — Professional Training Across Europe';
    }

    public static function email(): string
    {
        $email = trim((string) config('academia.email'));

        return $email !== '' ? $email : 'info@academiatraining.eu';
    }

    public static function phone(): ?string
    {
        $phone = trim((string) config('academia.phone'));

        if ($phone === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        // +31 20 000 0000 and any other number that is only a zero run.
        if ($digits === '' || preg_match('/0{6,}/', $digits) === 1) {
            return null;
        }

        return $phone;
    }

    public static function registrationLine(): string
    {
        $kvk = self::realRegistration(config('academia.kvk'));
        $vat = self::realRegistration(config('academia.vat'));

        if ($kvk !== null && $vat !== null) {
            return 'KvK '.$kvk.' · VAT '.$vat;
        }

        return 'Company registration details on request';
    }

    public static function ogImageUrl(): string
    {
        return rtrim(request()->getSchemeAndHttpHost(), '/').'/images/og-default.png';
    }

    private static function realRegistration(mixed $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $compact = strtoupper((string) preg_replace('/\s+/', '', $value));

        if (preg_match('/^0+$/', $compact) === 1 || preg_match('/^NL0+B0*1$/', $compact) === 1) {
            return null;
        }

        return $value;
    }
}
