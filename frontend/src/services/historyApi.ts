import type { ReadingHistoryItem } from '../types/api';
import { apiFetch } from './apiClient';

interface ReadingHistoryResponse {
  items: ReadingHistoryItem[];
}

export async function fetchReadingHistory(): Promise<ReadingHistoryItem[]> {
  const data = await apiFetch<ReadingHistoryResponse>('/api/reading-history');
  return data.items;
}
