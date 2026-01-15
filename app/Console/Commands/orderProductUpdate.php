<?php

namespace App\Console\Commands;

use App\Models\OrderProduct;
use Illuminate\Console\Command;

class orderProductUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:order-product-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting ...');
        // Backfill created_at from orders
        OrderProduct::whereNull('created_at')
            ->with('order:id,created_at')
            ->chunkById(500, function ($orderProducts) {
                foreach ($orderProducts as $orderProduct) {
                    if ($orderProduct->order) {
                        $orderProduct->created_at = $orderProduct->order->created_at;
                        $orderProduct->save();
                    }
                }
            });
        // Backfill buying_price from products
        OrderProduct::where(function ($q) {
                $q->whereNull('buying_price')
                    ->orWhere('buying_price', 0);
            })
            ->with('product:id,buy_price')
            ->chunkById(500, function ($orderProducts) {
                foreach ($orderProducts as $orderProduct) {
                    if ($orderProduct->product) {
                        $orderProduct->buying_price = $orderProduct->product->buy_price ?? 0;
                        $orderProduct->saveQuietly();
                    }
                }
            });
        // Backfill price from products
        OrderProduct::where(function ($q) {
                $q->whereNull('price')
                    ->orWhere('price', 0);
            })
            ->with('product:id,price,discount_price')
            ->chunkById(500, function ($orderProducts) {
                foreach ($orderProducts as $orderProduct) {
                    if ($orderProduct->product) {
                        $price = $orderProduct->product->discount_price > 0 ? $orderProduct->product->discount_price : $orderProduct->product->price;
                        $orderProduct->price = $price;
                        $orderProduct->saveQuietly();
                    }
                }
            });
        $this->info('Completed.');
    }
}
