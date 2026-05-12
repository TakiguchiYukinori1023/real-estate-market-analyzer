import type { Station } from '../types/station';

const API_BASE_URL =
  process.env.NEXT_PUBLIC_API_BASE_URL ?? 'http://localhost:8080';

export async function fetchStations(): Promise<Station[]> {
  const url = `${API_BASE_URL}/api/stations`;

  const response = await fetch(url, {
    headers: {
      Accept: 'application/json',
    },
    cache: 'no-store',
  });

  if (!response.ok) {
    throw new Error(`APIエラー: ${response.status}`);
  }

  const json = await response.json();

  return json.data;
}

