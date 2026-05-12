import { redirect } from 'next/navigation';

// API
import { fetchMarketPriceSeries } from '@/features/market-price/api/fetchMarketPriceSeries';
import { fetchStations } from '@/features/station/api/fetchStations';

// components
import MarketPriceChart from '@/features/market-price/components/MarketPriceChart';
import MarketPriceFilterForm from '@/features/market-price/components/MarketPriceFilterForm';

// types
import type {
  PropertyType,
  FloorAreaBand,
  BuiltYearBand,
} from '@/features/market-price/types/marketPrice';

type SearchParamValue = string | string[] | undefined;

type SearchParams = {
  station_id?: SearchParamValue;
  property_type?: SearchParamValue;
  floor_area_band?: SearchParamValue;
  built_year_band?: SearchParamValue;
}

type Props = {
  searchParams: Promise<SearchParams>;
};

/**
 * URLクエリパラメータから単一の文字列だけを取り出す
 *
 * 同じキーが複数指定された場合は string[] になる可能性があるため、
 * この画面では string のみを有効値として扱う
 *
 * @param value searchParams から取得した値
 * @returns 単一の文字列、または undefined
 */
function getSingleParam(value: SearchParamValue): string | undefined {
  return typeof value === 'string' ? value : undefined;
}

/**
 * URLの searchParams から取得した station_id を安全な number に変換する
 *
 * - string / string[] / undefined を受け取る（Next.js仕様）
 * - 正の整数のみ許可する
 * - 不正値（NaN / 少数 / 負数 / 配列）は null を返す
 *
 * @param value searchParams.station_id
 * @returns 有効な駅ID or null
 */
function parseStationId(value: string | string[] | undefined): number | null {
  if (typeof value !== 'string') {
    return null;
  }

  const num = Number(value);

  if (!Number.isInteger(num) || num <= 0) {
    return null;
  }

  return num;
}

/**
 * URLパラメータは不正な値が入る可能性があるため
 * PropertyTypeとして有効な値かを判定する
 */
function isPropertyType(value: string | undefined): value is PropertyType {
  return value === 'mansion' || value === 'house';
}

/**
 * URLパラメータは不正な値が入る可能性があるため
 * FloorAreaBandとして有効な値かを判定する
 */
function isFloorAreaBand(value: string | undefined): value is FloorAreaBand {
  return [
    'under30',
    '30_50',
    '50_70',
    '70_90',
    '90_120',
    'over120',
  ].includes(value ?? '');
}

/**
 * URLパラメータは不正な値が入る可能性があるため
 * BuiltYearBandとして有効な値かを判定する
 */
function isBuiltYearBand(value: string | undefined): value is BuiltYearBand {
  return [
    '0_5',
    '6_10',
    '11_20',
    '21_30',
    'over31',
  ].includes(value ?? '');
}

export default async function Home({ searchParams }: Props) {
  const params = await searchParams;

  const stations = await fetchStations();

  const defaultStationId = stations[0]?.id;

  if (!defaultStationId) {
    throw new Error('駅データが存在しません');
  }

  const stationIdParam = getSingleParam(params.station_id);
  const propertyTypeParam = getSingleParam(params.property_type);
  const floorAreaBandParam = getSingleParam(params.floor_area_band);
  const builtYearBandParam = getSingleParam(params.built_year_band);

  const parsedStationId = parseStationId(stationIdParam);

  const stationId =
    parsedStationId !== null && stations.some((s) => s.id === parsedStationId)
      ? parsedStationId
      : defaultStationId;

  const propertyType: PropertyType = isPropertyType(propertyTypeParam)
    ? propertyTypeParam
    : 'mansion';

  // TODO:
  // URLに floor_area_band が存在しない場合でも、
  // 現状は固定値で絞り込んでいる。
  // 将来的には「指定なし」やURLへの明示的な初期値付与を検討する。
  const floorAreaBand: FloorAreaBand = isFloorAreaBand(floorAreaBandParam)
   ? floorAreaBandParam
   : '50_70';

  // TODO:
  // URLに built_year_band が存在しない場合でも、
  // 現状は固定値で絞り込んでいる。
  // 将来的には「指定なし」やURLへの明示的な初期値付与を検討する。
  const builtYearBand: BuiltYearBand = isBuiltYearBand(builtYearBandParam)
   ? builtYearBandParam
   : '6_10';

  const canonicalParams = new URLSearchParams({
    station_id: String(stationId),
    property_type: propertyType,
    floor_area_band: floorAreaBand,
    built_year_band: builtYearBand,
  });

  const currentParams = new URLSearchParams();

  if (stationIdParam !== undefined) {
    currentParams.set('station_id', stationIdParam);
  }

  if (propertyTypeParam !== undefined) {
    currentParams.set('property_type', propertyTypeParam);
  }

  if (floorAreaBandParam !== undefined) {
    currentParams.set('floor_area_band', floorAreaBandParam);
  }

  if (builtYearBandParam !== undefined) {
    currentParams.set('built_year_band', builtYearBandParam);
  }

  if (currentParams.toString() !== canonicalParams.toString()) {
    redirect(`/?${canonicalParams.toString()}`);
  }

  const series =  await fetchMarketPriceSeries({
    stationId,
    propertyType,
    floorAreaBand,
    builtYearBand,
  });

  return (
    <main className="p-8">
      <h1 className="text-2xl font-bold">不動産相場分析アプリ</h1>
      <p className="mt-4">㎡単価の推移を表示します。</p>

      <div className="mt-8">
        <MarketPriceFilterForm
          stations={stations}
          selectedStationId={stationId}
          selectedPropertyType={propertyType}
          selectedFloorAreaBand={floorAreaBand}
          selectedBuiltYearBand={builtYearBand}
        />
      </div>

      <div className="mt-8">
        <MarketPriceChart data={series} />
      </div>
    </main>
  )
}