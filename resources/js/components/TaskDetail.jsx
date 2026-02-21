import { useState, useEffect } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import axios from 'axios';
import Layout from './Layout';
import { useAuth } from '../context/AuthContext';
import ConfirmDialog from './ConfirmDialog';
import SuccessModal from './SuccessModal';

export default function TaskDetail() {
    const { id } = useParams();
    const { user } = useAuth();
    const navigate = useNavigate();
    const [task, setTask] = useState(null);
    const [loading, setLoading] = useState(true);
    const [updating, setUpdating] = useState(false);
    const [newStatus, setNewStatus] = useState('');
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');
    const [successMessage, setSuccessMessage] = useState('');
    const [showConfirm, setShowConfirm] = useState(false);
    const [showDeleteConfirm, setShowDeleteConfirm] = useState(false);
    const [deleting, setDeleting] = useState(false);

    useEffect(() => {
        fetchTask();
    }, [id]);

    const fetchTask = async () => {
        try {
            const response = await axios.get(`/api/tasks/${id}`);
            setTask(response.data);
            setNewStatus(response.data.status);
            setError('');
        } catch (error) {
            console.error('Error fetching task:', error);
            setError('Failed to load task details. Please try again.');
        } finally {
            setLoading(false);
        }
    };

    const handleStatusUpdate = async (e) => {
        e.preventDefault();
        setError('');
        setSuccess('');
        
        // Show confirmation dialog
        setShowConfirm(true);
    };
    
    const handleConfirmUpdate = async () => {
        setShowConfirm(false);
        setUpdating(true);

        try {
            await axios.patch(`/api/tasks/${id}/status`, { status: newStatus });
            await fetchTask();
            setSuccessMessage('Task status updated successfully!');
        } catch (error) {
            setError('Failed to update status. Please try again.');
        } finally {
            setUpdating(false);
        }
    };
    
    const handleCancelUpdate = () => {
        setShowConfirm(false);
    };
    
    const getStatusLabel = (status) => {
        const labels = {
            pending: 'Pending',
            in_progress: 'In Progress',
            completed: 'Completed'
        };
        return labels[status] || status;
    };

    const handleDeleteClick = () => {
        setShowDeleteConfirm(true);
    };

    const handleConfirmDelete = async () => {
        setShowDeleteConfirm(false);
        setDeleting(true);
        setError('');

        try {
            await axios.delete(`/api/tasks/${id}`);
            setSuccessMessage('Task deleted successfully!');
            setTimeout(() => {
                navigate('/customer/dashboard');
            }, 2000);
        } catch (error) {
            setError('Failed to delete task. Please try again.');
            setDeleting(false);
        }
    };

    const handleCancelDelete = () => {
        setShowDeleteConfirm(false);
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

    if (!task) {
        return <Layout><div className="card">Task not found</div></Layout>;
    }

    const backUrl = user.role === 'customer' ? '/customer/dashboard' : '/developer/dashboard';

    return (
        <Layout>
            <SuccessModal 
                message={successMessage}
                onClose={() => setSuccessMessage('')}
            />
            
            <ConfirmDialog
                isOpen={showConfirm}
                title="Confirm Status Update"
                message={`Are you sure you want to update the task status? Task: ${task?.title} - Current Status: ${getStatusLabel(task?.status)} - New Status: ${getStatusLabel(newStatus)}`}
                onConfirm={handleConfirmUpdate}
                onCancel={handleCancelUpdate}
            />

            <ConfirmDialog
                isOpen={showDeleteConfirm}
                title="Confirm Task Deletion"
                message={`Are you sure you want to delete this task? Task: ${task?.title}. This action cannot be undone. All attachments will also be deleted.`}
                onConfirm={handleConfirmDelete}
                onCancel={handleCancelDelete}
            />
            
            <div className="task-detail">
                <div className="mb-2">
                    <Link to={backUrl} className="btn btn-secondary">
                        ← Back to Dashboard
                    </Link>
                </div>

                {error && <div className="alert alert-error">{error}</div>}
                {success && <div className="alert alert-success">{success}</div>}

                <div className="card">
                    <div className="task-header-actions">
                        <h1>{task.title}</h1>
                        {user.role === 'customer' && (
                            <div className="task-actions">
                                <Link to={`/customer/tasks/${task.id}/edit`} className="btn btn-secondary">
                                    Edit Task
                                </Link>
                                <button 
                                    onClick={handleDeleteClick} 
                                    className="btn btn-danger"
                                    disabled={deleting}
                                >
                                    {deleting ? 'Deleting...' : 'Delete Task'}
                                </button>
                            </div>
                        )}
                    </div>

                    <div className="badges">
                        <span className={`badge ${getCategoryBadge(task.category)}`}>
                            {task.category.charAt(0).toUpperCase() + task.category.slice(1)}
                        </span>
                        <span className={`badge ${getStatusBadge(task.status)}`}>
                            {task.status.replace('_', ' ').charAt(0).toUpperCase() + task.status.replace('_', ' ').slice(1)}
                        </span>
                    </div>

                    <div className="task-info">
                        <div className="info-item">
                            <strong>Project:</strong> {task.project?.name}
                        </div>

                        {task.link && (
                            <div className="info-item">
                                <strong>Link:</strong> 
                                <a href={task.link} target="_blank" rel="noopener noreferrer" style={{ color: '#06b6d4', textDecoration: 'underline' }}>
                                    {task.link}
                                </a>
                            </div>
                        )}

                        {user.role !== 'customer' && task.assigned_developer && (
                            <div className="info-item">
                                <strong>Assigned To:</strong> {task.assigned_developer.name}
                            </div>
                        )}

                        <div className="info-item">
                            <strong>Created:</strong> {new Date(task.created_at).toLocaleString()}
                        </div>

                        <div className="info-item">
                            <strong>Last Updated:</strong> {new Date(task.updated_at).toLocaleString()}
                        </div>
                    </div>

                    <div className="task-description">
                        <strong>Description:</strong>
                        <p>{task.description}</p>
                    </div>

                    {task.attachments && task.attachments.length > 0 && (
                        <div className="attachments-section">
                            <strong>Attachments:</strong>
                            <div className="attachments-grid">
                                {task.attachments.map((attachment) => (
                                    <a
                                        key={attachment.id}
                                        href={`/storage/${attachment.file_path}`}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="attachment-card"
                                    >
                                        {attachment.file_type.startsWith('image/') ? (
                                            <img
                                                src={`/storage/${attachment.file_path}`}
                                                alt={attachment.file_name}
                                                className="attachment-preview"
                                            />
                                        ) : attachment.file_type.startsWith('video/') ? (
                                            <video
                                                src={`/storage/${attachment.file_path}`}
                                                className="attachment-preview"
                                                controls
                                            />
                                        ) : (
                                            <div className="attachment-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                                    <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                                    <polyline points="13 2 13 9 20 9"></polyline>
                                                </svg>
                                            </div>
                                        )}
                                        <div className="attachment-info">
                                            <span className="attachment-name">{attachment.file_name}</span>
                                            <span className="attachment-size">
                                                {(attachment.file_size / 1024).toFixed(2)} KB
                                            </span>
                                        </div>
                                    </a>
                                ))}
                            </div>
                        </div>
                    )}

                    {user.role !== 'customer' && (
                        <div className="status-update">
                            <strong>Update Status:</strong>
                            <form onSubmit={handleStatusUpdate}>
                                <div className="status-form">
                                    <select
                                        value={newStatus}
                                        onChange={(e) => setNewStatus(e.target.value)}
                                        disabled={updating}
                                    >
                                        <option value="pending">Pending</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                    <button type="submit" className="btn btn-success" disabled={updating}>
                                        {updating ? 'Updating...' : 'Update Status'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    )}
                </div>
            </div>
        </Layout>
    );
}
