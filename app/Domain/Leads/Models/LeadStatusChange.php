<?php

declare(strict_types=1);

namespace App\Domain\Leads\Models;

use App\Domain\Leads\Enums\LeadStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Append-only pipeline history.
 *
 * The current status on the inquiry is never changed without writing one of
 * these. Without the history, "average days to win" is unanswerable and the
 * pipeline is a snapshot rather than a process.
 */
class LeadStatusChange extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'corporate_inquiry_id', 'user_id', 'from_status', 'to_status', 'note',
    ];

    protected function casts(): array
    {
        return [
            'from_status' => LeadStatus::class,
            'to_status'   => LeadStatus::class,
            'created_at'  => 'datetime',
        ];
    }

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(CorporateInquiry::class, 'corporate_inquiry_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
