export type PropertyType = 'mansion' | 'house';

export type FloorAreaBand = 'under30' | '30_50' | '50_70' | '70_90' | '90_120' | 'over120';

export type BuiltYearBand = '0_5' | '6_10' | '11_20' | '21_30' | 'over31';

export type MarketPriceSeries = {
  target_month: string;
  median_price_per_sqm: number;
  p25_price_per_sqm: number;
  p75_price_per_sqm: number;
  sample_count: number;
  floor_area_band: FloorAreaBand;
  built_year_band: BuiltYearBand;
}

export type MarketPriceSeriesResponse = {
  data: MarketPriceSeries[];
};