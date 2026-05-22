export interface User {
  id: number;
  firstName: string;
  lastName: string;
  email: string;
  roles: string[];
}

export interface StorySummary {
  id: number;
  title: string;
  description: string;
  coverImage: string;
  category: string;
  ageRange: string;
  pageCount: number;
  isFavorite?: boolean;
  lastPageNumber?: number;
}

export interface StoryPage {
  id: number;
  pageNumber: number;
  content: string;
  illustration: string;
}

export interface StoryDetail extends StorySummary {
  pages: StoryPage[];
  lastPageNumber?: number;
}

export interface AdminStory extends StoryDetail {
  status: string;
  createdAt: string;
  updatedAt: string;
}

export interface StoryInput {
  title: string;
  description: string;
  coverImage: string;
  status: string;
  category: string;
  ageRange: string;
}

export interface PageInput {
  pageNumber: number;
  content: string;
  illustration: string;
}

export interface AdminUser {
  id: number;
  firstName: string;
  lastName: string;
  email: string;
  roles: string[];
  isAdmin: boolean;
  createdAt: string;
  updatedAt: string;
}

export interface AdminUserInput {
  firstName: string;
  lastName: string;
  email: string;
  password: string;
  isAdmin: boolean;
}

export interface ReadingHistoryItem {
  storyId: number;
  title: string;
  lastPageNumber: number;
  pageCount: number;
  isCompleted: boolean;
  startedAt: string;
  lastReadAt: string;
}

export interface ApiError {
  error?: string;
  message?: string;
  detail?: string;
}
