import { Link, Outlet } from 'react-router-dom';
import { labels } from '../i18n/fr';
import styles from './AuthLayout.module.css';

export function AuthLayout() {
  return (
    <div className={styles.shell}>
      <header className={styles.header}>
        <Link to="/login" className={styles.logoLink}>
          <img src="/logo.png" alt={labels.appName} className={styles.logo} />
        </Link>
      </header>
      <main className={styles.main}>
        <Outlet />
      </main>
    </div>
  );
}
