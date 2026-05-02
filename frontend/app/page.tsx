import { fetchMarketPriceSeries } from "@/features/market-price/api/fetchMarketPriceSeries";
import MarketPriceChart from "@/features/market-price/components/MarketPriceChart";

export default async function Home() {
  const series =  await fetchMarketPriceSeries({
    stationId: 1,
    propertyType: 'mansion',
    floorAreaBand: '50_70',
    builtYearBand: '6_10',
  });

  return (
    <main className="p-8">
      <h1 className="text-2xl font-bold">不動産相場分析アプリ</h1>
      <p className="mt-4">㎡単価の推移を表示します。</p>

      <div className="mt-8">
        <MarketPriceChart data={series} />
      </div>
    </main>
  )
}