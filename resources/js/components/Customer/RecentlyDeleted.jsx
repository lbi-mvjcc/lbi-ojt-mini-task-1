import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import axios from 'axios';
import Layout from '../Layout';
import ConfirmDialog from '../ConfirmDialog';
import Toast from '../Toast';
import SuccessModal from '../SuccessModal';

export default function RecentlyDeleted() {
    const [tasks, setTasks] = useState([]);
    const [loading, setLoading] = useState(true);
    const [toastMessage, setToastMessage] = useState('');
    const [toastType, setToastType] = useState('success');
    const [successMessage, setSuccessMessage] = useState('');
    const [showRestoreConfirm, setShowRestoreConfirm] = useState(false);
    const [showDeleteConfirm, setShowDeleteConfirm] = useState(false);
    const [selectedTask, setSelectedTask] = useState(null);

    useEffect(() => {
        fetchDeletedTasks();
    }, []);

    const fetchDeletedTasks = async () => {
        try {
            const response = await axios.get('/api/tasks/trashed/all');
            setTasks(response.data);
        } catch (error) {
            console.error('Error fetching deleted tasks:', error);
            setToastMessage('Failed to load deleted tasks');
            setToastType('error');
        } finally {
            setLoading(false);
        }
    };

    const handleRestoreClick = (task) => {
        setSelectedTask(task);
        setShowRestoreConfirm(true);
    };

    const handleConfirmRestore = async () => {
        try {
            await axios.post(`/api/tasks/${selectedTask.id}/restore`);
            setSuccessMessage('Task restored successfully');
            fetchDeletedTasks();
        } catch (error) {
            setToastMessage('Failed to restore task');
            setToastType('error');
        }
        setShowRestoreConfirm(false);
        setSelectedTask(null);
    };

    const handleDeleteClick = (task) => {
        setSelectedTask(task);
        setShowDeleteConfirm(true);
    };

    const handleConfirmDelete = async () => {
        try {
            await axios.delete(`/api/tasks/${selectedTask.id}/force`);
            setSuccessMessage('Task permanently deleted');
            fetchDeletedTasks();
        } catch (error) {
            setToastMessage('Failed to delete task');
            setToastType('error');
        }
        setShowDeleteConfirm(false);
        setSelectedTask(null);
    };

    const getCategoryBadge = (category) => {
        const colors = {
            frontend: 'badge-frontend',
            backend: 'badge-backend',
            server: 'badge-server',
        };
        return colors[category] || '';
    };

    const getStatusBadge = (status) => {
        const colors = {
            pending: 'badge-pending',
            in_progress: 'badge-in-progress',
            completed: 'badge-completed',
        };
        return colors[status] || '';
    };

    if (loading) {
        return <Layout><div className="loading">Loading...</div></Layout>;
    }

    return (
        <Layout>
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
                isOpen={showRestoreConfirm}
                title="Confirm Restore"
                message={`Are you sure you want to restore "${selectedTask?.title}"? This will move it back to your active tasks.`}
                onConfirm={handleConfirmRestore}
                onCancel={() => setShowRestoreConfirm(false)}
            />

            <ConfirmDialog
                isOpen={showDeleteConfirm}
                title="Confirm Permanent Delete"
                message={`Are you sure you want to permanently delete "${selectedTask?.title}"? This action cannot be undone and all attachments will be deleted.`}
                onConfirm={handleConfirmDelete}
                onCancel={() => setShowDeleteConfirm(false)}
            />

            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '2rem' }}>
                <h1>Recently Deleted Tasks</h1>
                <Link to="/customer/dashboard" className="btn btn-secondary">
                    Back to Dashboard
                </Link>
            </div>

            {tasks.length === 0 ? (
                <div className="card">
                    <p className="text-center text-muted">
                        No deleted tasks. Deleted tasks will appear here for 30 days.
                    </p>
                </div>
            ) : (
                <div className="card">
                    <div className="table-container">
                        <table className="table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Project</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Deleted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {tasks.map((task) => (
                                    <tr key={task.id}>
                                        <td>{task.title}</td>
                                        <td>{task.project?.name}</td>
                                        <td>
                                            <span className={`badge ${getCategoryBadge(task.category)}`}>
                                                {task.category.charAt(0).toUpperCase() + task.category.slice(1)}
                                            </span>
                                        </td>
                                        <td>
                                            <span className={`badge ${getStatusBadge(task.status)}`}>
                                                {task.status.replace('_', ' ').charAt(0).toUpperCase() + task.status.replace('_', ' ').slice(1)}
                                            </span>
                                        </td>
                                        <td>{new Date(task.deleted_at).toLocaleDateString()}</td>
                                        <td>
                                            <div className="table-actions">
                                                <button
                                                    onClick={() => handleRestoreClick(task)}
                                                    className="btn btn-sm btn-success"
                                                >
                                                    Restore
                                                </button>
                                                <button
                                                    onClick={() => handleDeleteClick(task)}
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
        </Layout>
    );
}
