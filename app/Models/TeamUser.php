<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TeamUser extends Model
{
    use HasFactory;

    protected $table = 'team_user';

    protected $fillable = [
        'team_id',
        'user_id',
    ];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
