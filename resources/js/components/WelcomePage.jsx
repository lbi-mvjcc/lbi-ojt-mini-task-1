import { Link } from 'react-router-dom';
import { useState } from 'react';

export default function WelcomePage() {
    const [hoveredFeature, setHoveredFeature] = useState(null);

    const features = [
        {
            icon: (
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
            ),
            title: 'Role-Based Access',
            description: 'Secure access control for admins, customers, and developers'
        },
        {
            icon: (
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            ),
            title: 'Team Collaboration',
            description: 'Connect customers with development teams seamlessly'
        },
        {
            icon: (
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                </svg>
            ),
            title: 'Real-time Tracking',
            description: 'Monitor task progress and status updates instantly'
        },
        {
            icon: (
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                </svg>
            ),
            title: 'Smart Assignment',
            description: 'Automatic task routing based on category and expertise'
        }
    ];

    return (
        <div style={{ 
            minHeight: '100vh',
            background: 'linear-gradient(135deg, #06b6d4 0%, #0891b2 100%)',
            position: 'relative',
            overflow: 'hidden'
        }}>
            {/* Animated Background Elements */}
            <div style={{
                position: 'absolute',
                top: '10%',
                left: '5%',
                width: '300px',
                height: '300px',
                background: 'rgba(255, 255, 255, 0.1)',
                borderRadius: '50%',
                filter: 'blur(60px)',
                animation: 'float 6s ease-in-out infinite'
            }}></div>
            <div style={{
                position: 'absolute',
                bottom: '10%',
                right: '5%',
                width: '400px',
                height: '400px',
                background: 'rgba(255, 255, 255, 0.08)',
                borderRadius: '50%',
                filter: 'blur(80px)',
                animation: 'float 8s ease-in-out infinite reverse'
            }}></div>

            <style>{`
                @keyframes float {
                    0%, 100% { transform: translateY(0px); }
                    50% { transform: translateY(-30px); }
                }
                @keyframes fadeInUp {
                    from {
                        opacity: 0;
                        transform: translateY(30px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
                @keyframes slideInLeft {
                    from {
                        opacity: 0;
                        transform: translateX(-50px);
                    }
                    to {
                        opacity: 1;
                        transform: translateX(0);
                    }
                }
                @keyframes slideInRight {
                    from {
                        opacity: 0;
                        transform: translateX(50px);
                    }
                    to {
                        opacity: 1;
                        transform: translateX(0);
                    }
                }
            `}</style>

            <div style={{
                minHeight: '100vh',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                padding: '2rem',
                position: 'relative',
                zIndex: 1
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
                    <div style={{ 
                        color: 'white', 
                        padding: '2rem',
                        animation: 'slideInLeft 0.8s ease-out'
                    }}>
                        {/* Brand Name */}
                        <div style={{
                            display: 'flex',
                            alignItems: 'center',
                            gap: '0.75rem',
                            marginBottom: '2rem'
                        }}>
                            <div style={{
                                width: '48px',
                                height: '48px',
                                background: 'rgba(255, 255, 255, 0.2)',
                                borderRadius: '12px',
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center',
                                backdropFilter: 'blur(10px)',
                                border: '1px solid rgba(255, 255, 255, 0.3)'
                            }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round">
                                    <polyline points="9 11 12 14 22 4"></polyline>
                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                </svg>
                            </div>
                            <span style={{
                                fontSize: '1.75rem',
                                fontWeight: '800',
                                letterSpacing: '-0.02em'
                            }}>
                                Task Sync
                            </span>
                        </div>

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
                            marginBottom: '2.5rem',
                            opacity: '0.95'
                        }}>
                            Streamline your workflow, collaborate with your team, and deliver projects on time with our powerful Task Management System.
                        </p>

                        {/* Feature Highlights */}
                        <div style={{ 
                            display: 'grid',
                            gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))',
                            gap: '1.5rem',
                            marginBottom: '2rem'
                        }}>
                            {features.map((feature, index) => (
                                <div 
                                    key={index}
                                    onMouseEnter={() => setHoveredFeature(index)}
                                    onMouseLeave={() => setHoveredFeature(null)}
                                    style={{
                                        padding: '1.5rem',
                                        background: hoveredFeature === index 
                                            ? 'rgba(255, 255, 255, 0.25)' 
                                            : 'rgba(255, 255, 255, 0.15)',
                                        borderRadius: '16px',
                                        backdropFilter: 'blur(10px)',
                                        border: '1px solid rgba(255, 255, 255, 0.2)',
                                        transition: 'all 0.3s ease',
                                        transform: hoveredFeature === index ? 'translateY(-5px)' : 'translateY(0)',
                                        cursor: 'pointer',
                                        animation: `fadeInUp 0.6s ease-out ${index * 0.1}s backwards`
                                    }}
                                >
                                    <div style={{ marginBottom: '0.75rem', opacity: 0.9 }}>
                                        {feature.icon}
                                    </div>
                                    <h3 style={{ 
                                        fontSize: '1rem', 
                                        fontWeight: '700',
                                        marginBottom: '0.5rem'
                                    }}>
                                        {feature.title}
                                    </h3>
                                    <p style={{ 
                                        fontSize: '0.875rem',
                                        opacity: 0.85,
                                        lineHeight: '1.4'
                                    }}>
                                        {feature.description}
                                    </p>
                                </div>
                            ))}
                        </div>

                        {/* Stats */}
                        <div style={{
                            display: 'flex',
                            gap: '2rem',
                            flexWrap: 'wrap',
                            paddingTop: '2rem',
                            borderTop: '1px solid rgba(255, 255, 255, 0.2)'
                        }}>
                            <div>
                                <div style={{ fontSize: '2rem', fontWeight: '800', marginBottom: '0.25rem' }}>100%</div>
                                <div style={{ fontSize: '0.875rem', opacity: 0.85 }}>Secure</div>
                            </div>
                            <div>
                                <div style={{ fontSize: '2rem', fontWeight: '800', marginBottom: '0.25rem' }}>24/7</div>
                                <div style={{ fontSize: '0.875rem', opacity: 0.85 }}>Available</div>
                            </div>
                            <div>
                                <div style={{ fontSize: '2rem', fontWeight: '800', marginBottom: '0.25rem' }}>∞</div>
                                <div style={{ fontSize: '0.875rem', opacity: 0.85 }}>Projects</div>
                            </div>
                        </div>
                    </div>

                    {/* Right Side - Login Card */}
                    <div style={{ 
                        background: 'white',
                        borderRadius: '24px',
                        padding: '3rem',
                        boxShadow: '0 20px 60px rgba(0,0,0,0.3)',
                        maxWidth: '450px',
                        margin: '0 auto',
                        width: '100%',
                        animation: 'slideInRight 0.8s ease-out'
                    }}>
                        <div style={{ textAlign: 'center', marginBottom: '2.5rem' }}>
                            <h2 style={{ 
                                color: '#1e293b', 
                                marginBottom: '0.75rem', 
                                fontSize: '2rem',
                                fontWeight: '800'
                            }}>
                                Welcome Back!
                            </h2>
                            <p style={{ color: '#64748b', fontSize: '1rem', lineHeight: '1.5' }}>
                                Sign in to access your dashboard and manage your tasks efficiently
                            </p>
                        </div>

                        <div style={{ display: 'flex', flexDirection: 'column', gap: '1rem' }}>
                            <Link to="/login" style={{ textDecoration: 'none' }}>
                                <button style={{
                                    width: '100%',
                                    padding: '1.125rem',
                                    fontSize: '1.1rem',
                                    fontWeight: '600',
                                    color: 'white',
                                    background: 'linear-gradient(135deg, #06b6d4 0%, #0891b2 100%)',
                                    border: 'none',
                                    borderRadius: '12px',
                                    cursor: 'pointer',
                                    transition: 'all 0.3s ease',
                                    boxShadow: '0 4px 15px rgba(6, 182, 212, 0.4)',
                                    display: 'flex',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    gap: '0.5rem'
                                }}
                                onMouseEnter={(e) => {
                                    e.currentTarget.style.transform = 'translateY(-2px)';
                                    e.currentTarget.style.boxShadow = '0 6px 20px rgba(6, 182, 212, 0.5)';
                                }}
                                onMouseLeave={(e) => {
                                    e.currentTarget.style.transform = 'translateY(0)';
                                    e.currentTarget.style.boxShadow = '0 4px 15px rgba(6, 182, 212, 0.4)';
                                }}>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                                        <polyline points="10 17 15 12 10 7"></polyline>
                                        <line x1="15" y1="12" x2="3" y2="12"></line>
                                    </svg>
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
                                    padding: '1.125rem',
                                    fontSize: '1.1rem',
                                    fontWeight: '600',
                                    color: '#0891b2',
                                    background: 'white',
                                    border: '2px solid #06b6d4',
                                    borderRadius: '12px',
                                    cursor: 'pointer',
                                    transition: 'all 0.3s ease',
                                    display: 'flex',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    gap: '0.5rem'
                                }}
                                onMouseEnter={(e) => {
                                    e.currentTarget.style.background = '#ecfeff';
                                    e.currentTarget.style.transform = 'translateY(-2px)';
                                }}
                                onMouseLeave={(e) => {
                                    e.currentTarget.style.background = 'white';
                                    e.currentTarget.style.transform = 'translateY(0)';
                                }}>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <line x1="19" y1="8" x2="19" y2="14"></line>
                                        <line x1="22" y1="11" x2="16" y2="11"></line>
                                    </svg>
                                    Create New Account
                                </button>
                            </Link>
                        </div>

                        <div style={{ 
                            marginTop: '2.5rem',
                            paddingTop: '2rem',
                            borderTop: '1px solid #e2e8f0',
                            textAlign: 'center'
                        }}>
                            <p style={{ 
                                color: '#94a3b8', 
                                fontSize: '0.875rem',
                                marginBottom: '1rem'
                            }}>
                                Trusted by teams worldwide
                            </p>
                            <div style={{ 
                                display: 'flex', 
                                justifyContent: 'center',
                                gap: '2rem',
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
        </div>
    );
}
