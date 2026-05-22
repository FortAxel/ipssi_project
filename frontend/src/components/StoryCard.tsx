import { useState, type MouseEvent } from 'react';
import { Link } from 'react-router-dom';
import { useFavorites } from '../context/FavoritesContext';
import type { StorySummary } from '../types/api';
import { ProgressRing } from './ProgressRing';
import styles from './StoryCard.module.css';

interface StoryCardProps {
  story: StorySummary;
}

export function StoryCard({ story }: StoryCardProps) {
  const readPages = story.lastPageNumber ?? 0;
  const { isFavorite, toggleFavorite } = useFavorites();
  const favorited = isFavorite(story.id);
  const [favError, setFavError] = useState<string | null>(null);

  async function handleFavoriteClick(e: MouseEvent) {
    e.preventDefault();
    e.stopPropagation();
    setFavError(null);
    try {
      await toggleFavorite(story.id);
    } catch {
      setFavError('Impossible de modifier les favoris.');
    }
  }

  return (
    <article className={styles.cardWrap}>
      <Link to={`/stories/${story.id}/read`} className={styles.card}>
        <div className={styles.imageWrap}>
          <ProgressRing
            current={readPages}
            total={story.pageCount}
            className={styles.progressMobile}
          />
          <img src={story.coverImage} alt="" className={styles.cover} />
          <button
            type="button"
            className={styles.favoriteBtn}
            aria-label={favorited ? 'Retirer des favoris' : 'Ajouter aux favoris'}
            aria-pressed={favorited}
            onClick={handleFavoriteClick}
          >
            <img
              src={favorited ? '/icons/heart-filled.svg' : '/icons/heart-outline.svg'}
              alt=""
              width={32}
              height={32}
            />
          </button>
        </div>
        <div className={styles.body}>
          <div className={styles.titleRow}>
            <h2 className={styles.title}>{story.title}</h2>
            <button
              type="button"
              className={`${styles.favoriteBtn} ${styles.favoriteDesktop}`}
              aria-label={favorited ? 'Retirer des favoris' : 'Ajouter aux favoris'}
              aria-pressed={favorited}
              onClick={handleFavoriteClick}
            >
              <img
                src={favorited ? '/icons/heart-filled.svg' : '/icons/heart-outline.svg'}
                alt=""
                width={32}
                height={32}
              />
            </button>
          </div>
          <p className={styles.description}>{story.description}</p>
          <ProgressRing
            current={readPages}
            total={story.pageCount}
            className={styles.progressDesktop}
          />
        </div>
      </Link>
      {favError && <p className={`errorText ${styles.favError}`}>{favError}</p>}
    </article>
  );
}
