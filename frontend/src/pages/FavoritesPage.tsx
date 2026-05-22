import { useEffect, useState } from 'react';
import { PageHeader } from '../components/PageHeader';
import { StoryCard } from '../components/StoryCard';
import { useFavorites } from '../context/FavoritesContext';
import { labels } from '../i18n/fr';
import { fetchFavoriteStories } from '../services/favoriteApi';
import type { StorySummary } from '../types/api';
import styles from './CatalogPage.module.css';

export function FavoritesPage() {
  const { favoriteIds, setFavoriteIds } = useFavorites();
  const [stories, setStories] = useState<StorySummary[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    setLoading(true);
    setError(null);
    fetchFavoriteStories()
      .then((items) => {
        setStories(items);
        setFavoriteIds(items.map((s) => s.id));
      })
      .catch(() => setError('Impossible de charger les favoris.'))
      .finally(() => setLoading(false));
  }, [setFavoriteIds]);

  useEffect(() => {
    setStories((prev) => prev.filter((s) => favoriteIds.includes(s.id)));
  }, [favoriteIds]);

  return (
    <>
      <PageHeader title={labels.favorites} />
      <div className={styles.wrap}>
        {loading && <p className="textSmall">{labels.loading}</p>}
        {error && <p className="errorText">{error}</p>}
        {!loading && !error && stories.length === 0 && (
          <p className="textSmall">{labels.noFavorites}</p>
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
