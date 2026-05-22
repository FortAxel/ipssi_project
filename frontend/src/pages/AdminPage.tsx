import { useEffect, useState, type FormEvent } from 'react';
import { Link } from 'react-router-dom';
import { ImageUploadField } from '../components/ImageUploadField';
import { PageHeader } from '../components/PageHeader';
import { labels } from '../i18n/fr';
import {
  createStory,
  deleteStory,
  fetchAdminStories,
  updateStory,
} from '../services/storyApi';
import type { AdminStory, StoryInput } from '../types/api';
import styles from './AdminPage.module.css';

const emptyStory: StoryInput = {
  title: '',
  description: '',
  coverImage: '',
  status: 'DRAFT',
  category: 'OTHER',
  ageRange: '3-6',
};

export function AdminPage() {
  const [stories, setStories] = useState<AdminStory[]>([]);
  const [form, setForm] = useState<StoryInput>(emptyStory);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [message, setMessage] = useState<string | null>(null);

  async function load() {
    setStories(await fetchAdminStories());
  }

  useEffect(() => {
    load().catch(() => setMessage('Erreur de chargement admin.'));
  }, []);

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setMessage(null);
    try {
      if (editingId) {
        await updateStory(editingId, form);
        setMessage('Histoire mise à jour.');
      } else {
        await createStory(form);
        setMessage('Histoire créée.');
      }
      setForm(emptyStory);
      setEditingId(null);
      await load();
    } catch {
      setMessage('Enregistrement impossible.');
    }
  }

  function startEdit(story: AdminStory) {
    setEditingId(story.id);
    setForm({
      title: story.title,
      description: story.description,
      coverImage: story.coverImage,
      status: story.status,
      category: story.category,
      ageRange: story.ageRange,
    });
  }

  async function handleDelete(id: number) {
    if (!confirm('Supprimer cette histoire ?')) return;
    await deleteStory(id);
    await load();
  }

  return (
    <>
      <PageHeader title={labels.admin} />
      <div className={styles.wrap}>
        <div className={styles.adminNav}>
          <span>{labels.stories}</span>
          <Link to="/admin/users">{labels.users}</Link>
        </div>
        {message && <p className={styles.message}>{message}</p>}

        <form onSubmit={handleSubmit} className={`card ${styles.form}`}>
          <h2>{editingId ? 'Modifier' : 'Nouvelle histoire'}</h2>
          <label>
            {labels.title}
            <input
              className="input"
              value={form.title}
              onChange={(e) => setForm({ ...form, title: e.target.value })}
              required
            />
          </label>
          <label>
            {labels.description}
            <textarea
              className="input"
              value={form.description}
              onChange={(e) => setForm({ ...form, description: e.target.value })}
              required
            />
          </label>
          <ImageUploadField
            label={labels.coverImage}
            value={form.coverImage}
            onChange={(url) => setForm({ ...form, coverImage: url })}
            required
          />
          <label>
            {labels.status}
            <select
              className="input"
              value={form.status}
              onChange={(e) => setForm({ ...form, status: e.target.value })}
            >
              <option value="DRAFT">{labels.draft}</option>
              <option value="PUBLISHED">{labels.publish}</option>
              <option value="ARCHIVED">{labels.archived}</option>
            </select>
          </label>
          <label>
            {labels.category}
            <select
              className="input"
              value={form.category}
              onChange={(e) => setForm({ ...form, category: e.target.value })}
            >
              <option value="ADVENTURE">Aventure</option>
              <option value="ANIMALS">Animaux</option>
              <option value="FAMILY">Famille</option>
              <option value="FANTASY">Fantaisie</option>
              <option value="OTHER">Autre</option>
            </select>
          </label>
          <label>
            {labels.ageRange}
            <input
              className="input"
              value={form.ageRange}
              onChange={(e) => setForm({ ...form, ageRange: e.target.value })}
              required
            />
          </label>
          <div className={styles.formActions}>
            <button type="submit" className="btn btnPrimary">
              {labels.save}
            </button>
            {editingId && (
              <button
                type="button"
                className="btn btnPrimary"
                onClick={() => {
                  setEditingId(null);
                  setForm(emptyStory);
                }}
              >
                Annuler
              </button>
            )}
          </div>
        </form>

        <ul className={styles.list}>
          {stories.map((s) => (
            <li key={s.id} className={`card ${styles.listItem}`}>
              <p className={styles.itemTitle}>
                {s.title} <span className="textSmall">({s.status})</span>
              </p>
              <p className="textSmall">{s.pages.length} pages</p>
              <div className={styles.actions}>
                <button type="button" className="btn btnPrimary" onClick={() => startEdit(s)}>
                  Modifier
                </button>
                <button type="button" className="btn btnPrimary" onClick={() => handleDelete(s.id)}>
                  {labels.delete}
                </button>
                <Link to={`/admin/stories/${s.id}`}>Gérer les pages</Link>
              </div>
            </li>
          ))}
        </ul>
      </div>
    </>
  );
}
