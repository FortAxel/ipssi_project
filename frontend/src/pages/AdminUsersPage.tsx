import { useEffect, useState, type FormEvent } from 'react';
import { Link } from 'react-router-dom';
import { PageHeader } from '../components/PageHeader';
import {
  createAdminUser,
  deleteAdminUser,
  fetchAdminUsers,
  updateAdminUser,
} from '../services/adminApi';
import type { AdminUser, AdminUserInput } from '../types/api';
import { labels } from '../i18n/fr';
import styles from './AdminPage.module.css';

const emptyUser = (): AdminUserInput => ({
  firstName: '',
  lastName: '',
  email: '',
  password: '',
  isAdmin: false,
});

export function AdminUsersPage() {
  const [users, setUsers] = useState<AdminUser[]>([]);
  const [form, setForm] = useState<AdminUserInput>(emptyUser());
  const [editingId, setEditingId] = useState<number | null>(null);
  const [message, setMessage] = useState<string | null>(null);

  async function load() {
    setUsers(await fetchAdminUsers());
  }

  useEffect(() => {
    load().catch(() => setMessage('Erreur de chargement.'));
  }, []);

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setMessage(null);
    try {
      if (editingId) {
        const payload: Partial<AdminUserInput> = {
          firstName: form.firstName,
          lastName: form.lastName,
          email: form.email,
          isAdmin: form.isAdmin,
        };
        if (form.password) payload.password = form.password;
        await updateAdminUser(editingId, payload);
        setMessage('Utilisateur mis à jour.');
      } else {
        await createAdminUser(form);
        setMessage('Utilisateur créé.');
      }
      setForm(emptyUser());
      setEditingId(null);
      await load();
    } catch {
      setMessage('Enregistrement impossible.');
    }
  }

  function startEdit(user: AdminUser) {
    setEditingId(user.id);
    setForm({
      firstName: user.firstName,
      lastName: user.lastName,
      email: user.email,
      password: '',
      isAdmin: user.isAdmin,
    });
  }

  async function handleDelete(user: AdminUser) {
    if (!confirm(`Supprimer ${user.email} ?`)) return;
    try {
      await deleteAdminUser(user.id);
      await load();
    } catch {
      setMessage('Suppression impossible (compte connecté ?).');
    }
  }

  return (
    <>
      <PageHeader title={labels.users} />
      <div className={styles.wrap}>
        <div className={styles.adminNav}>
          <Link to="/admin">{labels.stories}</Link>
          <span>{labels.users}</span>
        </div>
        {message && <p className={styles.message}>{message}</p>}

        <form onSubmit={handleSubmit} className={`card ${styles.form}`}>
          <h2>{editingId ? labels.editUser : labels.newUser}</h2>
          <label>
            {labels.firstName}
            <input
              className="input"
              value={form.firstName}
              onChange={(e) => setForm({ ...form, firstName: e.target.value })}
              required
            />
          </label>
          <label>
            {labels.lastName}
            <input
              className="input"
              value={form.lastName}
              onChange={(e) => setForm({ ...form, lastName: e.target.value })}
              required
            />
          </label>
          <label>
            {labels.email}
            <input
              className="input"
              type="email"
              value={form.email}
              onChange={(e) => setForm({ ...form, email: e.target.value })}
              required
            />
          </label>
          <label>
            {labels.password}
            <input
              className="input"
              type="password"
              value={form.password}
              onChange={(e) => setForm({ ...form, password: e.target.value })}
              minLength={editingId ? 0 : 8}
              required={!editingId}
              placeholder={editingId ? 'Laisser vide pour ne pas changer' : ''}
            />
          </label>
          <label className={styles.checkbox}>
            <input
              type="checkbox"
              checked={form.isAdmin}
              onChange={(e) => setForm({ ...form, isAdmin: e.target.checked })}
            />
            {labels.roleAdmin}
          </label>
          <div className={styles.formActions}>
            <button type="submit" className="btn btnPrimary">
              {labels.save}
            </button>
            {editingId && (
              <button
                type="button"
                className="btn btnNeutral"
                onClick={() => {
                  setEditingId(null);
                  setForm(emptyUser());
                }}
              >
                Annuler
              </button>
            )}
          </div>
        </form>

        <ul className={styles.list}>
          {users.map((u) => (
            <li key={u.id} className={`card ${styles.listItem}`}>
              <p className={styles.itemTitle}>
                {u.firstName} {u.lastName}{' '}
                <span className="textSmall">{u.isAdmin ? '(Admin)' : '(Parent)'}</span>
              </p>
              <p className="textSmall">{u.email}</p>
              <div className={styles.actions}>
                <button type="button" className="btn btnPrimary" onClick={() => startEdit(u)}>
                  Modifier
                </button>
                <button type="button" className="btn btnNeutral" onClick={() => handleDelete(u)}>
                  {labels.delete}
                </button>
              </div>
            </li>
          ))}
        </ul>
      </div>
    </>
  );
}
