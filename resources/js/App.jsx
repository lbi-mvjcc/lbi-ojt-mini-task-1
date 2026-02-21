import React from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import LandingPage from './components/LandingPage';
import Login from './components/Auth/Login';
import Register from './components/Auth/Register';
import ForgotPassword from './components/Auth/ForgotPassword';
import ResetPassword from './components/Auth/ResetPassword';
import CustomerDashboard from './components/Customer/Dashboard';
import DeveloperDashboard from './components/Developer/Dashboard';
import AdminDashboard from './components/Admin/Dashboard';
import EditProfile from './components/EditProfile';
import CreateTask from './components/Customer/CreateTask';
import EditTask from './components/Customer/EditTask';
import RecentlyDeleted from './components/Customer/RecentlyDeleted';
import TaskDetail from './components/TaskDetail';
import { AuthProvider, useAuth } from './context/AuthContext';
import { ThemeProvider } from './context/ThemeContext';
import './styles/app.css';

function PrivateRoute({ children, allowedRoles }) {
    const { user, loading } = useAuth();

    if (loading) {
        return <div className="loading">Loading...</div>;
    }

    if (!user) {
        return <Navigate to="/" />;
    }

    if (allowedRoles && !allowedRoles.includes(user.role)) {
        // Redirect to appropriate login page based on attempted access
        if (allowedRoles.includes('admin')) {
            return <Navigate to="/admin/login" />;
        } else if (allowedRoles.includes('customer')) {
            return <Navigate to="/customer/login" />;
        } else {
            return <Navigate to="/developer/login" />;
        }
    }

    return children;
}

function AppRoutes() {
    const { user } = useAuth();

    return (
        <Routes>
            {/* Landing Page */}
            <Route path="/" element={<LandingPage />} />
            
            {/* Single Login Page */}
            <Route path="/login" element={<Login />} />
            <Route path="/register" element={<Register />} />
            
            {/* Password Reset */}
            <Route path="/password/forgot" element={<ForgotPassword />} />
            <Route path="/password/reset" element={<ResetPassword />} />
            
            {/* Legacy login routes - redirect to main login */}
            <Route path="/customer/login" element={<Navigate to="/login" />} />
            <Route path="/developer/login" element={<Navigate to="/login" />} />
            <Route path="/admin/login" element={<Navigate to="/login" />} />
            
            {/* Customer Routes */}
            <Route
                path="/customer/dashboard"
                element={
                    <PrivateRoute allowedRoles={['customer']}>
                        <CustomerDashboard />
                    </PrivateRoute>
                }
            />

            <Route
                path="/customer/tasks/create"
                element={
                    <PrivateRoute allowedRoles={['customer']}>
                        <CreateTask />
                    </PrivateRoute>
                }
            />

            <Route
                path="/customer/tasks/:id/edit"
                element={
                    <PrivateRoute allowedRoles={['customer']}>
                        <EditTask />
                    </PrivateRoute>
                }
            />

            <Route
                path="/customer/recently-deleted"
                element={
                    <PrivateRoute allowedRoles={['customer']}>
                        <RecentlyDeleted />
                    </PrivateRoute>
                }
            />

            {/* Developer Routes */}
            <Route
                path="/developer/dashboard"
                element={
                    <PrivateRoute allowedRoles={['frontend_developer', 'backend_developer', 'server_admin']}>
                        <DeveloperDashboard />
                    </PrivateRoute>
                }
            />

            {/* Admin Routes */}
            <Route
                path="/admin/dashboard"
                element={
                    <PrivateRoute allowedRoles={['admin']}>
                        <AdminDashboard />
                    </PrivateRoute>
                }
            />

            {/* Profile Routes (All authenticated users) */}
            <Route
                path="/profile"
                element={
                    <PrivateRoute>
                        <EditProfile />
                    </PrivateRoute>
                }
            />

            {/* Task Detail (All authenticated users) */}
            <Route
                path="/tasks/:id"
                element={
                    <PrivateRoute>
                        <TaskDetail />
                    </PrivateRoute>
                }
            />
        </Routes>
    );
}

export default function App() {
    return (
        <ThemeProvider>
            <AuthProvider>
                <AppRoutes />
            </AuthProvider>
        </ThemeProvider>
    );
}
