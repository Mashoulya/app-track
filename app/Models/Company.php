<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Application;

/**
 * @property int $id
 */
class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'position',
        'location',
        'source',
        'contact',
        'notes',
    ];

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
