import type { StorySummary } from '../types/api';
import { apiFetch } from './apiClient';

interface FavoriteListResponse {
  items: StorySummary[];
}

export async function fetchFavoriteStories(): Promise<StorySummary[]> {
  const data = await apiFetch<FavoriteListResponse>('/api/favorites');
  return data.items;
}

export async function toggleFavorite(storyId: number): Promise<{ storyId: number; isFavorite: boolean }> {
  return apiFetch('/api/favorites/toggle', {
    method: 'POST',
    body: JSON.stringify({ storyId }),
  });
}
