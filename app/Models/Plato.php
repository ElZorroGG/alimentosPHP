<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'menu_plato')
            ->withTimestamps();
    }
}
