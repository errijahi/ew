<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuperAdminPanel extends Model
{
    use HasFactory;

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
