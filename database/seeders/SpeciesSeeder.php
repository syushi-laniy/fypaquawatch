<?php

namespace Database\Seeders;

use App\Models\Species;
use Illuminate\Database\Seeder;

class SpeciesSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name' => 'Guppy', 'min_ph' => 6.8, 'max_ph' => 7.8, 'image_path' => 'images/species/guppy.jpeg'],
            ['name' => 'Betta Fish', 'min_ph' => 6.5, 'max_ph' => 7.5, 'image_path' => 'images/species/betta.jpeg'],
            ['name' => 'Goldfish', 'min_ph' => 7.0, 'max_ph' => 8.0, 'image_path' => 'images/species/goldfish.jpeg'],
            ['name' => 'Molly', 'min_ph' => 7.0, 'max_ph' => 8.0, 'image_path' => 'images/species/molly.jpeg'],
            ['name' => 'Platy', 'min_ph' => 7.0, 'max_ph' => 8.2, 'image_path' => 'images/species/platy.jpeg'],
            ['name' => 'Neon Tetra', 'min_ph' => 6.0, 'max_ph' => 7.0, 'image_path' => 'images/species/neon-tetra.jpeg'],
            ['name' => 'Zebra Danio', 'min_ph' => 6.5, 'max_ph' => 7.5, 'image_path' => 'images/species/zebra-danio.jpeg'],
            ['name' => 'Cherry Shrimp', 'min_ph' => 6.5, 'max_ph' => 7.5, 'image_path' => 'images/species/cherry-shrimp.jpeg'],
            ['name' => 'Mystery Snail', 'min_ph' => 7.0, 'max_ph' => 8.0, 'image_path' => 'images/species/mystery-snail.jpeg'],
            ['name' => 'Corydoras', 'min_ph' => 6.5, 'max_ph' => 7.5, 'image_path' => 'images/species/corydoras.jpeg'],
            ['name' => 'Discus', 'min_ph' => 5.5, 'max_ph' => 6.5, 'image_path' => 'images/species/discus.jpeg'],
            ['name' => 'African Cichlid', 'min_ph' => 7.8, 'max_ph' => 8.6, 'image_path' => 'images/species/african-cichlid.jpeg'],
        ];

        foreach ($rows as $row) {
            Species::updateOrCreate(
                ['name' => $row['name']],
                $row + ['description' => null]
            );
        }
    }
}
