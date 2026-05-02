'use client';

import {
  LineChart,
  Line,
  XAxis,
  YAxis,
  Tooltip,
  Legend,
  CartesianGrid,
  ResponsiveContainer,
} from 'recharts';

import type { MarketPriceSeries } from '../types/marketPrice';

type Props = {
  data: MarketPriceSeries[];
};

export default function MarketPriceChart({ data }: Props) {
  return (
    <ResponsiveContainer width="100%" height={400}>
      <LineChart data={data}>
        <CartesianGrid strokeDasharray="3 3" />

        <XAxis dataKey="target_month" />

        <YAxis />

        <Tooltip />
        <Legend />

        <Line
          type="monotone"
          dataKey="median_price_per_sqm"
          name="中央値"
        />

        <Line
          type="monotone"
          dataKey="p25_price_per_sqm"
          name="p25"
        />

        <Line
          type="monotone"
          dataKey="p75_price_per_sqm"
          name="p75"
        />
      </LineChart>
    </ResponsiveContainer>
  );
}