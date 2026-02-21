import { Link } from 'react-router-dom';

export default function LandingPage() {
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
                maxWidth: '1200px', 
                width: '100%',
                display: 'grid',
                gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))',
                gap: '3rem',
                alignItems: 'center'
            }}>
                {/* Left Side - Hero Content */}
                <div style={{ color: 'white', padding: '2rem' }}>
                    
                    
                    <h1 style={{ 
                        fontSize: 'clamp(2.5rem, 6vw, 4rem)', 
                        fontWeight: '800',
                        lineHeight: '1.1',
                        marginBottom: '1.5rem',
                        textShadow: '0 2px 10px rgba(0,0,0,0.1)'
                    }}>
                        Manage Tasks<br />Like a Pro
                    </h1>
                    
                    <p style={{ 
                        fontSize: 'clamp(1.1rem, 2vw, 1.25rem)', 
                        lineHeight: '1.6',
                        marginBottom: '2rem',
                        opacity: '0.95'
                    }}>
                        Streamline your workflow, collaborate with your team, and deliver projects on time with Task Sync.
                    </p>

                    <div style={{ 
                        display: 'flex', 
                        gap: '1rem',
                        flexWrap: 'wrap',
                        marginBottom: '3rem'
                    }}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span>Real-time Updates</span>
                        </div>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span>Team Collaboration</span>
                        </div>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span>Progress Tracking</span>
                        </div>
                    </div>

                    {/* Stats */}
                    <div style={{ 
                        display: 'grid',
                        gridTemplateColumns: 'repeat(3, 1fr)',
                        gap: '2rem',
                        paddingTop: '2rem',
                        borderTop: '1px solid rgba(255,255,255,0.2)'
                    }}>
                       
                    </div>
                </div>

                {/* Right Side - Login Card */}
                <div style={{ 
                    background: 'white',
                    borderRadius: '20px',
                    padding: '3rem',
                    boxShadow: '0 20px 60px rgba(0,0,0,0.3)',
                    maxWidth: '450px',
                    margin: '0 auto',
                    width: '100%'
                }}>
                    <div style={{ textAlign: 'center', marginBottom: '2rem' }}>
                        
                        <h2 style={{ 
                            color: '#1e293b', 
                            marginBottom: '0.5rem', 
                            fontSize: '1.875rem',
                            fontWeight: '700'
                        }}>
                            Welcome 
                        </h2>
                        <p style={{ color: '#64748b', fontSize: '1rem' }}>
                            Sign in to continue to your dashboard
                        </p>
                    </div>

                    <div style={{ display: 'flex', flexDirection: 'column', gap: '1rem' }}>
                        <Link to="/login" style={{ textDecoration: 'none' }}>
                            <button style={{
                                width: '100%',
                                padding: '1rem',
                                fontSize: '1.1rem',
                                fontWeight: '600',
                                color: 'white',
                                background: 'linear-gradient(135deg, #06b6d4 0%, #0891b2 100%)',
                                border: 'none',
                                borderRadius: '12px',
                                cursor: 'pointer',
                                transition: 'all 0.3s ease',
                                boxShadow: '0 4px 15px rgba(6, 182, 212, 0.4)'
                            }}
                            onMouseEnter={(e) => {
                                e.currentTarget.style.transform = 'translateY(-2px)';
                                e.currentTarget.style.boxShadow = '0 6px 20px rgba(6, 182, 212, 0.5)';
                            }}
                            onMouseLeave={(e) => {
                                e.currentTarget.style.transform = 'translateY(0)';
                                e.currentTarget.style.boxShadow = '0 4px 15px rgba(6, 182, 212, 0.4)';
                            }}>
                                Sign In to Your Account
                            </button>
                        </Link>
                        
                        <div style={{ 
                            display: 'flex', 
                            alignItems: 'center', 
                            gap: '1rem',
                            margin: '0.5rem 0'
                        }}>
                            <div style={{ flex: 1, height: '1px', background: '#e2e8f0' }}></div>
                            <span style={{ color: '#94a3b8', fontSize: '0.875rem', fontWeight: '500' }}>OR</span>
                            <div style={{ flex: 1, height: '1px', background: '#e2e8f0' }}></div>
                        </div>

                        <Link to="/register" style={{ textDecoration: 'none' }}>
                            <button style={{
                                width: '100%',
                                padding: '1rem',
                                fontSize: '1.1rem',
                                fontWeight: '600',
                                color: '#0891b2',
                                background: 'white',
                                border: '2px solid #06b6d4',
                                borderRadius: '12px',
                                cursor: 'pointer',
                                transition: 'all 0.3s ease'
                            }}
                            onMouseEnter={(e) => {
                                e.currentTarget.style.background = '#ecfeff';
                                e.currentTarget.style.transform = 'translateY(-2px)';
                            }}
                            onMouseLeave={(e) => {
                                e.currentTarget.style.background = 'white';
                                e.currentTarget.style.transform = 'translateY(0)';
                            }}>
                                Create New Account
                            </button>
                        </Link>
                    </div>

                    <div style={{ 
                        marginTop: '2rem',
                        paddingTop: '2rem',
                        borderTop: '1px solid #e2e8f0',
                        textAlign: 'center'
                    }}>
                        
                        <div style={{ 
                            display: 'flex', 
                            justifyContent: 'center',
                            gap: '1.5rem',
                            flexWrap: 'wrap'
                        }}>
                            <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem', color: '#64748b', fontSize: '0.875rem' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                </svg>
                                Secure
                            </div>
                            <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem', color: '#64748b', fontSize: '0.875rem' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                Reliable
                            </div>
                            <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem', color: '#64748b', fontSize: '0.875rem' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path>
                                </svg>
                                Fast
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
