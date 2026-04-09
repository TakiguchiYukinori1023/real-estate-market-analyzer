<?php

namespace App\Console\Commands;

use App\Services\MarketPriceAggregator;
use Illuminate\Console\Command;

class AggregateMarketPrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'market:aggregate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'property_price_observations から月次相場データを再集計して market_price_series を再生成する';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('月次相場集計を開始します。');

        $createdCount =  (new MarketPriceAggregator())->execute();

        if ($createdCount === 0) {
            $this->warn('集計対象データが存在しません。');
            return self::SUCCESS;
        }

        $this->info("月次相場集計が完了しました。作成件数: {$createdCount} 件");

        return self::SUCCESS;
    }
}
