import { useState, type ChangeEvent } from 'react';
import { ApiRequestError, apiUploadImage } from '../services/apiClient';
import { labels } from '../i18n/fr';
import styles from './ImageUploadField.module.css';

interface ImageUploadFieldProps {
  label: string;
  value: string;
  onChange: (url: string) => void;
  required?: boolean;
}

export function ImageUploadField({ label, value, onChange, required }: ImageUploadFieldProps) {
  const [uploading, setUploading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function handleFile(e: ChangeEvent<HTMLInputElement>) {
    const file = e.target.files?.[0];
    if (!file) return;
    setUploading(true);
    setError(null);
    try {
      const result = await apiUploadImage(file);
      onChange(result.url);
    } catch (err) {
      const msg =
        err instanceof ApiRequestError && err.body?.detail
          ? err.body.detail
          : err instanceof ApiRequestError && err.body?.message
            ? err.body.message
            : 'Échec de l’envoi de l’image.';
      setError(msg);
    } finally {
      setUploading(false);
      e.target.value = '';
    }
  }

  return (
    <div className={styles.wrap}>
      <span className={styles.label}>{label}</span>
      {value && (
        <img src={value} alt="" className={styles.preview} />
      )}
      <label className={`btn btnPrimary ${styles.uploadBtn}`}>
        {uploading ? labels.uploading : labels.uploadImage}
        <input type="file" accept="image/jpeg,image/png,image/webp,image/gif" onChange={handleFile} hidden />
      </label>
      <input
        className="input"
        value={value}
        onChange={(e) => onChange(e.target.value)}
        placeholder="/images/…"
        required={required && !value}
      />
      {error && <p className="errorText">{error}</p>}
    </div>
  );
}
