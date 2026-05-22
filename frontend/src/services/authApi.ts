import type { User } from '../types/api';
import { apiFetch, setToken } from './apiClient';

interface LoginResponse {
  token: string;
}

export async function login(email: string, password: string): Promise<void> {
  const data = await apiFetch<LoginResponse>(
    '/api/auth/login',
    {
      method: 'POST',
      body: JSON.stringify({ email, password }),
    },
    false,
  );
  setToken(data.token);
}

export async function register(
  firstName: string,
  lastName: string,
  email: string,
  password: string,
): Promise<User> {
  return apiFetch<User>(
    '/api/auth/register',
    {
      method: 'POST',
      body: JSON.stringify({ firstName, lastName, email, password }),
    },
    false,
  );
}

export async function fetchMe(): Promise<User> {
  return apiFetch<User>('/api/me');
}
