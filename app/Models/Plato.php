<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plato extends Model
{
    protected $fillable = ['user_id', 'nombre', 'descripcion'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class, 'plato_producto')
            ->withPivot('cantidad_gramos')
            ->withTimestamps();
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }
}
