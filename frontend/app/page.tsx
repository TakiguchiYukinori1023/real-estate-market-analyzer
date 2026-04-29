import { fetchMarketPriceSeries } from "@/features/market-price/api/fetchMarketPriceSeries";

export default async function Home() {
  const series =  await fetchMarketPriceSeries({
    stationId: 1,
    propertyType: 'mansion',
    floorAreaBand: '50_70',
    builtYearBand: '6_10',
  });

  console.log(series);

  return (
    <main className="p-8">
      <h1  className="text-2xl font-bold">不動産相場分析アプリ</h1>
      <p className="mt-4">API接続確認中</p>
    </main>
  )
}