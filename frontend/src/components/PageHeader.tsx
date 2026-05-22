import styles from './PageHeader.module.css';

interface PageHeaderProps {
  title: string;
}

export function PageHeader({ title }: PageHeaderProps) {
  return (
    <div className={styles.titleBar}>
      <img src="/icons/sparkle.svg" alt="" className={styles.sparkle} aria-hidden />
      <h1 className={styles.title}>{title}</h1>
      <img src="/icons/sparkle.svg" alt="" className={styles.sparkle} aria-hidden />
    </div>
  );
}
