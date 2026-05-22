import { useState, type FormEvent } from 'react';
import { Link, Navigate, useNavigate } from 'react-router-dom';
import { PageHeader } from '../components/PageHeader';
import { useAuth } from '../context/AuthContext';
import { ApiRequestError } from '../services/apiClient';
import { labels } from '../i18n/fr';
import styles from './AuthPage.module.css';

export function LoginPage() {
  const { login, user } = useAuth();
  const navigate = useNavigate();
  const [email, setEmail] = useState('parent@demo.local');
  const [password, setPassword] = useState('parent123');
  const [error, setError] = useState<string | null>(null);

  if (user) {
    return <Navigate to="/" replace />;
  }

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setError(null);
    try {
      await login(email, password);
      navigate('/');
    } catch (err) {
      setError(err instanceof ApiRequestError ? err.message : 'Connexion impossible.');
    }
  }

  return (
    <>
      <PageHeader title={labels.login} />
      <div className={styles.wrap}>
        <form onSubmit={handleSubmit} className={styles.form}>
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
            {labels.password}
            <input
              className="input"
              type="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
            />
          </label>
          {error && <p className="errorText">{error}</p>}
          <button type="submit" className="btn btnPrimary">
            {labels.login}
          </button>
        </form>
        <p className={styles.footerLink}>
          Pas de compte ? <Link to="/register">{labels.register}</Link>
        </p>
      </div>
    </>
  );
}
