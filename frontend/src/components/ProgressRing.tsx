import styles from './ProgressRing.module.css';

interface ProgressRingProps {
  current: number;
  total: number;
  className?: string;
}

export function ProgressRing({ current, total, className }: ProgressRingProps) {
  const safeTotal = Math.max(total, 1);
  const ratio = Math.min(Math.max(current / safeTotal, 0), 1);
  const degrees = ratio * 360;

  return (
    <div
      className={`${styles.ring} ${className ?? ''}`}
      style={{ background: `conic-gradient(var(--color-primary) 0deg ${degrees}deg, #e4e4e4 ${degrees}deg 360deg)` }}
      aria-label={`${current} sur ${total}`}
    >
      <span className={styles.inner}>
        <span className={styles.current}>{current}</span>
        <span className={styles.sep}>sur</span>
        <span className={styles.total}>{total}</span>
      </span>
    </div>
  );
}
