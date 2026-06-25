import { Link } from 'react-router-dom';
import { PageHeader } from '../components/PageHeader';
import { labels } from '../i18n/fr';
import styles from './PrivacyPage.module.css';

export function PrivacyPage() {
  return (
    <>
      <PageHeader title={labels.privacyTitle} />
      <article className={`card ${styles.article}`}>
        <p className="textSmall">Dernière mise à jour : juin 2026</p>

        <section>
          <h2>Responsable du traitement</h2>
          <p>
            L’application <strong>Storybook Kids</strong> est un projet pédagogique. Les données
            sont hébergées dans un environnement de démonstration contrôlé par le titulaire du
            compte parent.
          </p>
        </section>

        <section>
          <h2>Données collectées</h2>
          <p>Nous collectons uniquement les données nécessaires au compte parent :</p>
          <ul>
            <li>Identité (prénom, nom)</li>
            <li>Adresse e-mail</li>
            <li>Mot de passe (stocké de manière hachée)</li>
            <li>Favoris et progression de lecture associés au compte</li>
          </ul>
          <p>
            Aucune donnée nominative n’est collectée directement auprès des enfants : l’usage de
            l’application se fait sous la responsabilité du parent.
          </p>
        </section>

        <section>
          <h2>Finalités</h2>
          <p>
            Ces données permettent l’authentification, la personnalisation de l’expérience (favoris,
            reprise de lecture) et la gestion du catalogue consulté par le foyer.
          </p>
        </section>

        <section>
          <h2>Vos droits</h2>
          <p>Conformément au RGPD, vous pouvez :</p>
          <ul>
            <li>Consulter vos données depuis votre profil</li>
            <li>Modifier votre e-mail ou votre mot de passe</li>
            <li>Supprimer votre compte et l’ensemble des données associées</li>
          </ul>
        </section>

        <section>
          <h2>Sécurité</h2>
          <p>
            Les mots de passe sont hachés. Les communications avec l’API doivent s’effectuer en
            HTTPS en production. Les secrets (clés JWT) ne sont pas exposés côté client.
          </p>
        </section>

        <p className={styles.back}>
          <Link to="/profile">{labels.privacyBack}</Link>
        </p>
      </article>
    </>
  );
}
