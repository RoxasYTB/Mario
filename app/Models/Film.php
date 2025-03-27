<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    use HasFactory;

    // Définir les propriétés qui peuvent être assignées en masse
    protected $fillable = [
        'title',
        'description',
        'releaseYear',
        'languageId',
        'originalLanguageId',
        'rentalDuration',
        'rentalRate',
        'length',
        'replacementCost',
        'rating',
        'specialFeatures', // Si vous avez des fonctionnalités spéciales
    ];

    // Si vous avez besoin de définir des relations, vous pouvez le faire ici
    // Par exemple, si un film a un réalisateur, vous pouvez définir une relatio

    // Vous pouvez également ajouter des méthodes pour la validation ou d'autres logiques
    public static function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'releaseYear' => 'required|integer',
            'languageId' => 'required|integer',
            'originalLanguageId' => 'nullable|integer',
            'rentalDuration' => 'required|integer',
            'rentalRate' => 'required|numeric',
            'length' => 'required|integer',
            'replacementCost' => 'required|numeric',
            'rating' => 'nullable|string|max:10',
            'specialFeatures' => 'nullable|string',
        ];
    }
} 