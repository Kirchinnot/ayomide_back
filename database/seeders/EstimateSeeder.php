<?php

namespace Database\Seeders;

use App\Models\Estimate;
use App\Models\Product;
use Illuminate\Database\Seeder;

class EstimateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();

        if ($products->isEmpty()) {
            return;
        }

        // Créer quelques devis d'exemple
        $estimates = [
            [
                'client_name' => 'Jean Dupont',
                'client_email' => 'jean@example.com',
                'client_phone' => '0612345678',
                'client_address' => '123 Rue de la Paix, 75000 Paris',
                'items' => [
                    [
                        'product_id' => $products->first()->id,
                        'product_name' => $products->first()->name,
                        'price_m2' => $products->first()->price_m2,
                        'surface' => 50,
                        'total_price' => $products->first()->price_m2 * 50,
                    ],
                ],
                'total_price' => $products->first()->price_m2 * 50,
                'status' => 'accepted',
            ],
            [
                'client_name' => 'Marie Martin',
                'client_email' => 'marie@example.com',
                'client_phone' => '0687654321',
                'client_address' => '456 Avenue des Champs, 75008 Paris',
                'items' => [
                    [
                        'product_id' => $products[1]->id ?? $products->first()->id,
                        'product_name' => $products[1]->name ?? $products->first()->name,
                        'price_m2' => $products[1]->price_m2 ?? $products->first()->price_m2,
                        'surface' => 30,
                        'total_price' => ($products[1]->price_m2 ?? $products->first()->price_m2) * 30,
                    ],
                ],
                'total_price' => ($products[1]->price_m2 ?? $products->first()->price_m2) * 30,
                'status' => 'sent',
            ],
            [
                'client_name' => 'Pierre Bernard',
                'client_email' => 'pierre@example.com',
                'client_phone' => '0756789012',
                'client_address' => '789 Boulevard Saint-Germain, 75006 Paris',
                'items' => [
                    [
                        'product_id' => $products->first()->id,
                        'product_name' => $products->first()->name,
                        'price_m2' => $products->first()->price_m2,
                        'surface' => 75,
                        'total_price' => $products->first()->price_m2 * 75,
                    ],
                    [
                        'product_id' => $products[1]->id ?? $products->first()->id,
                        'product_name' => $products[1]->name ?? $products->first()->name,
                        'price_m2' => $products[1]->price_m2 ?? $products->first()->price_m2,
                        'surface' => 25,
                        'total_price' => ($products[1]->price_m2 ?? $products->first()->price_m2) * 25,
                    ],
                ],
                'total_price' => ($products->first()->price_m2 * 75) + (($products[1]->price_m2 ?? $products->first()->price_m2) * 25),
                'status' => 'draft',
            ],
            [
                'client_name' => 'Sophie Laurent',
                'client_email' => 'sophie@example.com',
                'client_phone' => '0645123456',
                'client_address' => '321 Rue de Rivoli, 75001 Paris',
                'items' => [
                    [
                        'product_id' => $products->last()->id,
                        'product_name' => $products->last()->name,
                        'price_m2' => $products->last()->price_m2,
                        'surface' => 40,
                        'total_price' => $products->last()->price_m2 * 40,
                    ],
                ],
                'total_price' => $products->last()->price_m2 * 40,
                'status' => 'rejected',
            ],
        ];

        foreach ($estimates as $estimateData) {
            Estimate::create([
                'client_name' => $estimateData['client_name'],
                'client_email' => $estimateData['client_email'],
                'client_phone' => $estimateData['client_phone'],
                'client_address' => $estimateData['client_address'],
                'items' => json_encode($estimateData['items']),
                'total_price' => $estimateData['total_price'],
                'status' => $estimateData['status'],
                'valid_until' => now()->addDays(30),
                'notes' => 'Devis de démonstration',
            ]);
        }
    }
}
