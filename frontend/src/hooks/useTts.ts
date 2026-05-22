import { useCallback, useEffect, useRef, useState } from 'react';
import { fetchTtsConfig, synthesizeSpeech } from '../services/ttsApi';

type TtsMode = 'api' | 'browser' | 'none';

export function useTts() {
  const [mode, setMode] = useState<TtsMode>('none');
  const [speaking, setSpeaking] = useState(false);
  const [loading, setLoading] = useState(false);
  const audioRef = useRef<HTMLAudioElement | null>(null);

  useEffect(() => {
    let cancelled = false;
    fetchTtsConfig()
      .then((cfg) => {
        if (cancelled) return;
        if (cfg.enabled) {
          setMode('api');
        } else if (typeof window !== 'undefined' && 'speechSynthesis' in window) {
          setMode('browser');
        } else {
          setMode('none');
        }
      })
      .catch(() => {
        if (!cancelled) {
          setMode(
            typeof window !== 'undefined' && 'speechSynthesis' in window ? 'browser' : 'none',
          );
        }
      });
    return () => {
      cancelled = true;
    };
  }, []);

  const stop = useCallback(() => {
    if (audioRef.current) {
      audioRef.current.pause();
      audioRef.current = null;
    }
    if (typeof window !== 'undefined' && 'speechSynthesis' in window) {
      window.speechSynthesis.cancel();
    }
    setSpeaking(false);
    setLoading(false);
  }, []);

  const speakBrowser = useCallback(
    (text: string) => {
      if (typeof window === 'undefined' || !('speechSynthesis' in window) || !text.trim()) {
        return false;
      }
      const utterance = new SpeechSynthesisUtterance(text);
      utterance.lang = 'fr-FR';
      utterance.rate = 0.95;
      utterance.onend = () => setSpeaking(false);
      utterance.onerror = () => setSpeaking(false);
      setSpeaking(true);
      window.speechSynthesis.speak(utterance);
      return true;
    },
    [],
  );

  const speakApi = useCallback(
    async (text: string) => {
      setLoading(true);
      try {
        const { audioBase64, mimeType } = await synthesizeSpeech(text);
        stop();
        const audio = new Audio(`data:${mimeType};base64,${audioBase64}`);
        audioRef.current = audio;
        audio.onended = () => {
          setSpeaking(false);
          audioRef.current = null;
        };
        audio.onerror = () => {
          setSpeaking(false);
          audioRef.current = null;
        };
        setSpeaking(true);
        await audio.play();
        return true;
      } catch {
        setSpeaking(false);
        return speakBrowser(text);
      } finally {
        setLoading(false);
      }
    },
    [speakBrowser, stop],
  );

  const speak = useCallback(
    async (text: string) => {
      if (!text.trim()) return false;
      stop();
      if (mode === 'api') {
        return speakApi(text);
      }
      if (mode === 'browser') {
        return speakBrowser(text);
      }
      return false;
    },
    [mode, speakApi, speakBrowser, stop],
  );

  useEffect(() => () => stop(), [stop]);

  return {
    speak,
    stop,
    speaking,
    loading,
    supported: mode !== 'none',
    mode,
  };
}
