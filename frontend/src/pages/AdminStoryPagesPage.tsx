import { useCallback, useEffect, useState, type FormEvent } from 'react';
import { Link, useParams } from 'react-router-dom';
import { ImageUploadField } from '../components/ImageUploadField';
import { PageHeader } from '../components/PageHeader';
import { createPage, deletePage, updatePage } from '../services/adminApi';
import { fetchAdminStory } from '../services/storyApi';
import type { AdminStory, PageInput, StoryPage } from '../types/api';
import { labels } from '../i18n/fr';
import styles from './AdminPage.module.css';

const emptyPage = (): PageInput => ({
  pageNumber: 1,
  content: '',
  illustration: '',
});

export function AdminStoryPagesPage() {
  const { id } = useParams<{ id: string }>();
  const [story, setStory] = useState<AdminStory | null>(null);
  const [pageForm, setPageForm] = useState<PageInput>(emptyPage());
  const [editingPageId, setEditingPageId] = useState<number | null>(null);
  const [message, setMessage] = useState<string | null>(null);

  const reload = useCallback(async () => {
    if (!id) return;
    const data = await fetchAdminStory(Number(id));
    setStory(data);
    setPageForm((prev) => {
      if (editingPageId) return prev;
      return { ...prev, pageNumber: data.pages.length + 1 };
    });
  }, [id, editingPageId]);

  useEffect(() => {
    reload().catch(() => undefined);
  }, [reload]);

  function startEdit(page: StoryPage) {
    setEditingPageId(page.id);
    setPageForm({
      pageNumber: page.pageNumber,
      content: page.content,
      illustration: page.illustration,
    });
    setMessage(null);
  }

  function cancelEdit() {
    setEditingPageId(null);
    setPageForm(emptyPage());
    if (story) {
      setPageForm({
        ...emptyPage(),
        pageNumber: story.pages.length + 1,
      });
    }
  }

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    if (!id) return;
    setMessage(null);
    try {
      if (editingPageId) {
        await updatePage(Number(id), editingPageId, pageForm);
        setMessage('Page mise à jour.');
      } else {
        await createPage(Number(id), pageForm);
        setMessage('Page ajoutée.');
      }
      cancelEdit();
      await reload();
    } catch {
      setMessage('Enregistrement impossible.');
    }
  }

  async function handleDelete(page: StoryPage) {
    if (!id || !confirm(`Supprimer la page ${page.pageNumber} ?`)) return;
    await deletePage(Number(id), page.id);
    if (editingPageId === page.id) cancelEdit();
    await reload();
  }

  if (!story) {
    return (
      <>
        <PageHeader title={labels.pages} />
        <p className="textSmall">{labels.loading}</p>
      </>
    );
  }

  const sortedPages = [...story.pages].sort((a, b) => a.pageNumber - b.pageNumber);

  return (
    <>
      <PageHeader title={`${labels.pages} — ${story.title}`} />
      <div className={styles.wrap}>
        <Link to="/admin" className="textSmall">
          ← {labels.admin}
        </Link>
        {message && <p className={styles.message}>{message}</p>}

        <ul className={styles.list}>
          {sortedPages.map((p) => (
            <li key={p.id} className={`card ${styles.listItem} ${styles.pageRow}`}>
              {p.illustration && (
                <img src={p.illustration} alt="" className={styles.pageThumb} />
              )}
              <div className={styles.pageInfo}>
                <p className={styles.itemTitle}>Page {p.pageNumber}</p>
                <p className="textSmall">{p.content.slice(0, 120)}{p.content.length > 120 ? '…' : ''}</p>
              </div>
              <div className={styles.actions}>
                <button type="button" className="btn btnPrimary" onClick={() => startEdit(p)}>
                  Modifier
                </button>
                <button type="button" className="btn btnNeutral" onClick={() => handleDelete(p)}>
                  {labels.delete}
                </button>
              </div>
            </li>
          ))}
        </ul>

        <form onSubmit={handleSubmit} className={`card ${styles.form}`}>
          <h2>{editingPageId ? labels.editPage : labels.addPage}</h2>
          <label>
            N°
            <input
              className="input"
              type="number"
              min={1}
              value={pageForm.pageNumber}
              onChange={(e) => setPageForm({ ...pageForm, pageNumber: Number(e.target.value) })}
            />
          </label>
          <label>
            Texte
            <textarea
              className="input"
              value={pageForm.content}
              onChange={(e) => setPageForm({ ...pageForm, content: e.target.value })}
              required
            />
          </label>
          <ImageUploadField
            label={labels.illustration}
            value={pageForm.illustration}
            onChange={(url) => setPageForm({ ...pageForm, illustration: url })}
            required
          />
          <div className={styles.formActions}>
            <button type="submit" className="btn btnPrimary">
              {editingPageId ? labels.save : labels.addPage}
            </button>
            {editingPageId && (
              <button type="button" className="btn btnNeutral" onClick={cancelEdit}>
                Annuler
              </button>
            )}
          </div>
        </form>
      </div>
    </>
  );
}
