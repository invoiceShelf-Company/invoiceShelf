<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'supplier_id' => $this->faker->boolean(70) ? Supplier::factory() : null,
            'created_by' => null,
            'name' => $this->faker->words(3, true),
            'sku' => strtoupper($this->faker->unique()->bothify('SKU-####-??')),
            'description' => $this->faker->optional()->paragraph(),
            'unit' => $this->faker->randomElement(['pcs', 'box', 'kg', 'liter', 'roll']),
            'purchase_price' => $this->faker->numberBetween(5000, 500000),
            'selling_price' => $this->faker->numberBetween(10000, 750000),
            'minimum_stock' => $this->faker->numberBetween(0, 20),
            'is_active' => $this->faker->boolean(85),
        ];
    }
}
