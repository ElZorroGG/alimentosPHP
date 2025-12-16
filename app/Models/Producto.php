<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Producto extends Model
{
    protected $fillable = [
        'nombre',
        'categoria_id',
        'user_id',
        'es_personalizado',
        'calorias',
        'grasa_total',
        'grasa_saturada',
        'grasa_monoinsaturada',
        'grasa_poliinsaturada',
        'grasa_trans',
        'colesterol',
        'carbohidratos',
        'fibra',
        'proteinas',
    ];

    protected $casts = [
        'es_personalizado' => 'boolean',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function platos(): BelongsToMany
    {
        return $this->belongsToMany(Plato::class, 'plato_producto')
            ->withPivot('cantidad_gramos')
            ->withTimestamps();
    }
}
