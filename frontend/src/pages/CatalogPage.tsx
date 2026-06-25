import { useCallback, useEffect, useMemo, useState } from 'react';
import { useLocation } from 'react-router-dom';
import { CatalogFilters } from '../components/CatalogFilters';
import { PageHeader } from '../components/PageHeader';
import { StoryCard } from '../components/StoryCard';
import { useFavorites } from '../context/FavoritesContext';
import { labels } from '../i18n/fr';
import { fetchStories } from '../services/storyApi';
import type { StorySummary } from '../types/api';
import {
  categoriesPresentIn,
  filterStoriesByCategories,
  pruneCategorySelection,
} from '../utils/catalogFilters';
import styles from './CatalogPage.module.css';

const SEARCH_DEBOUNCE_MS = 300;

export function CatalogPage() {
  const location = useLocation();
  const { setFavoriteIds } = useFavorites();
  const [catalogStories, setCatalogStories] = useState<StorySummary[]>([]);
  const [selectedCategories, setSelectedCategories] = useState<Set<string>>(() => new Set());
  const [search, setSearch] = useState('');
  const [debouncedSearch, setDebouncedSearch] = useState('');
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const timer = window.setTimeout(() => setDebouncedSearch(search.trim()), SEARCH_DEBOUNCE_MS);
    return () => window.clearTimeout(timer);
  }, [search]);

  const loadStories = useCallback(async () => {
    setLoading(true);
    setError(null);
    try {
      const items = await fetchStories(debouncedSearch || undefined);
      setCatalogStories(items);
      setFavoriteIds(items.filter((s) => s.isFavorite).map((s) => s.id));
    } catch {
      setError('Impossible de charger le catalogue.');
    } finally {
      setLoading(false);
    }
  }, [debouncedSearch, setFavoriteIds]);

  useEffect(() => {
    if (location.pathname === '/') {
      loadStories();
    }
  }, [location.pathname, loadStories]);

  const availableCategories = useMemo(
    () => categoriesPresentIn(catalogStories),
    [catalogStories],
  );

  useEffect(() => {
    setSelectedCategories((prev) => pruneCategorySelection(prev, availableCategories));
  }, [availableCategories]);

  const visibleStories = useMemo(
    () => filterStoriesByCategories(catalogStories, selectedCategories),
    [catalogStories, selectedCategories],
  );

  function handleCategoryToggle(category: string) {
    setSelectedCategories((prev) => {
      const next = new Set(prev);
      if (next.has(category)) {
        next.delete(category);
      } else {
        next.add(category);
      }
      return next;
    });
  }

  const hasActiveFilter = debouncedSearch !== '' || selectedCategories.size > 0;
  const emptyMessage = hasActiveFilter ? labels.noSearchResults : labels.noStories;

  return (
    <>
      <PageHeader title={labels.catalog} />
      <div className={styles.wrap}>
        <CatalogFilters
          search={search}
          availableCategories={availableCategories}
          selectedCategories={selectedCategories}
          onSearchChange={setSearch}
          onCategoryToggle={handleCategoryToggle}
        />
        {loading && <p className="textSmall">{labels.loading}</p>}
        {error && <p className="errorText">{error}</p>}
        {!loading && !error && visibleStories.length === 0 && (
          <p className="textSmall">{emptyMessage}</p>
        )}
        <div className={styles.grid}>
          {visibleStories.map((story) => (
            <StoryCard key={story.id} story={story} />
          ))}
        </div>
      </div>
    </>
  );
}
