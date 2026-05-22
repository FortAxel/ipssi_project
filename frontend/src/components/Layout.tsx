import { useState } from 'react';
import { Link, NavLink, Outlet } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { labels } from '../i18n/fr';
import styles from './Layout.module.css';

export function Layout() {
  const { user, logout, isAdmin } = useAuth();
  const [menuOpen, setMenuOpen] = useState(false);

  function closeMenu() {
    setMenuOpen(false);
  }

  function toggleMenu() {
    setMenuOpen((o) => !o);
  }

  return (
    <div className={styles.shell}>
      <header className={styles.header}>
        <div className={styles.headerLeft}>
          <button
            type="button"
            className={styles.menuToggle}
            aria-label={labels.menu}
            aria-expanded={menuOpen}
            onClick={toggleMenu}
          >
            <img src="/icons/menu.svg" alt="" width={28} height={28} />
          </button>

          <nav className={`${styles.nav} ${menuOpen ? styles.navOpen : ''}`} aria-label={labels.menu}>
            <NavLink
              to="/"
              end
              className={({ isActive }) =>
                `btn btnSecondary ${styles.navLink} ${isActive ? styles.navLinkActive : ''}`
              }
              onClick={closeMenu}
            >
              {labels.catalog}
            </NavLink>
            <NavLink
              to="/favorites"
              className={({ isActive }) =>
                `btn btnFavorite ${styles.navLink} ${isActive ? styles.navLinkActive : ''}`
              }
              onClick={closeMenu}
            >
              {labels.favorites}
            </NavLink>
            {isAdmin && (
              <NavLink
                to="/admin"
                className={({ isActive }) =>
                  `btn btnPrimary ${styles.navLink} ${isActive ? styles.navLinkActive : ''}`
                }
                onClick={closeMenu}
              >
                {labels.admin}
              </NavLink>
            )}
            {user && (
              <NavLink
                to="/profile"
                className={({ isActive }) =>
                  `btn btnProfile ${styles.navLink} ${styles.onlyMobile} ${isActive ? styles.navLinkActive : ''}`
                }
                onClick={closeMenu}
              >
                {labels.profile}
              </NavLink>
            )}
            {user ? (
              <button
                type="button"
                className={`btn btnNeutral ${styles.navLink} ${styles.onlyMobile}`}
                onClick={() => {
                  closeMenu();
                  logout();
                }}
              >
                {labels.logout}
              </button>
            ) : (
              <NavLink
                to="/login"
                className={`btn btnNeutral ${styles.navLink} ${styles.onlyMobile}`}
                onClick={closeMenu}
              >
                {labels.login}
              </NavLink>
            )}
          </nav>
        </div>

        <div className={styles.headerRight}>
          {user && (
            <NavLink
              to="/profile"
              className={({ isActive }) =>
                `btn btnProfile ${styles.navLink} ${styles.onlyDesktop} ${isActive ? styles.navLinkActive : ''}`
              }
              onClick={closeMenu}
            >
              {labels.profile}
            </NavLink>
          )}
          {user && (
            <button
              type="button"
              className={`btn btnNeutral ${styles.navLink} ${styles.onlyDesktop}`}
              onClick={() => logout()}
            >
              {labels.logout}
            </button>
          )}
          <Link to="/" className={styles.logoLink} onClick={closeMenu}>
            <img src="/logo.png" alt={labels.appName} className={styles.logo} />
          </Link>
        </div>
      </header>

      {menuOpen && (
        <button
          type="button"
          className={styles.menuBackdrop}
          aria-label="Fermer le menu"
          onClick={closeMenu}
        />
      )}

      <main className={styles.main}>
        <Outlet />
      </main>
    </div>
  );
}
