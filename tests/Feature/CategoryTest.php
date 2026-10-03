<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /** Create and authenticate an admin user for each test. */
    private function actingAsAdmin(): static
    {
        return $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('categories.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_can_view_category_list(): void
    {
        Category::factory()->count(3)->create();

        $response = $this->actingAsAdmin()->get(route('categories.index'));

        $response->assertOk();
        $response->assertViewIs('categories.index');
    }

    public function test_user_can_create_a_category(): void
    {
        $response = $this->actingAsAdmin()->post(route('categories.store'), [
            'name' => 'Elektronik',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'Elektronik',
            'slug' => 'elektronik',
        ]);
    }

    public function test_user_cannot_create_category_without_name(): void
    {
        $response = $this->actingAsAdmin()->post(route('categories.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseMissing('categories', ['name' => '']);
    }

    public function test_category_name_must_be_unique(): void
    {
        Category::factory()->create(['name' => 'Elektronik', 'slug' => 'elektronik']);

        $response = $this->actingAsAdmin()->post(route('categories.store'), [
            'name' => 'Elektronik',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_user_can_update_a_category(): void
    {
        $category = Category::factory()->create(['name' => 'Lama', 'slug' => 'lama']);

        $response = $this->actingAsAdmin()->put(route('categories.update', $category), [
            'name' => 'Baru',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Baru',
            'slug' => 'baru',
        ]);
    }

    public function test_user_can_delete_unused_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAsAdmin()->delete(route('categories.destroy', $category));

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_user_cannot_delete_category_with_products(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id, 'supplier_id' => null]);

        $response = $this->actingAsAdmin()->delete(route('categories.destroy', $category));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
}
