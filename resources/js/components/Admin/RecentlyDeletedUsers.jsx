import { useState, useEffect } from 'react';
import axios from 'axios';
import ConfirmDialog from '../ConfirmDialog';
import Toast from '../Toast';

export default function RecentlyDeletedUsers() {
    const [users, setUsers] = useState([]);
    const [loading, setLoading] = useState(true);
    const [toastMessage, setToastMessage] = useState('');
    const [toastType, setToastType] = useState('success');
    const [showRestoreConfirm, setShowRestoreConfirm] = useState(false);
    const [showDeleteConfirm, setShowDeleteConfirm] = useState(false);
    const [selectedUser, setSelectedUser] = useState(null);

    useEffect(() => {
        fetchDeletedUsers();
    }, []);

    const fetchDeletedUsers = async () => {
        try {
            const response = await axios.get('/api/admin/users/trashed/all');
            setUsers(response.data);
        } catch (error) {
            console.error('Error fetching deleted users:', error);
            setToastMessage('Failed to load deleted users');
            setToastType('error');
        } finally {
            setLoading(false);
        }
    };

    const handleRestoreClick = (user) => {
        setSelectedUser(user);
        setShowRestoreConfirm(true);
    };

    const handleConfirmRestore = async () => {
        try {
            await axios.post(`/api/admin/users/${selectedUser.id}/restore`);
            setToastMessage('User restored successfully');
            setToastType('success');
            fetchDeletedUsers();
        } catch (error) {
            setToastMessage('Failed to restore user');
            setToastType('error');
        }
        setShowRestoreConfirm(false);
        setSelectedUser(null);
    };

    const handleDeleteClick = (user) => {
        setSelectedUser(user);
        setShowDeleteConfirm(true);
    };

    const handleConfirmDelete = async () => {
        try {
            await axios.delete(`/api/admin/users/${selectedUser.id}/force`);
            setToastMessage('User permanently deleted');
            setToastType('success');
            fetchDeletedUsers();
        } catch (error) {
            setToastMessage('Failed to delete user');
            setToastType('error');
        }
        setShowDeleteConfirm(false);
        setSelectedUser(null);
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

    if (loading) {
        return <div className="loading">Loading deleted users...</div>;
    }

    return (
        <div>
            <Toast 
                message={toastMessage} 
                type={toastType} 
                onClose={() => setToastMessage('')}
            />

            <ConfirmDialog
                isOpen={showRestoreConfirm}
                title="Confirm Restore"
                message={`Are you sure you want to restore user "${selectedUser?.name}"? This will restore their account and all access.`}
                onConfirm={handleConfirmRestore}
                onCancel={() => setShowRestoreConfirm(false)}
            />

            <ConfirmDialog
                isOpen={showDeleteConfirm}
                title="Confirm Permanent Delete"
                message={`Are you sure you want to permanently delete user "${selectedUser?.name}"? This action cannot be undone.`}
                onConfirm={handleConfirmDelete}
                onCancel={() => setShowDeleteConfirm(false)}
            />

            <h2 style={{ marginBottom: '2rem' }}>Recently Deleted Users</h2>

            {users.length === 0 ? (
                <div className="card">
                    <p className="text-center text-muted">
                        No deleted users. Deleted users will appear here.
                    </p>
                </div>
            ) : (
                <div className="card">
                    <div className="table-container">
                        <table className="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Project</th>
                                    <th>Deleted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {users.map((user) => (
                                    <tr key={user.id}>
                                        <td>{user.name}</td>
                                        <td>{user.email}</td>
                                        <td>
                                            <span className={`badge ${getRoleBadge(user.role)}`}>
                                                {getRoleLabel(user.role)}
                                            </span>
                                        </td>
                                        <td>{user.project?.name || '-'}</td>
                                        <td>{new Date(user.deleted_at).toLocaleDateString()}</td>
                                        <td>
                                            <div className="table-actions">
                                                <button
                                                    onClick={() => handleRestoreClick(user)}
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
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            )}
        </div>
    );
}
