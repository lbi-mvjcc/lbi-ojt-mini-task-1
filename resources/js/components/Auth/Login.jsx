import React, { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';

export default function Login() {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [rememberMe, setRememberMe] = useState(false);
    const [showPassword, setShowPassword] = useState(false);
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);
    const { login } = useAuth();
    const navigate = useNavigate();

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError('');
        setLoading(true);

        try {
            const user = await login(email, password);
            
            // Redirect based on user role
            if (user.role === 'admin') {
                navigate('/admin/dashboard');
            } else if (user.role === 'customer') {
                navigate('/customer/dashboard');
            } else if (['frontend_developer', 'backend_developer', 'server_admin'].includes(user.role)) {
                navigate('/developer/dashboard');
            } else {
                navigate('/');
            }
        } catch (err) {
            setError(err.response?.data?.message || 'Login failed. Please check your credentials.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div style={{ 
            minHeight: '100vh',
            background: 'linear-gradient(135deg, #06b6d4 0%, #0891b2 100%)',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            padding: '2rem'
        }}>
            <div style={{ 
                background: 'white',
                borderRadius: '20px',
                padding: '3rem',
                boxShadow: '0 20px 60px rgba(0,0,0,0.3)',
                maxWidth: '480px',
                width: '100%'
            }}>
                {/* Header */}
                <div style={{ textAlign: 'center', marginBottom: '2.5rem' }}>
                    <Link to="/" style={{ textDecoration: 'none' }}>
                        
                    </Link>
                    <h1 style={{ 
                        color: '#1e293b', 
                        marginBottom: '0.5rem', 
                        fontSize: '2rem',
                        fontWeight: '700'
                    }}>
                        Login
                    </h1>
                    <p style={{ color: '#64748b', fontSize: '1rem' }}>
                        Sign in to access your dashboard
                    </p>
                </div>

                {error && (
                    <div style={{
                        padding: '1rem',
                        background: '#fee2e2',
                        border: '1px solid #fecaca',
                        borderRadius: '12px',
                        color: '#dc2626',
                        marginBottom: '1.5rem',
                        fontSize: '0.875rem'
                    }}>
                        {error}
                    </div>
                )}

                <form onSubmit={handleSubmit}>
                    <div style={{ marginBottom: '1.5rem' }}>
                        <label style={{ 
                            display: 'block',
                            marginBottom: '0.5rem',
                            color: '#1e293b',
                            fontWeight: '600',
                            fontSize: '0.875rem'
                        }}>
                            Email Address
                        </label>
                        <input
                            type="email"
                            value={email}
                            onChange={(e) => setEmail(e.target.value)}
                            placeholder="your.email@example.com"
                            required
                            disabled={loading}
                            style={{
                                width: '100%',
                                padding: '0.875rem 1rem',
                                border: '2px solid #e2e8f0',
                                borderRadius: '12px',
                                fontSize: '1rem',
                                transition: 'all 0.3s ease',
                                outline: 'none'
                            }}
                            onFocus={(e) => e.target.style.borderColor = '#06b6d4'}
                            onBlur={(e) => e.target.style.borderColor = '#e2e8f0'}
                        />
                    </div>

                    <div style={{ marginBottom: '1.5rem' }}>
                        <label style={{ 
                            display: 'block',
                            marginBottom: '0.5rem',
                            color: '#1e293b',
                            fontWeight: '600',
                            fontSize: '0.875rem'
                        }}>
                            Password
                        </label>
                        <div style={{ position: 'relative' }}>
                            <input
                                type={showPassword ? 'text' : 'password'}
                                value={password}
                                onChange={(e) => setPassword(e.target.value)}
                                placeholder="Enter your password"
                                minLength={8}
                                required
                                disabled={loading}
                                style={{
                                    width: '100%',
                                    padding: '0.875rem 3rem 0.875rem 1rem',
                                    border: '2px solid #e2e8f0',
                                    borderRadius: '12px',
                                    fontSize: '1rem',
                                    transition: 'all 0.3s ease',
                                    outline: 'none'
                                }}
                                onFocus={(e) => e.target.style.borderColor = '#06b6d4'}
                                onBlur={(e) => e.target.style.borderColor = '#e2e8f0'}
                            />
                            <button
                                type="button"
                                onClick={() => setShowPassword(!showPassword)}
                                disabled={loading}
                                style={{
                                    position: 'absolute',
                                    right: '1rem',
                                    top: '50%',
                                    transform: 'translateY(-50%)',
                                    background: 'none',
                                    border: 'none',
                                    cursor: 'pointer',
                                    color: '#64748b',
                                    padding: '0.25rem'
                                }}
                            >
                                {showPassword ? (
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                    </svg>
                                ) : (
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                )}
                            </button>
                        </div>
                    </div>

                    <div style={{ 
                        display: 'flex', 
                        justifyContent: 'space-between', 
                        alignItems: 'center',
                        marginBottom: '2rem'
                    }}>
                        <label style={{ 
                            display: 'flex',
                            alignItems: 'center',
                            gap: '0.5rem',
                            cursor: 'pointer',
                            fontSize: '0.875rem',
                            color: '#64748b'
                        }}>
                            <input
                                type="checkbox"
                                checked={rememberMe}
                                onChange={(e) => setRememberMe(e.target.checked)}
                                disabled={loading}
                                style={{ cursor: 'pointer' }}
                            />
                            <span>Remember me</span>
                        </label>
                        <Link 
                            to="/password/forgot"
                            style={{
                                color: '#06b6d4',
                                textDecoration: 'none',
                                fontSize: '0.875rem',
                                fontWeight: '600'
                            }}
                        >
                            Forgot Password?
                        </Link>
                    </div>

                    <button 
                        type="submit" 
                        disabled={loading}
                        style={{
                            width: '100%',
                            padding: '1rem',
                            fontSize: '1.1rem',
                            fontWeight: '600',
                            color: 'white',
                            background: loading ? '#94a3b8' : 'linear-gradient(135deg, #06b6d4 0%, #0891b2 100%)',
                            border: 'none',
                            borderRadius: '12px',
                            cursor: loading ? 'not-allowed' : 'pointer',
                            transition: 'all 0.3s ease',
                            boxShadow: '0 4px 15px rgba(6, 182, 212, 0.4)'
                        }}
                        onMouseEnter={(e) => {
                            if (!loading) {
                                e.currentTarget.style.transform = 'translateY(-2px)';
                                e.currentTarget.style.boxShadow = '0 6px 20px rgba(6, 182, 212, 0.5)';
                            }
                        }}
                        onMouseLeave={(e) => {
                            e.currentTarget.style.transform = 'translateY(0)';
                            e.currentTarget.style.boxShadow = '0 4px 15px rgba(6, 182, 212, 0.4)';
                        }}
                    >
                        {loading ? 'Signing in...' : 'Sign In'}
                    </button>
                </form>

                <div style={{ 
                    marginTop: '2rem',
                    paddingTop: '2rem',
                    borderTop: '1px solid #e2e8f0',
                    textAlign: 'center'
                }}>
                    <p style={{ color: '#64748b', fontSize: '0.875rem' }}>
                        Don't have an account?{' '}
                        <Link 
                            to="/register"
                            style={{
                                color: '#06b6d4',
                                textDecoration: 'none',
                                fontWeight: '600'
                            }}
                        >
                            Create Account
                        </Link>
                    </p>
                </div>
            </div>
        </div>
    );
}
