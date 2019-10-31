<?php

namespace Freshbitsweb\LaravelCartManager\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class ClearCartDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lcm_carts:clear_old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes the cart data that was updated before the number of hours specified in cart_data_validity config.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (! Schema::hasTable('carts')) {
            $this->error('No carts table found.');
            exit;
        }

        $this->comment('Cleaning carts data...');

        $validHours = config('cart_manager.cart_data_validity');

        $query = resolve(config('cart_manager.cart_model'))::where('updated_at', '<', Carbon::now()->subHours($validHours));

        $cartIds = $query->get(['id']);

        if ($cartIds->isNotEmpty()) {
            $cartsDeleted = $query->delete();

            resolve(config('cart_manager.cart_item_model'))::whereIn('cart_id', $cartIds->pluck('id')->toArray())->delete();

            $this->info("$cartsDeleted older cart record(s) removed from the table.");
        } else {
            $this->info('No older cart records found.');
        }

        $this->comment('Task complete.');
    }
}
