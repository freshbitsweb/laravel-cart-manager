<?php

namespace Freshbitsweb\LaravelCartManager\Test;

use Carbon\Carbon;

class ClearCartDataCommandTest extends TestCase
{
    /** @test */
    public function clear_cart_command_does_not_remove_valid_data()
    {
        $cart = $this->addACartItem();

        $this->artisan('lcm_carts:clear_old');

        $this->assertDatabaseHas('carts', [
            'id' => 1,
        ]);
    }

    /** @test */
    public function clear_cart_command_removes_invalid_data()
    {
        $cart = $this->addACartItem();

        $validHours = config('cart_manager.cart_data_validity') + 1;

        resolve(config('cart_manager.cart_model'))::where('id', 1)->update(['updated_at' => Carbon::now()->subHours($validHours)]);

        $this->artisan('lcm_carts:clear_old');

        $this->assertDatabaseMissing('carts', [
            'id' => 1,
        ]);
    }
}
