<?php

namespace App\Services\DeFi;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class DeFiMarketDataService
{
    public function single(string $slug)
    {
        $single = base64_decode($slug);

        $cacheKey = 'defi-market-data-single-new-' . md5($slug);

        return Cache::remember($cacheKey, 15, function () use ($single) {

            $http = Http::get('https://api.mobula.io/api/1/market/data', [
                'asset' => $single,
            ]);

            if ($http->failed()) {
                return [];
            }

            return $http->json('data');
        });
    }

    public function data()
    {
        $cacheKey = 'defi-market-data' . md5($this->assets());

        return Cache::remember($cacheKey, 15, function () {

            $http = Http::get('https://api.mobula.io/api/1/market/multi-data', [
                'assets' => $this->assets(),
            ]);

            if ($http->failed()) {
                return [];
            }

            return $http->json('data');
        });
    }

    public function assets(): string
    {
        return 'Bitcoin,Dogcoin,Ethereum,Cardano,Polkadot,Uniswap,Chainlink,Litecoin,Stellar,Luna,Filecoin,Tron,Monero,Tezos,Neo,Algorand,Compound,Yearn.finance,Aave,The Graph,SushiSwap,Decentraland,Enjin Coin,Chiliz,Flow,Theta Network,Helium,Decred,Horizen,Qtum,ICON,OMG Network,0x,Loopring,Reserve Rights,SwissBorg,Ankr,Origin Protocol,Render Token,Orchid,Livepeer,Streamr,Numeraire,Keep Network,Request Network,Storj,Bluzelle';
    }
}
