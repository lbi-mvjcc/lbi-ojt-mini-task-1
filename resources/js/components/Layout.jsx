import React from 'react';
import { useAuth } from '../context/AuthContext';
import { useNavigate } from 'react-router-dom';
import { useState, useRef, useEffect } from 'react';
import { useTheme } from '../context/ThemeContext';
import ConfirmDialog from './ConfirmDialog';

export default function Layout({ children, navItems, onTitleClick }) {
    const { user, logout } = useAuth();
    const { isDark } = useTheme();
    const navigate = useNavigate();
    const [showLogoutConfirm, setShowLogoutConfirm] = useState(false);
    const [showProfileMenu, setShowProfileMenu] = useState(false);
    const profileMenuRef = useRef(null);

    useEffect(() => {
        const handleClickOutside = (event) => {
            if (profileMenuRef.current && !profileMenuRef.current.contains(event.target)) {
                setShowProfileMenu(false);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    const handleLogoutClick = () => {
        setShowProfileMenu(false);
        setShowLogoutConfirm(true);
    };
    
    const handleEditProfile = () => {
        setShowProfileMenu(false);
        navigate('/profile');
    };
    
    const toggleProfileMenu = () => {
        setShowProfileMenu(!showProfileMenu);
    };
    
    const handleConfirmLogout = async () => {
        setShowLogoutConfirm(false);
        await logout();
        navigate('/login');
    };
    
    const handleCancelLogout = () => {
        setShowLogoutConfirm(false);
    };

    const getRoleName = (role) => {
        const roles = {
            customer: 'Customer',
            frontend_developer: 'Frontend Developer',
            backend_developer: 'Backend Developer',
            server_admin: 'Server Administrator',
        };
        return roles[role] || role;
    };

    return (
        <div className="app">
            <ConfirmDialog
                isOpen={showLogoutConfirm}
                title="Confirm Logout"
                message="Are you sure you want to logout? You will need to login again to access your account."
                onConfirm={handleConfirmLogout}
                onCancel={handleCancelLogout}
            />
            
            <nav className="navbar">
                <div className="nav-content">
                    <div className="nav-left">
                        {onTitleClick ? (
                            <strong 
                                onClick={onTitleClick}
                                style={{ 
                                    cursor: 'pointer',
                                    transition: 'color 0.2s'
                                }}
                                onMouseEnter={(e) => e.target.style.color = '#0891b2'}
                                onMouseLeave={(e) => e.target.style.color = ''}
                            >
                                Task Sync
                            </strong>
                        ) : (
                            <strong>Task Sync</strong>
                        )}
                    </div>
                    <div className="nav-right">
                        {navItems && navItems.length > 0 && (
                            <div style={{ display: 'flex', gap: '2.5rem', marginRight: '1.5rem', alignItems: 'center' }}>
                                {navItems.map((item, index) => (
                                    <button
                                        key={index}
                                        onClick={item.onClick}
                                        className={`nav-item-btn ${item.active ? 'active' : ''}`}
                                    >
                                        {item.label}
                                    </button>
                                ))}
                            </div>
                        )}
                        {user && (
                            <div ref={profileMenuRef} style={{ position: 'relative' }}>
                                <button 
                                    onClick={toggleProfileMenu} 
                                    className="btn-logout"
                                    style={{ 
                                        padding: '0.5rem',
                                        width: '40px',
                                        height: '40px',
                                        display: 'flex',
                                        alignItems: 'center',
                                        justifyContent: 'center',
                                        borderRadius: '50%'
                                    }}
                                    title="Profile Menu"
                                >
                                    {user.profile_picture ? (
                                        <img 
                                            src={`/storage/${user.profile_picture}`} 
                                            alt={user.name}
                                            style={{
                                                width: '28px',
                                                height: '28px',
                                                borderRadius: '50%',
                                                objectFit: 'cover'
                                            }}
                                        />
                                    ) : (
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                    )}
                                </button>
                                
                                {showProfileMenu && (
                                    <div className="profile-dropdown">
                                        <button onClick={handleEditProfile} className="profile-dropdown-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="12" cy="7" r="4"></circle>
                                            </svg>
                                            Edit Profile
                                        </button>
                                        <button onClick={handleLogoutClick} className="profile-dropdown-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                                <polyline points="16 17 21 12 16 7"></polyline>
                                                <line x1="21" y1="12" x2="9" y2="12"></line>
                                            </svg>
                                            Logout
                                        </button>
                                    </div>
                                )}
                            </div>
                        )}
                    </div>
                </div>
            </nav>

            <div className="container">{children}</div>
        </div>
    );
}
