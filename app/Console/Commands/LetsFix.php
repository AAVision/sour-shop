<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class LetsFix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:lets-fix';

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
        $settings = \App\Models\GeneraleSetting::first();

        $settings->update([
            'product_description' => 'Product name: {product_name}. Short description: {short_description}. Write a long, SEO-friendly product description that includes relevant keywords, highlights unique features, and encourages buyers to take action.',
            'page_description' => 'The page title is {title}. Generate a well-structured, professional, and legally appropriate long content for this page, ensuring it covers all important points relevant to {title}.',
        ]);
    }
}
