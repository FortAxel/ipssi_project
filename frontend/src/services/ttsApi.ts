import { apiFetch } from './apiClient';

export interface TtsConfig {
  enabled: boolean;
  provider: string;
  voice: string | null;
  rate: string | null;
}

export interface TtsSynthesizeResponse {
  audioBase64: string;
  mimeType: string;
  provider: string;
}

export async function fetchTtsConfig(): Promise<TtsConfig> {
  return apiFetch<TtsConfig>('/api/tts/config');
}

export async function synthesizeSpeech(text: string): Promise<TtsSynthesizeResponse> {
  return apiFetch<TtsSynthesizeResponse>('/api/tts/synthesize', {
    method: 'POST',
    body: JSON.stringify({ text }),
  });
}
