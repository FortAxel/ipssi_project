import { apiFetch } from './apiClient';

export async function fetchReadingProgress(storyId: number): Promise<{
  storyId: number;
  lastPageNumber: number;
  pageCount: number;
}> {
  return apiFetch(`/api/reading-progress/${storyId}`);
}

export async function saveReadingProgress(
  storyId: number,
  lastPageNumber: number,
): Promise<{ storyId: number; lastPageNumber: number; pageCount: number }> {
  return apiFetch(`/api/reading-progress/${storyId}`, {
    method: 'PUT',
    body: JSON.stringify({ lastPageNumber }),
  });
}
