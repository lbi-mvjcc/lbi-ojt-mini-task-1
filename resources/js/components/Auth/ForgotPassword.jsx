import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import axios from 'axios';

export default function ForgotPassword() {
    const [email, setEmail] = useState('');
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');
    const [loading, setLoading] = useState(false);
    const [resetToken, setResetToken] = useState('');
    const navigate = useNavigate();

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError('');
        setSuccess('');
        setLoading(true);

        try {
            const response = await axios.post('/api/password/email', { email });
            setSuccess(response.data.message);
            
            if (response.data.token) {
                setResetToken(response.data.token);
                setTimeout(() => {
                    navigate(`/password/reset?token=${response.data.token}&email=${email}`);
                }, 2000);
            }
        } catch (err) {
            setError(err.response?.data?.message || 'Failed to send reset link. Please try again.');
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
            padding: '2rem',
            position: 'relative'
        }}>
            {/* Back Button - Outside Container */}
            <Link 
                to="/login"
                style={{
                    position: 'absolute',
                    top: '2rem',
                    left: '2rem',
                    display: 'flex',
                    alignItems: 'center',
                    gap: '0.5rem',
                    color: 'white',
                    textDecoration: 'none',
                    fontSize: '0.95rem',
                    fontWeight: '600',
                    transition: 'all 0.3s ease',
                    padding: '0.5rem 1rem',
                    borderRadius: '8px',
                    background: 'rgba(255, 255, 255, 0.1)',
                    backdropFilter: 'blur(10px)'
                }}
                onMouseEnter={(e) => {
                    e.currentTarget.style.background = 'rgba(255, 255, 255, 0.2)';
                    e.currentTarget.style.transform = 'translateX(-4px)';
                }}
                onMouseLeave={(e) => {
                    e.currentTarget.style.background = 'rgba(255, 255, 255, 0.1)';
                    e.currentTarget.style.transform = 'translateX(0)';
                }}
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Back
            </Link>

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
                        Forgot Password?
                    </h1>
                    <p style={{ color: '#64748b', fontSize: '0.95rem', lineHeight: '1.6' }}>
                        No worries! Enter your email address and we'll send you a reset link to reset your password.
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

                {success && (
                    <div style={{
                        padding: '1rem',
                        background: '#d1fae5',
                        border: '1px solid #a7f3d0',
                        borderRadius: '12px',
                        color: '#065f46',
                        marginBottom: '1.5rem',
                        fontSize: '0.875rem'
                    }}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem', marginBottom: '0.5rem' }}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <strong>Success!</strong>
                        </div>
                        {success}
                        {resetToken && (
                            <div style={{ marginTop: '0.75rem', fontSize: '0.8rem', opacity: '0.9' }}>
                                Redirecting to reset page...
                            </div>
                        )}
                    </div>
                )}

                <form onSubmit={handleSubmit}>
                    <div style={{ marginBottom: '2rem' }}>
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
                            boxShadow: '0 4px 15px rgba(6, 182, 212, 0.4)',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            gap: '0.5rem'
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
                        {loading ? (
                            <>
                                <svg style={{ animation: 'spin 1s linear infinite' }} xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <path d="M21 12a9 9 0 1 1-6.219-8.56"></path>
                                </svg>
                                Sending...
                            </>
                        ) : (
                            <>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                                Send Reset Link
                            </>
                        )}
                    </button>
                </form>

                <div style={{ 
                    marginTop: '2rem',
                    paddingTop: '2rem',
                    borderTop: '1px solid #e2e8f0',
                    textAlign: 'center'
                }}>
                    <p style={{ color: '#64748b', fontSize: '0.875rem' }}>
                        Remember your password?{' '}
                        <Link 
                            to="/login"
                            style={{
                                color: '#06b6d4',
                                textDecoration: 'none',
                                fontWeight: '600'
                            }}
                        >
                            Back to Login
                        </Link>
                    </p>
                </div>
            </div>
            
            <style>{`
                @keyframes spin {
                    from { transform: rotate(0deg); }
                    to { transform: rotate(360deg); }
                }
            `}</style>
        </div>
    );
}
