<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class GoldPriceService
{
    /**
     * Get the current live gold price per gram in IDR.
     * Uses caching to avoid rate limits and improve performance.
     *
     * @return float|null
     */
    public static function getPricePerGram()
    {
        return Cache::remember('gold_price_per_gram', 60 * 60 * 6, function () {
            try {
                // Scrape harga-emas.org
                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                ])->get('https://harga-emas.org');
                
                if ($response->successful()) {
                    $html = $response->body();
                    $crawler = new Crawler($html);
                    
                    $priceText = null;
                    
                    // Find the table that contains "Antam"
                    $crawler->filter('table')->each(function (Crawler $table) use (&$priceText) {
                        $headerText = $table->filter('tr')->eq(0)->text('');
                        if (stripos($headerText, 'Antam') !== false) {
                            $table->filter('tr')->each(function (Crawler $node) use (&$priceText) {
                                if ($priceText) return;
                                
                                $cols = $node->filter('td');
                                if ($cols->count() >= 2) {
                                    $col0 = trim($cols->eq(0)->text());
                                    // Row for 1 Gram
                                    if ($col0 === '1') {
                                        // Column 1 is usually Antam
                                        $priceText = trim($cols->eq(1)->text());
                                    }
                                }
                            });
                        }
                    });
                    
                    if ($priceText) {
                        // Convert '3.147.000' to float
                        $clean = str_replace(['.', ','], ['', '.'], $priceText);
                        return (float) $clean;
                    }
                }
                
                Log::warning('Failed to scrape or locate 1 gram Antam price from harga-emas.org');
                
            } catch (\Exception $e) {
                Log::error('Exception fetching gold price: ' . $e->getMessage());
            }

            // Fallback: If everything fails, return 1.35m as default
            return 1350000;
        });
    }

    /**
     * Clear the cached gold price.
     */
    public static function flushCache()
    {
        Cache::forget('gold_price_per_gram');
    }
}
