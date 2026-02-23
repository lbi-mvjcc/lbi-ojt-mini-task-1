import { Navigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

export default function LandingPage() {
    const { user, loading } = useAuth();

    if (loading) {
        return <div className="loading">Loading...</div>;
    }

    // If user is logged in, redirect to their dashboard
    if (user) {
        if (user.role === 'admin') {
            return <Navigate to="/admin/dashboard" replace />;
        } else if (user.role === 'customer') {
            return <Navigate to="/customer/dashboard" replace />;
        } else {
            return <Navigate to="/developer/dashboard" replace />;
        }
    }

    // If not logged in, redirect to welcome page
    return <Navigate to="/welcome" replace />;
}
