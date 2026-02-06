<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Menu extends Model
{
    protected $fillable = ['user_id', 'fecha', 'tipo_comida'];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function platos(): BelongsToMany
    {
        return $this->belongsToMany(Plato::class, 'menu_plato')
            ->withTimestamps();
    }
}
