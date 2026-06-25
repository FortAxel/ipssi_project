import { useEffect, useState, type FormEvent } from 'react';
import { Link } from 'react-router-dom';
import { Modal } from '../components/Modal';
import { PageHeader } from '../components/PageHeader';
import { useAuth } from '../context/AuthContext';
import { ApiRequestError } from '../services/apiClient';
import { deleteAccount, updateProfile } from '../services/authApi';
import { labels } from '../i18n/fr';
import { fetchReadingHistory } from '../services/historyApi';
import type { ReadingHistoryItem } from '../types/api';
import modalStyles from '../components/Modal.module.css';
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
  const { user, login, logout, refreshUser } = useAuth();
  const [tab, setTab] = useState<ProfileTab>('account');
  const [history, setHistory] = useState<ReadingHistoryItem[]>([]);
  const [loadingHistory, setLoadingHistory] = useState(false);
  const [historyError, setHistoryError] = useState<string | null>(null);

  const [editOpen, setEditOpen] = useState(false);
  const [email, setEmail] = useState('');
  const [currentPassword, setCurrentPassword] = useState('');
  const [newPassword, setNewPassword] = useState('');
  const [accountMessage, setAccountMessage] = useState<string | null>(null);
  const [accountError, setAccountError] = useState<string | null>(null);
  const [saving, setSaving] = useState(false);

  const [deletePassword, setDeletePassword] = useState('');
  const [deleteOpen, setDeleteOpen] = useState(false);
  const [deleteError, setDeleteError] = useState<string | null>(null);
  const [deleting, setDeleting] = useState(false);

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

  const profileUser = user;

  function openEditModal() {
    setEmail(profileUser.email);
    setCurrentPassword('');
    setNewPassword('');
    setAccountError(null);
    setEditOpen(true);
  }

  function closeEditModal() {
    if (saving) return;
    setEditOpen(false);
    setAccountError(null);
    setCurrentPassword('');
    setNewPassword('');
  }

  function openDeleteModal() {
    setDeletePassword('');
    setDeleteError(null);
    setDeleteOpen(true);
  }

  function closeDeleteModal() {
    if (deleting) return;
    setDeleteOpen(false);
    setDeleteError(null);
    setDeletePassword('');
  }

  async function handleProfileSubmit(e: FormEvent) {
    e.preventDefault();

    setAccountError(null);
    setSaving(true);

    const trimmedEmail = email.trim();
    const emailChanged = trimmedEmail !== profileUser.email;
    const hasNewPassword = newPassword.length > 0;

    if (!emailChanged && !hasNewPassword) {
      setAccountError('Indiquez une nouvelle adresse e-mail ou un nouveau mot de passe.');
      setSaving(false);
      return;
    }

    try {
      await updateProfile({
        currentPassword,
        ...(emailChanged ? { email: trimmedEmail } : {}),
        ...(hasNewPassword ? { newPassword } : {}),
      });

      if (emailChanged) {
        await login(trimmedEmail, hasNewPassword ? newPassword : currentPassword);
      } else {
        await refreshUser();
      }

      setEditOpen(false);
      setCurrentPassword('');
      setNewPassword('');
      setAccountMessage(labels.profileUpdated);
    } catch (err) {
      setAccountError(
        err instanceof ApiRequestError ? err.message : labels.profileUpdateError,
      );
    } finally {
      setSaving(false);
    }
  }

  async function handleDeleteAccount(e: FormEvent) {
    e.preventDefault();

    setDeleteError(null);
    setDeleting(true);
    try {
      await deleteAccount(deletePassword);
      logout();
    } catch (err) {
      setDeleteError(
        err instanceof ApiRequestError ? err.message : 'Suppression impossible.',
      );
    } finally {
      setDeleting(false);
    }
  }

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
          <div className={styles.accountStack} role="tabpanel">
            <div className={`card ${styles.panel}`}>
              <div className={styles.panelHeader}>
                <button type="button" className="btn btnPrimary" onClick={openEditModal}>
                  {labels.editProfile}
                </button>
              </div>
              <p className={styles.name}>
                {user.firstName} {user.lastName}
              </p>
              <p className={styles.emailLine}>{user.email}</p>
              <p className="textSmall">
                <Link to="/privacy" className={styles.privacyLink}>
                  {labels.privacyPolicy}
                </Link>
              </p>
              {accountMessage && <p className={styles.successText}>{accountMessage}</p>}
            </div>

            <div className={`card ${styles.panel} ${styles.dangerZone}`}>
              <div className={`${styles.panelHeader} ${styles.panelHeaderWithTitle}`}>
                <h2 className={styles.dangerTitle}>{labels.deleteAccount}</h2>
                <button type="button" className="btn btnFavorite" onClick={openDeleteModal}>
                  {labels.deleteAccountButton}
                </button>
              </div>
              <p className="textSmall">{labels.deleteAccountWarning}</p>
            </div>
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

      <Modal title={labels.editProfile} open={editOpen} onClose={closeEditModal}>
        <form className={styles.form} onSubmit={handleProfileSubmit}>
          <label>
            {labels.email}
            <input
              className="input"
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
            />
          </label>
          <label>
            {labels.currentPassword}
            <input
              className="input"
              type="password"
              value={currentPassword}
              onChange={(e) => setCurrentPassword(e.target.value)}
              required
              autoComplete="current-password"
            />
          </label>
          <label>
            {labels.newPassword}
            <input
              className="input"
              type="password"
              value={newPassword}
              onChange={(e) => setNewPassword(e.target.value)}
              minLength={newPassword.length > 0 ? 8 : undefined}
              autoComplete="new-password"
            />
          </label>
          {accountError && <p className="errorText">{accountError}</p>}
          <div className={modalStyles.actions}>
            <button type="button" className="btn btnNeutral" onClick={closeEditModal} disabled={saving}>
              {labels.cancel}
            </button>
            <button type="submit" className="btn btnPrimary" disabled={saving}>
              {saving ? labels.loading : labels.save}
            </button>
          </div>
        </form>
      </Modal>

      <Modal title={labels.deleteAccount} open={deleteOpen} onClose={closeDeleteModal}>
        <p className="textSmall">{labels.deleteAccountWarning}</p>
        <form className={styles.form} onSubmit={handleDeleteAccount}>
          <label>
            {labels.currentPassword}
            <input
              className="input"
              type="password"
              value={deletePassword}
              onChange={(e) => setDeletePassword(e.target.value)}
              required
              autoComplete="current-password"
            />
          </label>
          {deleteError && <p className="errorText">{deleteError}</p>}
          <div className={modalStyles.actions}>
            <button type="button" className="btn btnNeutral" onClick={closeDeleteModal} disabled={deleting}>
              {labels.cancel}
            </button>
            <button type="submit" className="btn btnFavorite" disabled={deleting}>
              {deleting ? labels.loading : labels.deleteAccountConfirm}
            </button>
          </div>
        </form>
      </Modal>
    </>
  );
}
