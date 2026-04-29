export type MarketPriceSeries = {
  target_month: string;
  median_price_per_sqm: number;
  p25_price_per_sqm: number;
  p75_price_per_sqm: number;
  sample_count: number;
  floor_area_band: string;
  built_year_band: string;
}