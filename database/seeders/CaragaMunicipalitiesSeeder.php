<?php

namespace Database\Seeders;

use App\Models\Municipality;
use Illuminate\Database\Seeder;

class CaragaMunicipalitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Agusan del Norte' => [
                'Buenavista',
                'Carmen',
                'Jabonga',
                'Kitcharao',
                'Las Nieves',
                'Magallanes',
                'Nasipit',
                'Santiago',
                'Tubay',
                'Remedios T. Romualdez',
            ],
            'Agusan del Sur' => [
                'Bunawan',
                'Esperanza',
                'La Paz',
                'Loreto',
                'Prosperidad',
                'Rosario',
                'San Francisco',
                'San Luis',
                'Santa Josefa',
                'Talacogon',
                'Trento',
                'Veruela',
                'Sibagat',
            ],
            'Surigao del Norte' => [
                'Alegria',
                'Bacuag',
                'Burgos',
                'Claver',
                'Dapa',
                'Del Carmen',
                'General Luna',
                'Gigaquit',
                'Mainit',
                'Malimono',
                'Pilar',
                'Placer',
                'San Benito',
                'San Francisco',
                'San Isidro',
                'Santa Monica',
                'Sison',
                'Socorro',
                'Tagana-An',
                'Tubod',
            ],
            'Surigao del Sur' => [
                'Barobo',
                'Bayabas',
                'Cagwait',
                'Cantilan',
                'Carmen',
                'Carrascal',
                'Cortes',
                'Hinatuan',
                'Lanuza',
                'Lianga',
                'Lingig',
                'Madrid',
                'Marihatag',
                'San Agustin',
                'San Miguel',
                'Tagbina',
                'Tago',
            ],
            'Dinagat Islands' => [
                'Basilisa',
                'Cagdianao',
                'Dinagat',
                'Libjo',
                'Loreto',
                'San Jose',
                'Tubajon',
            ],
        ];

        foreach ($data as $province => $municipalities) {
            foreach ($municipalities as $municipality) {
                Municipality::updateOrCreate(
                    [
                        'province' => $province,
                        'name' => $municipality,
                    ]
                );
            }
        }
    }
}
