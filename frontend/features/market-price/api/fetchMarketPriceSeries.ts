import type {
  BuiltYearBand,
  FloorAreaBand,
  MarketPriceSeries,
  PropertyType,
} from "../types/maketPrice";

type FetchMarketPriceSeriesParams = {
  stationId: number;
  propertyType: PropertyType;
  floorAreaBand?: FloorAreaBand;
  builtYearBand?: BuiltYearBand;
};

const API_BASE_URL =
  process.env.NEXT_PUBLIC_API_BASE_URL ?? 'http://localhost:8080';

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

  const url = `${API_BASE_URL}/api/market-price-series?${params.toString()}`;

  try {
    const response = await fetch(url, {
      headers: {
        Accept: 'application/json',
      },
    });

    if (!response.ok) {
      throw new Error(`APIエラー: ${response.status}`);
    }

    const json = await response.json();
    return json.data;
  } catch (error) {
    throw error;
  }
}