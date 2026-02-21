import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';
import ConfirmDialog from '../ConfirmDialog';
import Toast from '../Toast';
import SuccessModal from '../SuccessModal';
import { useAuth } from '../../context/AuthContext';
import { useTheme } from '../../context/ThemeContext';

export default function UsersManagement() {
    const { user: currentUser } = useAuth();
    const { isDark } = useTheme();
    const navigate = useNavigate();
    const [users, setUsers] = useState([]);
    const [filteredUsers, setFilteredUsers] = useState([]);
    const [projects, setProjects] = useState([]);
    const [loading, setLoading] = useState(true);
    const [showForm, setShowForm] = useState(false);
    const [showEditModal, setShowEditModal] = useState(false);
    const [showCreateModal, setShowCreateModal] = useState(false);
    const [editingUser, setEditingUser] = useState(null);
    const [showConfirm, setShowConfirm] = useState(false);
    const [userToDelete, setUserToDelete] = useState(null);
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');
    const [toastMessage, setToastMessage] = useState('');
    const [toastType, setToastType] = useState('success');
    const [successMessage, setSuccessMessage] = useState('');
    const [searchTerm, setSearchTerm] = useState('');
    const [currentPage, setCurrentPage] = useState(1);
    const [usersPerPage] = useState(10);
    const [showPassword, setShowPassword] = useState(false);
    const [showConfirmPassword, setShowConfirmPassword] = useState(false);
    const [viewMode, setViewMode] = useState('active');
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        role: '',
        project_id: '',
    });

    useEffect(() => {
        setLoading(true);
        fetchUsers();
        fetchProjects();
    }, [viewMode]);

    useEffect(() => {
        // Filter users based on search term
        const filtered = users.filter(user => 
            user.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
            user.email.toLowerCase().includes(searchTerm.toLowerCase()) ||
            user.role.toLowerCase().includes(searchTerm.toLowerCase())
        );
        setFilteredUsers(filtered);
        setCurrentPage(1);
    }, [searchTerm, users]);

    const fetchUsers = async () => {
        try {
            const endpoint = viewMode === 'deleted' ? '/api/admin/users/trashed/all' : '/api/admin/users';
            const response = await axios.get(endpoint);
            // Sort users alphabetically by name
            const sortedUsers = response.data.sort((a, b) => a.name.localeCompare(b.name));
            setUsers(sortedUsers);
            setFilteredUsers(sortedUsers);
        } catch (error) {
            setError('Failed to load users');
            console.error('Error fetching users:', error);
        } finally {
            setLoading(false);
        }
    };

    const fetchProjects = async () => {
        try {
            const response = await axios.get('/api/admin/projects');
            setProjects(response.data);
        } catch (error) {
            console.error('Error fetching projects:', error);
        }
    };

    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError('');
        setSuccess('');

        // Validate password confirmation if password is provided
        if (formData.password && formData.password !== formData.password_confirmation) {
            setToastMessage('Passwords do not match');
            setToastType('error');
            return;
        }

        try {
            if (editingUser) {
                await axios.put(`/api/admin/users/${editingUser.id}`, formData);
                setSuccessMessage('User updated successfully');
                setShowEditModal(false);
            } else {
                await axios.post('/api/admin/users', formData);
                setSuccessMessage('User created successfully');
                setShowCreateModal(false);
            }
            fetchUsers();
            resetForm();
        } catch (err) {
            setToastMessage(err.response?.data?.message || 'Operation failed');
            setToastType('error');
        }
    };

    const handleEdit = (user) => {
        setEditingUser(user);
        setFormData({
            name: user.name,
            email: user.email,
            password: '',
            role: user.role,
            project_id: user.project_id || '',
        });
        setShowEditModal(true);
    };

    const handleDeleteClick = (user) => {
        setUserToDelete(user);
        setShowConfirm(true);
    };

    const handleConfirmDelete = async () => {
        try {
            if (viewMode === 'deleted') {
                await axios.delete(`/api/admin/users/${userToDelete.id}/force`);
                setSuccessMessage('User permanently deleted');
            } else {
                await axios.delete(`/api/admin/users/${userToDelete.id}`);
                setSuccessMessage('User deleted successfully');
            }
            fetchUsers();
        } catch (err) {
            setToastMessage(err.response?.data?.message || 'Failed to delete user');
            setToastType('error');
        }
        setShowConfirm(false);
        setUserToDelete(null);
    };

    const handleRestore = async (user) => {
        try {
            await axios.post(`/api/admin/users/${user.id}/restore`);
            setSuccessMessage('User restored successfully');
            fetchUsers();
        } catch (err) {
            setToastMessage(err.response?.data?.message || 'Failed to restore user');
            setToastType('error');
        }
    };

    const resetForm = () => {
        setFormData({
            name: '',
            email: '',
            password: '',
            password_confirmation: '',
            role: '',
            project_id: '',
        });
        setEditingUser(null);
        setShowForm(false);
        setShowEditModal(false);
        setShowCreateModal(false);
        setShowPassword(false);
        setShowConfirmPassword(false);
    };

    const getRoleBadge = (role) => {
        const badges = {
            admin: 'badge-completed',
            customer: 'badge-pending',
            frontend_developer: 'badge-frontend',
            backend_developer: 'badge-backend',
            server_admin: 'badge-server',
        };
        return badges[role] || 'badge-pending';
    };

    const getRoleLabel = (role) => {
        const labels = {
            admin: 'Admin',
            customer: 'Customer',
            frontend_developer: 'Frontend Dev',
            backend_developer: 'Backend Dev',
            server_admin: 'Server Admin',
        };
        return labels[role] || role;
    };

    const getProjectCustomer = (projectId) => {
        return users.find(u => u.role === 'customer' && u.project_id === projectId);
    };

    const isProjectAvailable = (projectId) => {
        const customer = getProjectCustomer(projectId);
        // Project is available if no customer or if the customer is the one being edited
        return !customer || (editingUser && customer.id === editingUser.id);
    };

    // Pagination logic
    const indexOfLastUser = currentPage * usersPerPage;
    const indexOfFirstUser = indexOfLastUser - usersPerPage;
    const currentUsers = filteredUsers.slice(indexOfFirstUser, indexOfLastUser);
    const totalPages = Math.ceil(filteredUsers.length / usersPerPage);

    const paginate = (pageNumber) => setCurrentPage(pageNumber);

    const handleSearchChange = (e) => {
        setSearchTerm(e.target.value);
    };

    if (loading) {
        return <div className="loading">Loading users...</div>;
    }

    return (
        <div>
            <Toast 
                message={toastMessage} 
                type={toastType} 
                onClose={() => setToastMessage('')}
            />
            
            <SuccessModal 
                message={successMessage}
                onClose={() => setSuccessMessage('')}
            />
            
            <ConfirmDialog
                isOpen={showConfirm}
                title="Confirm Delete"
                message={`Are you sure you want to delete user "${userToDelete?.name}"? This action cannot be undone.`}
                onConfirm={handleConfirmDelete}
                onCancel={() => setShowConfirm(false)}
            />

            {/* Edit User Modal */}
            {showEditModal && (
                <div className="modal-overlay" onClick={() => setShowEditModal(false)}>
                    <div className="modal-content" style={{ maxWidth: '600px', width: '95%' }} onClick={(e) => e.stopPropagation()}>
                        <div className="modal-header">
                            <h3>Edit User</h3>
                        </div>
                        <div className="modal-body">
                            <form onSubmit={handleSubmit} id="editUserForm">
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

                                {formData.password && (
                                    <div className="form-group">
                                        <label>Confirm Password</label>
                                        <div className="password-input-wrapper">
                                            <input
                                                type={showConfirmPassword ? 'text' : 'password'}
                                                name="password_confirmation"
                                                value={formData.password_confirmation}
                                                onChange={handleChange}
                                                minLength={8}
                                            />
                                            <button
                                                type="button"
                                                onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                                                className="password-toggle"
                                            >
                                                {showConfirmPassword ? (
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
                                        {formData.password_confirmation && formData.password !== formData.password_confirmation && (
                                            <small className="form-text" style={{ color: '#dc2626', marginTop: '0.5rem', display: 'block' }}>
                                                Passwords do not match
                                            </small>
                                        )}
                                    </div>
                                )}

                                <div className="form-group">
                                    <label>Role</label>
                                    <select
                                        name="role"
                                        value={formData.role}
                                        onChange={handleChange}
                                        required
                                        disabled
                                        style={{
                                            backgroundColor: '#e2e8f0',
                                            cursor: 'not-allowed',
                                            color: '#475569',
                                            opacity: 0.7,
                                            pointerEvents: 'none',
                                            WebkitAppearance: 'none',
                                            MozAppearance: 'none',
                                            appearance: 'none',
                                            backgroundImage: 'none',
                                            paddingRight: '1rem'
                                        }}
                                    >
                                        <option value="">Select Role</option>
                                        <option value="admin">Admin</option>
                                        <option value="customer">Customer</option>
                                        <option value="frontend_developer">Frontend Developer</option>
                                        <option value="backend_developer">Backend Developer</option>
                                        <option value="server_admin">Server Administrator</option>
                                    </select>
                                    <small className="form-text" style={{ color: '#64748b', marginTop: '0.5rem', display: 'block' }}>
                                        Role cannot be changed after user creation
                                    </small>
                                </div>

                                {formData.role === 'customer' && (
                                    <div className="form-group">
                                        <label>Project (for customers)</label>
                                        <select
                                            name="project_id"
                                            value={formData.project_id}
                                            onChange={handleChange}
                                        >
                                            <option value="">No Project</option>
                                            {projects.map(project => {
                                                const customer = getProjectCustomer(project.id);
                                                const available = isProjectAvailable(project.id);
                                                return (
                                                    <option 
                                                        key={project.id} 
                                                        value={project.id}
                                                        disabled={!available}
                                                    >
                                                        {project.name} {!available ? `(Assigned to ${customer.name})` : ''}
                                                    </option>
                                                );
                                            })}
                                        </select>
                                    </div>
                                )}
                            </form>
                        </div>
                        <div className="modal-footer">
                            <button type="button" onClick={() => setShowEditModal(false)} className="btn btn-secondary">
                                Cancel
                            </button>
                            <button type="submit" form="editUserForm" className="btn btn-primary">
                                Update User
                            </button>
                        </div>
                    </div>
                </div>
            )}

            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '2rem', gap: '1rem', flexWrap: 'wrap' }}>
                <h2>User Management</h2>
                <div style={{ display: 'flex', gap: '1rem', alignItems: 'center', flex: 1, maxWidth: '700px' }}>
                    <div style={{ 
                        display: 'flex',
                        gap: '2rem',
                        alignItems: 'center'
                    }}>
                        <button
                            onClick={() => setViewMode('active')}
                            style={{ 
                                background: 'none',
                                border: 'none',
                                color: viewMode === 'active' ? '#06b6d4' : (isDark ? '#ffffff' : '#64748b'),
                                cursor: 'pointer',
                                fontSize: '1rem',
                                fontWeight: viewMode === 'active' ? '600' : '500',
                                padding: '0.5rem 0',
                                transition: 'all 0.3s ease',
                                position: 'relative',
                                borderBottom: viewMode === 'active' ? '2px solid #06b6d4' : '2px solid transparent'
                            }}
                            onMouseEnter={(e) => {
                                if (viewMode !== 'active') {
                                    e.target.style.color = '#06b6d4';
                                }
                            }}
                            onMouseLeave={(e) => {
                                if (viewMode !== 'active') {
                                    e.target.style.color = isDark ? '#ffffff' : '#64748b';
                                }
                            }}
                        >
                            Active
                        </button>
                        <button
                            onClick={() => setViewMode('deleted')}
                            style={{ 
                                background: 'none',
                                border: 'none',
                                color: viewMode === 'deleted' ? '#06b6d4' : (isDark ? '#ffffff' : '#64748b'),
                                cursor: 'pointer',
                                fontSize: '1rem',
                                fontWeight: viewMode === 'deleted' ? '600' : '500',
                                padding: '0.5rem 0',
                                transition: 'all 0.3s ease',
                                position: 'relative',
                                borderBottom: viewMode === 'deleted' ? '2px solid #06b6d4' : '2px solid transparent'
                            }}
                            onMouseEnter={(e) => {
                                if (viewMode !== 'deleted') {
                                    e.target.style.color = '#06b6d4';
                                }
                            }}
                            onMouseLeave={(e) => {
                                if (viewMode !== 'deleted') {
                                    e.target.style.color = isDark ? '#ffffff' : '#64748b';
                                }
                            }}
                        >
                            Deleted
                        </button>
                    </div>
                    <div style={{ position: 'relative', flex: 1 }}>
                        <input
                            type="text"
                            placeholder="Search by name, email, or role..."
                            value={searchTerm}
                            onChange={handleSearchChange}
                            style={{
                                width: '100%',
                                padding: '0.75rem 1rem 0.75rem 2.5rem',
                                border: '2px solid #cbd5e1',
                                borderRadius: '25px',
                                fontSize: '0.95rem',
                            }}
                        />
                        <svg 
                            xmlns="http://www.w3.org/2000/svg" 
                            width="20" 
                            height="20" 
                            viewBox="0 0 24 24" 
                            fill="none" 
                            stroke="#64748b" 
                            strokeWidth="2" 
                            strokeLinecap="round" 
                            strokeLinejoin="round"
                            style={{
                                position: 'absolute',
                                left: '1rem',
                                top: '50%',
                                transform: 'translateY(-50%)',
                            }}
                        >
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                    </div>
                    {viewMode === 'active' && (
                        <button
                            onClick={() => setShowCreateModal(true)}
                            className="btn btn-primary"
                            style={{ whiteSpace: 'nowrap' }}
                        >
                            + Add User
                        </button>
                    )}
                </div>
            </div>

            {error && <div className="alert alert-error">{error}</div>}
            {success && <div className="alert alert-success">{success}</div>}

            {/* Create User Modal */}
            {showCreateModal && (
                <div className="modal-overlay" onClick={() => setShowCreateModal(false)}>
                    <div className="modal-content" style={{ maxWidth: '600px', width: '95%' }} onClick={(e) => e.stopPropagation()}>
                        <div className="modal-header">
                            <h3>Create New User</h3>
                        </div>
                        <div className="modal-body">
                            <form onSubmit={handleSubmit} id="createUserForm">
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
                                    <label>Password</label>
                                    <div className="password-input-wrapper">
                                        <input
                                            type={showPassword ? 'text' : 'password'}
                                            name="password"
                                            value={formData.password}
                                            onChange={handleChange}
                                            required
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
                                    <label>Confirm Password</label>
                                    <div className="password-input-wrapper">
                                        <input
                                            type={showConfirmPassword ? 'text' : 'password'}
                                            name="password_confirmation"
                                            value={formData.password_confirmation}
                                            onChange={handleChange}
                                            required
                                            minLength={8}
                                        />
                                        <button
                                            type="button"
                                            onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                                            className="password-toggle"
                                        >
                                            {showConfirmPassword ? (
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
                                        onChange={handleChange}
                                        required
                                    >
                                        <option value="">Select Role</option>
                                        <option value="admin">Admin</option>
                                        <option value="customer">Customer</option>
                                        <option value="frontend_developer">Frontend Developer</option>
                                        <option value="backend_developer">Backend Developer</option>
                                        <option value="server_admin">Server Administrator</option>
                                    </select>
                                </div>

                                {formData.role === 'customer' && (
                                    <div className="form-group">
                                        <label>Project (for customers)</label>
                                        <select
                                            name="project_id"
                                            value={formData.project_id}
                                            onChange={handleChange}
                                        >
                                            <option value="">No Project</option>
                                            {projects.map(project => {
                                                const customer = getProjectCustomer(project.id);
                                                const available = isProjectAvailable(project.id);
                                                return (
                                                    <option 
                                                        key={project.id} 
                                                        value={project.id}
                                                        disabled={!available}
                                                    >
                                                        {project.name} {!available ? `(Assigned to ${customer.name})` : ''}
                                                    </option>
                                                );
                                            })}
                                        </select>
                                    </div>
                                )}
                            </form>
                        </div>
                        <div className="modal-footer">
                            <button type="button" onClick={() => setShowCreateModal(false)} className="btn btn-secondary">
                                Cancel
                            </button>
                            <button type="submit" form="createUserForm" className="btn btn-primary">
                                Create User
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {showForm && (
                <div className="card" style={{ marginBottom: '2rem' }}>
                    <h3>Create New User</h3>
                    <form onSubmit={handleSubmit}>
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
                            <label>Password</label>
                            <div className="password-input-wrapper">
                                <input
                                    type={showPassword ? 'text' : 'password'}
                                    name="password"
                                    value={formData.password}
                                    onChange={handleChange}
                                    required
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
                                onChange={handleChange}
                                required
                            >
                                <option value="">Select Role</option>
                                <option value="admin">Admin</option>
                                <option value="customer">Customer</option>
                                <option value="frontend_developer">Frontend Developer</option>
                                <option value="backend_developer">Backend Developer</option>
                                <option value="server_admin">Server Administrator</option>
                            </select>
                        </div>

                        {formData.role === 'customer' && (
                            <div className="form-group">
                                <label>Project (for customers)</label>
                                <select
                                    name="project_id"
                                    value={formData.project_id}
                                    onChange={handleChange}
                                >
                                    <option value="">No Project</option>
                                    {projects
                                        .filter(project => {
                                            const available = isProjectAvailable(project.id);
                                            // When creating new user, only show available projects
                                            // When editing, show all projects but disable unavailable ones
                                            return editingUser ? true : available;
                                        })
                                        .map(project => {
                                            const customer = getProjectCustomer(project.id);
                                            const available = isProjectAvailable(project.id);
                                            return (
                                                <option 
                                                    key={project.id} 
                                                    value={project.id}
                                                    disabled={!available}
                                                >
                                                    {project.name} {!available ? `(Assigned to ${customer.name})` : ''}
                                                </option>
                                            );
                                        })}
                                </select>
                                <small style={{ display: 'block', marginTop: '0.5rem', color: '#64748b' }}>
                                    Only unassigned projects are shown
                                </small>
                            </div>
                        )}

                        <div className="form-actions">
                            <button type="submit" className="btn btn-primary">
                                Create User
                            </button>
                            <button type="button" onClick={resetForm} className="btn btn-secondary">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            )}

            <div className="card">
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '1rem', flexWrap: 'wrap', gap: '1rem' }}>
                    <div style={{ color: '#64748b', fontSize: '0.95rem' }}>
                        Showing {indexOfFirstUser + 1} to {Math.min(indexOfLastUser, filteredUsers.length)} of {filteredUsers.length} users
                        {searchTerm && ` (filtered from ${users.length} total)`}
                    </div>
                </div>
                
                {filteredUsers.length === 0 ? (
                    <div style={{ padding: '3rem', textAlign: 'center', color: '#64748b' }}>
                        <p style={{ fontSize: '1.1rem', marginBottom: '0.5rem' }}>
                            {viewMode === 'deleted' ? 'No deleted users found' : 'No users found'}
                        </p>
                        <p style={{ fontSize: '0.9rem' }}>
                            {viewMode === 'deleted' ? 'Deleted users will appear here' : searchTerm ? 'Try adjusting your search' : 'Click "+ Add User" to create a new user'}
                        </p>
                    </div>
                ) : (
                    <>
                <div className="table-container">
                    <table className="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Project</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {currentUsers.map(user => {
                                const isCurrentUser = currentUser && user.id === currentUser.id;
                                return (
                                    <tr key={user.id}>
                                        <td>
                                            {user.name}
                                            {isCurrentUser && (
                                                <span style={{ 
                                                    marginLeft: '0.5rem', 
                                                    fontSize: '0.75rem', 
                                                    color: '#0891b2',
                                                    fontWeight: '600'
                                                }}>
                                                    (You)
                                                </span>
                                            )}
                                        </td>
                                        <td>{user.email}</td>
                                        <td>
                                            <span className={`badge ${getRoleBadge(user.role)}`}>
                                                {getRoleLabel(user.role)}
                                            </span>
                                        </td>
                                        <td>{user.project?.name || '-'}</td>
                                        <td>
                                            {viewMode === 'deleted' ? (
                                                <div className="table-actions">
                                                    <button
                                                        onClick={() => handleRestore(user)}
                                                        className="btn btn-sm btn-success"
                                                    >
                                                        Restore
                                                    </button>
                                                    <button
                                                        onClick={() => handleDeleteClick(user)}
                                                        className="btn btn-sm btn-danger"
                                                    >
                                                        Delete Forever
                                                    </button>
                                                </div>
                                            ) : isCurrentUser ? (
                                                <div className="table-actions">
                                                    <button
                                                        onClick={() => navigate('/profile')}
                                                        className="btn btn-sm btn-secondary"
                                                    >
                                                        Edit Profile
                                                    </button>
                                                </div>
                                            ) : (
                                                <div className="table-actions">
                                                    <button
                                                        onClick={() => handleEdit(user)}
                                                        className="btn btn-sm btn-secondary"
                                                    >
                                                        Edit
                                                    </button>
                                                    <button
                                                        onClick={() => handleDeleteClick(user)}
                                                        className="btn btn-sm btn-danger"
                                                    >
                                                        Delete
                                                    </button>
                                                </div>
                                            )}
                                        </td>
                                    </tr>
                                );
                            })}
                        </tbody>
                    </table>
                </div>

                {/* Pagination */}
                {totalPages > 1 && (
                    <div style={{ 
                        display: 'flex', 
                        justifyContent: 'center', 
                        alignItems: 'center',
                        gap: '0.5rem',
                        marginTop: '2rem',
                        flexWrap: 'wrap'
                    }}>
                        <button
                            onClick={() => paginate(currentPage - 1)}
                            disabled={currentPage === 1}
                            className="btn btn-sm btn-secondary"
                            style={{ minWidth: '80px' }}
                        >
                            Previous
                        </button>
                        
                        <div style={{ display: 'flex', gap: '0.5rem', flexWrap: 'wrap' }}>
                            {[...Array(totalPages)].map((_, index) => (
                                <button
                                    key={index + 1}
                                    onClick={() => paginate(index + 1)}
                                    className={`btn btn-sm ${currentPage === index + 1 ? 'btn-primary' : 'btn-secondary'}`}
                                    style={{ minWidth: '40px' }}
                                >
                                    {index + 1}
                                </button>
                            ))}
                        </div>

                        <button
                            onClick={() => paginate(currentPage + 1)}
                            disabled={currentPage === totalPages}
                            className="btn btn-sm btn-secondary"
                            style={{ minWidth: '80px' }}
                        >
                            Next
                        </button>
                    </div>
                )}
                </>
                )}
            </div>
        </div>
    );
}
