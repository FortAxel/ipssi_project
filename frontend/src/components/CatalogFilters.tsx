import { categoryLabels, labels } from '../i18n/fr';
import type { StoryCategoryValue } from '../constants/storyCategories';
import styles from './CatalogFilters.module.css';

interface CatalogFiltersProps {
  search: string;
  availableCategories: StoryCategoryValue[];
  selectedCategories: ReadonlySet<string>;
  onSearchChange: (value: string) => void;
  onCategoryToggle: (category: string) => void;
}

export function CatalogFilters({
  search,
  availableCategories,
  selectedCategories,
  onSearchChange,
  onCategoryToggle,
}: CatalogFiltersProps) {
  return (
    <div className={styles.bar}>
      <label className={styles.searchWrap}>
        <span className="sr-only">{labels.searchPlaceholder}</span>
        <input
          type="search"
          className={`input ${styles.search}`}
          placeholder={labels.searchPlaceholder}
          value={search}
          onChange={(e) => onSearchChange(e.target.value)}
          autoComplete="off"
        />
      </label>
      {availableCategories.length > 0 && (
        <div className={styles.categories} role="group" aria-label={labels.filterByCategory}>
          {availableCategories.map((value) => {
            const isActive = selectedCategories.has(value);
            return (
              <button
                key={value}
                type="button"
                className={isActive ? styles.chipActive : styles.chip}
                aria-pressed={isActive}
                onClick={() => onCategoryToggle(value)}
              >
                {categoryLabels[value]}
              </button>
            );
          })}
        </div>
      )}
    </div>
  );
}
