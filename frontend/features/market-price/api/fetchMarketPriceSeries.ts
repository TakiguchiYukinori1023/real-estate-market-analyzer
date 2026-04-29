import type { MarketPriceSeries } from "../types/maketPrice";

type FetchMarketPriceSeriesParams = {
  stationId: number;
  propertyType: string;
  floorAreaBand?: string;
  builtYearBand?: string;
};

export async function fetchMarketPriceSeries({
  stationId,
  propertyType,
  floorAreaBand,
  builtYearBand,
}: FetchMarketPriceSeriesParams): Promise<MarketPriceSeries[]> {
  const params = new URLSearchParams({
    station_id: String(stationId),
    property_type: propertyType,
  });

  if (floorAreaBand) {
    params.append('floor_area_band', floorAreaBand);
  }

  if (builtYearBand) {
    params.append('built_year_band', builtYearBand);
  }

  const url = `http://localhost:8080/api/market-price-series?${params.toString()}`;

  const response = await fetch(url, {
    headers: {
      Accept: 'application/json',
    },
  });

  const text = await response.text();

  if (!response.ok) {
    console.error(text);

    throw new Error('相場推移データの取得に失敗しました');
  }

  try {
    const json = JSON.parse(text);

    return json.data;
  } catch {
    console.error(text);

    throw new Error('APIレスポンスがJSON形式ではありません');
  }
}