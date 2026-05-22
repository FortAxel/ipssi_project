import type { AdminStory, StoryDetail, StoryInput, StorySummary } from '../types/api';
import { apiFetch } from './apiClient';

interface StoryListResponse {
  items: StorySummary[];
}

export async function fetchStories(search?: string, category?: string): Promise<StorySummary[]> {
  const params = new URLSearchParams();
  if (search) params.set('search', search);
  if (category) params.set('category', category);
  const query = params.toString();
  const data = await apiFetch<StoryListResponse>(`/api/stories${query ? `?${query}` : ''}`);
  return data.items;
}

export async function fetchStory(id: number): Promise<StoryDetail> {
  return apiFetch<StoryDetail>(`/api/stories/${id}`);
}

interface AdminStoryListResponse {
  items: AdminStory[];
}

export async function fetchAdminStories(): Promise<AdminStory[]> {
  const data = await apiFetch<AdminStoryListResponse>('/api/admin/stories');
  return data.items;
}

export async function fetchAdminStory(id: number): Promise<AdminStory> {
  return apiFetch<AdminStory>(`/api/admin/stories/${id}`);
}

export async function createStory(input: StoryInput): Promise<AdminStory> {
  return apiFetch<AdminStory>('/api/admin/stories', {
    method: 'POST',
    body: JSON.stringify(input),
  });
}

export async function updateStory(id: number, input: StoryInput): Promise<AdminStory> {
  return apiFetch<AdminStory>(`/api/admin/stories/${id}`, {
    method: 'PUT',
    body: JSON.stringify(input),
  });
}

export async function deleteStory(id: number): Promise<void> {
  await apiFetch<void>(`/api/admin/stories/${id}`, { method: 'DELETE' });
}
