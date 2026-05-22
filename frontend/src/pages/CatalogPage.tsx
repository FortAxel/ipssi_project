import { useCallback, useEffect, useState } from 'react';
import { useLocation } from 'react-router-dom';
import { PageHeader } from '../components/PageHeader';
import { StoryCard } from '../components/StoryCard';
import { useFavorites } from '../context/FavoritesContext';
import { labels } from '../i18n/fr';
import { fetchStories } from '../services/storyApi';
import type { StorySummary } from '../types/api';
import styles from './CatalogPage.module.css';

export function CatalogPage() {
  const location = useLocation();
  const { setFavoriteIds } = useFavorites();
  const [stories, setStories] = useState<StorySummary[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const loadStories = useCallback(async () => {
    setLoading(true);
    setError(null);
    try {
      const items = await fetchStories();
      setStories(items);
      setFavoriteIds(items.filter((s) => s.isFavorite).map((s) => s.id));
    } catch {
      setError('Impossible de charger le catalogue.');
    } finally {
      setLoading(false);
    }
  }, [setFavoriteIds]);

  useEffect(() => {
    if (location.pathname === '/') {
      loadStories();
    }
  }, [location.pathname, loadStories]);

  return (
    <>
      <PageHeader title={labels.catalog} />
      <div className={styles.wrap}>
        {loading && <p className="textSmall">{labels.loading}</p>}
        {error && <p className="errorText">{error}</p>}
        {!loading && !error && stories.length === 0 && (
          <p className="textSmall">{labels.noStories}</p>
        )}
        <div className={styles.grid}>
          {stories.map((story) => (
            <StoryCard key={story.id} story={story} />
          ))}
        </div>
      </div>
    </>
  );
}
