<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoldPriceService
{
    /**
     * Get the current live gold price per gram in IDR using GoldAPI.io.
     * Fetches USD price, then converts to IDR using free open.er-api.com.
     * Uses caching to avoid rate limits and improve performance.
     *
     * @return float|null
     */
    public static function getPricePerGram()
    {
        return Cache::remember('gold_price_per_gram', 60 * 60 * 6, function () {
            try {
                $apiKey = config('services.goldapi.key');
                
                if (empty($apiKey)) {
                    Log::warning('GOLDAPI_KEY is not set in .env file.');
                } else {
                    // Step 1: Get Gold Price in USD per gram
                    $response = Http::withHeaders([
                        'x-access-token' => $apiKey,
                        'Content-Type' => 'application/json'
                    ])->get('https://www.goldapi.io/api/XAU/USD');
                    
                    if ($response->successful()) {
                        $data = $response->json();
                        
                        // Extract 24k gold price per gram in USD
                        if (isset($data['price_gram_24k'])) {
                            $priceUsd = (float) $data['price_gram_24k'];

                            // Step 2: Convert USD to IDR
                            $forexResponse = Http::get('https://open.er-api.com/v6/latest/USD');
                            if ($forexResponse->successful()) {
                                $forexData = $forexResponse->json();
                                $idrRate = $forexData['rates']['IDR'] ?? null;

                                if ($idrRate) {
                                    return $priceUsd * $idrRate;
                                }
                            }
                            
                            // If forex fails, use an estimated rate
                            Log::warning('Forex API failed, using estimated rate for Gold price conversion.');
                            return $priceUsd * 16000;
                        }
                    }
                    
                    Log::warning('Failed to fetch from GoldAPI.io', [
                        'response' => $response->body(),
                        'status' => $response->status()
                    ]);
                }
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
