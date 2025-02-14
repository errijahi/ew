<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ConnectedAccountFactory;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use JoelButcher\Socialstream\ConnectedAccount as SocialstreamConnectedAccount;

class ConnectedAccount extends SocialstreamConnectedAccount
{
    /** @use HasFactory<ConnectedAccountFactory> */
    use HasFactory;

    use HasTimestamps;

    protected $casts = [
        'created_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /** @return BelongsTo<Team, $this> */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
