import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { PageHeader } from '../components/PageHeader';
import { useTts } from '../hooks/useTts';
import { labels } from '../i18n/fr';
import { saveReadingProgress } from '../services/progressApi';
import { fetchStory } from '../services/storyApi';
import type { StoryDetail } from '../types/api';
import styles from './ReaderPage.module.css';

export function ReaderPage() {
  const { id } = useParams<{ id: string }>();
  const [story, setStory] = useState<StoryDetail | null>(null);
  const [pageIndex, setPageIndex] = useState(0);
  const [error, setError] = useState<string | null>(null);
  const { speak, stop, speaking, loading: ttsLoading, supported } = useTts();

  useEffect(() => {
    if (!id) return;
    fetchStory(Number(id))
      .then((data) => {
        setStory(data);
        const pages = [...data.pages].sort((a, b) => a.pageNumber - b.pageNumber);
        const last = Math.max(1, data.lastPageNumber ?? 1);
        const index = Math.min(last - 1, Math.max(0, pages.length - 1));
        setPageIndex(index);
      })
      .catch(() => setError('Histoire introuvable.'));
  }, [id]);

  useEffect(() => {
    if (!id || !story) return;
    const pageNumber = pageIndex + 1;
    saveReadingProgress(Number(id), pageNumber).catch(() => undefined);
  }, [id, story, pageIndex]);

  useEffect(() => () => stop(), [stop]);

  if (error) {
    return (
      <>
        <PageHeader title={labels.reading} />
        <p className="errorText">{error}</p>
      </>
    );
  }

  if (!story) {
    return (
      <>
        <PageHeader title={labels.reading} />
        <p className="textSmall">{labels.loading}</p>
      </>
    );
  }

  const pages = [...story.pages].sort((a, b) => a.pageNumber - b.pageNumber);
  const current = pages[pageIndex];
  const total = pages.length;

  function handleListen() {
    if (!current || ttsLoading) return;
    if (speaking) {
      stop();
      return;
    }
    if (!supported) {
      return;
    }
    void speak(current.content);
  }

  return (
    <>
      <PageHeader title={story.title} />
      <div className={styles.reader}>
        <div className={styles.content}>
          <div className={styles.imageCol}>
            <div className={styles.imageFrame}>
              {current && (
                <img
                  key={current.id}
                  src={current.illustration}
                  alt=""
                  className={styles.illustration}
                />
              )}
            </div>
          </div>

          <div className={styles.textCol}>
            <div className={`panel ${styles.textPanel}`}>
              <p className={styles.text}>{current?.content ?? ''}</p>
            </div>
            <div className={styles.listenWrap}>
              <button
                type="button"
                className={`btn btnSuccess ${styles.listenBtn}`}
                onClick={handleListen}
                disabled={!supported || ttsLoading}
                title={!supported ? labels.ttsUnsupported : undefined}
              >
                <img src="/icons/sound.png" alt="" className={styles.listenIcon} width={22} height={22} />
                {speaking ? labels.listenStop : labels.listen}
              </button>
            </div>
            {!supported && <p className="textSmall">{labels.ttsUnsupported}</p>}
          </div>
        </div>

        <footer className={styles.footer}>
          <div className={styles.footerSide}>
            <button
              type="button"
              className={styles.pageArrow}
              disabled={pageIndex <= 0}
              aria-label={labels.previous}
              onClick={() => {
                stop();
                setPageIndex((i) => i - 1);
              }}
            >
              <img src="/icons/arrow-left.png" alt="" width={80} height={80} />
            </button>
          </div>
          <p className={styles.progress}>{labels.pageOf(pageIndex + 1, total)}</p>
          <div className={styles.footerSide}>
            <button
              type="button"
              className={`${styles.pageArrow} ${styles.pageArrowRight}`}
              disabled={pageIndex >= total - 1}
              aria-label={labels.next}
              onClick={() => {
                stop();
                setPageIndex((i) => i + 1);
              }}
            >
              <img src="/icons/arrow-right.png" alt="" width={80} height={80} />
            </button>
          </div>
        </footer>
      </div>
    </>
  );
}
