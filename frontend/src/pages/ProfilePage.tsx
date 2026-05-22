import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { PageHeader } from '../components/PageHeader';
import { useAuth } from '../context/AuthContext';
import { labels } from '../i18n/fr';
import { fetchReadingHistory } from '../services/historyApi';
import type { ReadingHistoryItem } from '../types/api';
import styles from './ProfilePage.module.css';

type ProfileTab = 'account' | 'history';

function formatDate(iso: string): string {
  return new Date(iso).toLocaleString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
}

export function ProfilePage() {
  const { user } = useAuth();
  const [tab, setTab] = useState<ProfileTab>('account');
  const [history, setHistory] = useState<ReadingHistoryItem[]>([]);
  const [loadingHistory, setLoadingHistory] = useState(false);
  const [historyError, setHistoryError] = useState<string | null>(null);

  useEffect(() => {
    if (tab !== 'history') return;
    setLoadingHistory(true);
    setHistoryError(null);
    fetchReadingHistory()
      .then(setHistory)
      .catch(() => setHistoryError(labels.historyLoadError))
      .finally(() => setLoadingHistory(false));
  }, [tab]);

  if (!user) return null;

  return (
    <>
      <PageHeader title={labels.profile} />
      <div className={styles.wrap}>
        <div className={styles.tabs} role="tablist">
          <button
            type="button"
            role="tab"
            aria-selected={tab === 'account'}
            className={tab === 'account' ? styles.tabActive : styles.tab}
            onClick={() => setTab('account')}
          >
            {labels.profileAccount}
          </button>
          <button
            type="button"
            role="tab"
            aria-selected={tab === 'history'}
            className={tab === 'history' ? styles.tabActive : styles.tab}
            onClick={() => setTab('history')}
          >
            {labels.profileHistory}
          </button>
        </div>

        {tab === 'account' && (
          <div className={`card ${styles.panel}`} role="tabpanel">
            <p className={styles.name}>
              {user.firstName} {user.lastName}
            </p>
            <p className="textSmall">{user.email}</p>
          </div>
        )}

        {tab === 'history' && (
          <div className={`card ${styles.panel}`} role="tabpanel">
            {loadingHistory && <p className="textSmall">{labels.loading}</p>}
            {historyError && <p className="errorText">{historyError}</p>}
            {!loadingHistory && !historyError && history.length === 0 && (
              <p className="textSmall">{labels.historyEmpty}</p>
            )}
            {!loadingHistory && !historyError && history.length > 0 && (
              <div className={styles.tableWrap}>
                <table className={styles.table}>
                  <thead>
                    <tr>
                      <th>{labels.historyStory}</th>
                      <th>{labels.historyProgress}</th>
                      <th>{labels.historyStarted}</th>
                      <th>{labels.historyLastRead}</th>
                      <th />
                    </tr>
                  </thead>
                  <tbody>
                    {history.map((row) => (
                      <tr key={row.storyId}>
                        <td>{row.title}</td>
                        <td>
                          {row.isCompleted
                            ? labels.historyCompleted
                            : labels.pageOf(row.lastPageNumber, row.pageCount)}
                        </td>
                        <td>{formatDate(row.startedAt)}</td>
                        <td>{formatDate(row.lastReadAt)}</td>
                        <td>
                          <Link to={`/stories/${row.storyId}/read`} className={styles.readLink}>
                            {labels.read}
                          </Link>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}
          </div>
        )}
      </div>
    </>
  );
}
