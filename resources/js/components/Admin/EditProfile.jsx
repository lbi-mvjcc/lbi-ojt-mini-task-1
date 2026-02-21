import { useState, useEffect } from 'react';
import axios from 'axios';
import { useNavigate } from 'react-router-dom';
import Layout from '../Layout';
import Toast from '../Toast';
import { useAuth } from '../../context/AuthContext';
import { useTheme } from '../../context/ThemeContext';

export default function EditProfile() {
    const { user: currentUser, setUser } = useAuth();
    const { theme, toggleTheme, isDark } = useTheme();
    const navigate = useNavigate();
    const [loading, setLoading] = useState(false);
    const [toastMessage, setToastMessage] = useState('');
    const [toastType, setToastType] = useState('success');
    const [showPassword, setShowPassword] = useState(false);
    const [profilePicture, setProfilePicture] = useState(null);
    const [previewUrl, setPreviewUrl] = useState(null);
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        password: '',
        role: '',
    });

    useEffect(() => {
        if (currentUser) {
            setFormData({
                name: currentUser.name,
                email: currentUser.email,
                password: '',
                role: currentUser.role,
            });
            if (currentUser.profile_picture) {
                setPreviewUrl(`/storage/${currentUser.profile_picture}`);
            }
        }
    }, [currentUser]);

    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const handleFileChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setProfilePicture(file);
            const reader = new FileReader();
            reader.onloadend = () => {
                setPreviewUrl(reader.result);
            };
            reader.readAsDataURL(file);
        }
    };

    const handleRemovePicture = () => {
        setProfilePicture(null);
        setPreviewUrl(currentUser.profile_picture ? `/storage/${currentUser.profile_picture}` : null);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);

        try {
            const formDataToSend = new FormData();
            formDataToSend.append('name', formData.name);
            formDataToSend.append('email', formData.email);
            formDataToSend.append('role', formData.role);
            
            if (formData.password) {
                formDataToSend.append('password', formData.password);
            }
            
            if (profilePicture) {
                formDataToSend.append('profile_picture', profilePicture);
            }

            const response = await axios.post(`/api/admin/users/${currentUser.id}`, formDataToSend, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
                params: {
                    _method: 'PUT'
                }
            });

            // Update user in context
            if (setUser) {
                setUser(response.data);
            }

            setToastMessage('Profile updated successfully');
            setToastType('success');
            
            // Redirect back to dashboard after 1.5 seconds
            setTimeout(() => {
                navigate('/admin/dashboard');
            }, 1500);
        } catch (err) {
            setToastMessage(err.response?.data?.message || 'Failed to update profile');
            setToastType('error');
        } finally {
            setLoading(false);
        }
    };

    const handleCancel = () => {
        navigate('/admin/dashboard');
    };

    return (
        <Layout>
            <Toast 
                message={toastMessage} 
                type={toastType} 
                onClose={() => setToastMessage('')}
            />

            <div className="dashboard-header" style={{ justifyContent: 'center', textAlign: 'center', position: 'relative' }}>
                <button 
                    onClick={handleCancel}
                    className="btn btn-secondary"
                    style={{ 
                        position: 'absolute', 
                        left: 0,
                        display: 'flex',
                        alignItems: 'center',
                        gap: '0.5rem'
                    }}
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Back
                </button>
                <h1>Edit Profile</h1>
            </div>

            <div className="card" style={{ maxWidth: '600px', margin: '0 auto' }}>
                <form onSubmit={handleSubmit}>
                    {/* Profile Picture Section */}
                    <div style={{ textAlign: 'center', marginBottom: '2rem' }}>
                        <div style={{ 
                            width: '150px', 
                            height: '150px', 
                            margin: '0 auto 1rem',
                            borderRadius: '50%',
                            overflow: 'hidden',
                            border: '4px solid #e2e8f0',
                            background: '#f1f5f9',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center'
                        }}>
                            {previewUrl ? (
                                <img 
                                    src={previewUrl} 
                                    alt="Profile" 
                                    style={{ 
                                        width: '100%', 
                                        height: '100%', 
                                        objectFit: 'cover' 
                                    }} 
                                />
                            ) : (
                                <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            )}
                        </div>
                        <div style={{ display: 'flex', gap: '0.5rem', justifyContent: 'center', flexWrap: 'wrap' }}>
                            <label className="btn btn-secondary" style={{ cursor: 'pointer' }}>
                                <input 
                                    type="file" 
                                    accept="image/*" 
                                    onChange={handleFileChange}
                                    style={{ display: 'none' }}
                                />
                                Choose Photo
                            </label>
                            {previewUrl && (
                                <button 
                                    type="button" 
                                    onClick={handleRemovePicture}
                                    className="btn btn-secondary"
                                >
                                    Remove
                                </button>
                            )}
                        </div>
                        <small style={{ color: '#64748b', display: 'block', marginTop: '0.5rem' }}>
                            JPG, PNG or GIF (max 2MB)
                        </small>
                    </div>

                    <div className="form-group">
                        <label>Name</label>
                        <input
                            type="text"
                            name="name"
                            value={formData.name}
                            onChange={handleChange}
                            required
                        />
                    </div>

                    <div className="form-group">
                        <label>Email</label>
                        <input
                            type="email"
                            name="email"
                            value={formData.email}
                            onChange={handleChange}
                            required
                        />
                    </div>

                    <div className="form-group">
                        <label>Password (leave blank to keep current)</label>
                        <div className="password-input-wrapper">
                            <input
                                type={showPassword ? 'text' : 'password'}
                                name="password"
                                value={formData.password}
                                onChange={handleChange}
                                minLength={8}
                            />
                            <button
                                type="button"
                                onClick={() => setShowPassword(!showPassword)}
                                className="password-toggle"
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

                    <div className="form-group">
                        <label>Role</label>
                        <select
                            name="role"
                            value={formData.role}
                            disabled
                            style={{ 
                                backgroundColor: '#f1f5f9',
                                cursor: 'not-allowed',
                                color: '#64748b'
                            }}
                        >
                            <option value="admin">Admin</option>
                        </select>
                        <small className="form-text" style={{ color: '#64748b' }}>
                            You cannot change your own role
                        </small>
                    </div>

                    <div className="form-group">
                        <label>Theme</label>
                        <div style={{ 
                            display: 'flex', 
                            alignItems: 'center', 
                            gap: '1rem',
                            padding: '1rem',
                            background: '#f8fafc',
                            borderRadius: '15px',
                            border: '2px solid #cbd5e1'
                        }}>
                            <span style={{ 
                                flex: 1, 
                                fontWeight: '600',
                                color: '#1e293b'
                            }}>
                                {isDark ? 'Dark Mode' : 'Light Mode'}
                            </span>
                            <button
                                type="button"
                                onClick={toggleTheme}
                                style={{
                                    position: 'relative',
                                    width: '60px',
                                    height: '32px',
                                    background: isDark ? 'linear-gradient(135deg, #06b6d4 0%, #0891b2 100%)' : '#cbd5e1',
                                    borderRadius: '16px',
                                    border: 'none',
                                    cursor: 'pointer',
                                    transition: 'all 0.3s ease',
                                    boxShadow: isDark ? '0 2px 8px rgba(6, 182, 212, 0.4)' : '0 2px 4px rgba(0, 0, 0, 0.1)'
                                }}
                            >
                                <div style={{
                                    position: 'absolute',
                                    top: '4px',
                                    left: isDark ? '32px' : '4px',
                                    width: '24px',
                                    height: '24px',
                                    background: 'white',
                                    borderRadius: '50%',
                                    transition: 'all 0.3s ease',
                                    display: 'flex',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    boxShadow: '0 2px 4px rgba(0, 0, 0, 0.2)'
                                }}>
                                    {isDark ? (
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#06b6d4" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                                        </svg>
                                    ) : (
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                            <circle cx="12" cy="12" r="5"></circle>
                                            <line x1="12" y1="1" x2="12" y2="3"></line>
                                            <line x1="12" y1="21" x2="12" y2="23"></line>
                                            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                                            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                                            <line x1="1" y1="12" x2="3" y2="12"></line>
                                            <line x1="21" y1="12" x2="23" y2="12"></line>
                                            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                                            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                                        </svg>
                                    )}
                                </div>
                            </button>
                        </div>
                        <small className="form-text" style={{ color: '#64748b' }}>
                            Toggle between light and dark mode
                        </small>
                    </div>

                    <div className="form-actions">
                        <button 
                            type="submit" 
                            className="btn btn-primary"
                            disabled={loading}
                        >
                            {loading ? 'Updating...' : 'Update Profile'}
                        </button>
                        <button 
                            type="button" 
                            onClick={handleCancel} 
                            className="btn btn-secondary"
                            disabled={loading}
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </Layout>
    );
}
