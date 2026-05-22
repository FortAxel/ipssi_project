import { BrowserRouter, Navigate, Route, Routes } from 'react-router-dom';
import { AuthLayout } from './components/AuthLayout';
import { Layout } from './components/Layout';
import { AdminRoute, ProtectedRoute } from './components/ProtectedRoute';
import { AuthProvider } from './context/AuthContext';
import { FavoritesProvider } from './context/FavoritesContext';
import { AdminPage } from './pages/AdminPage';
import { AdminStoryPagesPage } from './pages/AdminStoryPagesPage';
import { AdminUsersPage } from './pages/AdminUsersPage';
import { CatalogPage } from './pages/CatalogPage';
import { FavoritesPage } from './pages/FavoritesPage';
import { LoginPage } from './pages/LoginPage';
import { ProfilePage } from './pages/ProfilePage';
import { ReaderPage } from './pages/ReaderPage';
import { RegisterPage } from './pages/RegisterPage';

export function App() {
  return (
    <AuthProvider>
      <FavoritesProvider>
      <BrowserRouter>
        <Routes>
          <Route element={<AuthLayout />}>
            <Route path="/login" element={<LoginPage />} />
            <Route path="/register" element={<RegisterPage />} />
          </Route>
          <Route element={<ProtectedRoute />}>
            <Route element={<Layout />}>
              <Route index element={<CatalogPage />} />
              <Route path="favorites" element={<FavoritesPage />} />
              <Route path="profile" element={<ProfilePage />} />
              <Route path="stories/:id/read" element={<ReaderPage />} />
              <Route element={<AdminRoute />}>
                <Route path="admin" element={<AdminPage />} />
                <Route path="admin/users" element={<AdminUsersPage />} />
                <Route path="admin/stories/:id" element={<AdminStoryPagesPage />} />
              </Route>
            </Route>
          </Route>
          <Route path="*" element={<Navigate to="/" replace />} />
        </Routes>
      </BrowserRouter>
      </FavoritesProvider>
    </AuthProvider>
  );
}
