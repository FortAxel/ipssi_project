import { useState, type FormEvent } from 'react';
import { Link, Navigate, useNavigate } from 'react-router-dom';
import { PageHeader } from '../components/PageHeader';
import { useAuth } from '../context/AuthContext';
import { ApiRequestError } from '../services/apiClient';
import { labels } from '../i18n/fr';
import styles from './AuthPage.module.css';

export function RegisterPage() {
  const { register, user } = useAuth();
  const navigate = useNavigate();
  const [firstName, setFirstName] = useState('');
  const [lastName, setLastName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState<string | null>(null);

  if (user) {
    return <Navigate to="/" replace />;
  }

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setError(null);
    try {
      await register(firstName, lastName, email, password);
      navigate('/');
    } catch (err) {
      setError(err instanceof ApiRequestError ? err.message : 'Inscription impossible.');
    }
  }

  return (
    <>
      <PageHeader title={labels.register} />
      <div className={styles.wrap}>
        <form onSubmit={handleSubmit} className={styles.form}>
          <label>
            {labels.firstName}
            <input
              className="input"
              value={firstName}
              onChange={(e) => setFirstName(e.target.value)}
              required
            />
          </label>
          <label>
            {labels.lastName}
            <input
              className="input"
              value={lastName}
              onChange={(e) => setLastName(e.target.value)}
              required
            />
          </label>
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
              minLength={8}
              required
            />
          </label>
          {error && <p className="errorText">{error}</p>}
          <button type="submit" className="btn btnPrimary">
            {labels.register}
          </button>
        </form>
        <p className={styles.footerLink}>
          Déjà inscrit ? <Link to="/login">{labels.login}</Link>
        </p>
      </div>
    </>
  );
}
