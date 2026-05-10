<?php

namespace Database\Factories;

use App\Models\Cours;
use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;

class CoursFactory extends Factory
{
    protected $model = Cours::class;

    public function definition()
    {
        return [
            'titre' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'niveau' => $this->faker->randomElement(['debutant', 'intermediaire', 'avance']),
            'categorie' => $this->faker->word,
            'id_enseignant' => Utilisateur::factory()->create(['role' => 'enseignant'])->id,
            'date_creation' => $this->faker->date,
        ];
    }
}