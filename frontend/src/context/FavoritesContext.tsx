import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
  type ReactNode,
} from 'react';
import { toggleFavorite as apiToggleFavorite } from '../services/favoriteApi';
import { useAuth } from './AuthContext';

interface FavoritesContextValue {
  favoriteIds: number[];
  toggleFavorite: (storyId: number) => Promise<boolean>;
  isFavorite: (storyId: number) => boolean;
  setFavoriteIds: (ids: number[]) => void;
}

const FavoritesContext = createContext<FavoritesContextValue | null>(null);

export function FavoritesProvider({ children }: { children: ReactNode }) {
  const { user } = useAuth();
  const [favoriteIds, setFavoriteIds] = useState<number[]>([]);

  useEffect(() => {
    if (!user) {
      setFavoriteIds([]);
    }
  }, [user]);

  const toggleFavorite = useCallback(async (storyId: number) => {
    const result = await apiToggleFavorite(storyId);
    setFavoriteIds((ids) =>
      result.isFavorite
        ? [...new Set([...ids, storyId])]
        : ids.filter((id) => id !== storyId),
    );
    return result.isFavorite;
  }, []);

  const isFavorite = useCallback(
    (storyId: number) => favoriteIds.includes(storyId),
    [favoriteIds],
  );

  const value = useMemo(
    () => ({ favoriteIds, toggleFavorite, isFavorite, setFavoriteIds }),
    [favoriteIds, toggleFavorite, isFavorite],
  );

  return <FavoritesContext.Provider value={value}>{children}</FavoritesContext.Provider>;
}

export function useFavorites(): FavoritesContextValue {
  const ctx = useContext(FavoritesContext);
  if (!ctx) {
    throw new Error('useFavorites must be used within FavoritesProvider');
  }
  return ctx;
}
