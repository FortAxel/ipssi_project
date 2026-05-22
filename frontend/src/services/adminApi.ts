import type { AdminUser, AdminUserInput, PageInput, StoryPage } from '../types/api';
import { apiFetch } from './apiClient';

interface AdminUserListResponse {
  items: AdminUser[];
}

export async function fetchAdminUsers(): Promise<AdminUser[]> {
  const data = await apiFetch<AdminUserListResponse>('/api/admin/users');
  return data.items;
}

export async function createAdminUser(input: AdminUserInput): Promise<AdminUser> {
  return apiFetch<AdminUser>('/api/admin/users', {
    method: 'POST',
    body: JSON.stringify(input),
  });
}

export async function updateAdminUser(
  id: number,
  input: Partial<AdminUserInput>,
): Promise<AdminUser> {
  return apiFetch<AdminUser>(`/api/admin/users/${id}`, {
    method: 'PUT',
    body: JSON.stringify(input),
  });
}

export async function deleteAdminUser(id: number): Promise<void> {
  await apiFetch<void>(`/api/admin/users/${id}`, { method: 'DELETE' });
}

export async function createPage(storyId: number, input: PageInput): Promise<StoryPage> {
  return apiFetch<StoryPage>(`/api/admin/stories/${storyId}/pages`, {
    method: 'POST',
    body: JSON.stringify(input),
  });
}

export async function updatePage(
  storyId: number,
  pageId: number,
  input: PageInput,
): Promise<StoryPage> {
  return apiFetch<StoryPage>(`/api/admin/stories/${storyId}/pages/${pageId}`, {
    method: 'PUT',
    body: JSON.stringify(input),
  });
}

export async function deletePage(storyId: number, pageId: number): Promise<void> {
  await apiFetch<void>(`/api/admin/stories/${storyId}/pages/${pageId}`, { method: 'DELETE' });
}
