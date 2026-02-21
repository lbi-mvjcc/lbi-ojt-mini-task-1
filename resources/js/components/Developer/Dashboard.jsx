import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import axios from 'axios';
import Layout from '../Layout';

// Developer Dashboard - No customer information displayed (v2.0)
export default function DeveloperDashboard() {
    const [tasks, setTasks] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [currentPage, setCurrentPage] = useState(1);
    const [tasksPerPage] = useState(10);
    const [statusFilter, setStatusFilter] = useState('all');

    useEffect(() => {
        fetchTasks();
    }, []);

    const fetchTasks = async () => {
        try {
            const response = await axios.get('/api/tasks');
            // Sort tasks alphabetically by title
            const sortedTasks = response.data.sort((a, b) => a.title.localeCompare(b.title));
            setTasks(sortedTasks);
            setError('');
        } catch (error) {
            console.error('Error fetching tasks:', error);
            setError('Failed to load tasks. Please refresh the page.');
        } finally {
            setLoading(false);
        }
    };

    // Filter tasks by status
    const filteredTasks = statusFilter === 'all' 
        ? tasks 
        : tasks.filter(task => task.status === statusFilter);

    // Task counts by status
    const pendingCount = tasks.filter(t => t.status === 'pending').length;
    const inProgressCount = tasks.filter(t => t.status === 'in_progress').length;
    const completedCount = tasks.filter(t => t.status === 'completed').length;

    // Pagination logic
    const indexOfLastTask = currentPage * tasksPerPage;
    const indexOfFirstTask = indexOfLastTask - tasksPerPage;
    const currentTasks = filteredTasks.slice(indexOfFirstTask, indexOfLastTask);
    const totalPages = Math.ceil(filteredTasks.length / tasksPerPage);

    // Reset to page 1 when filter changes
    useEffect(() => {
        setCurrentPage(1);
    }, [statusFilter]);

    const nextPage = () => {
        if (currentPage < totalPages) setCurrentPage(currentPage + 1);
    };
    const prevPage = () => {
        if (currentPage > 1) setCurrentPage(currentPage - 1);
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
            <h1>My Assigned Tasks</h1>

            {error && <div className="alert alert-error">{error}</div>}
            
            {tasks.length > 0 && tasks[0].project && (
                <div className="info-box" style={{ marginBottom: '2rem' }}>
                    <strong>Your Assignment:</strong> You are assigned to <strong>{tasks[0].project.name}</strong>. 
                </div>
            )}

            {/* Task Status Filter */}
            {tasks.length > 0 && (
                <div className="status-filter-tabs">
                    <button
                        onClick={() => setStatusFilter('all')}
                        className={`status-tab ${statusFilter === 'all' ? 'active' : ''}`}
                    >
                        All Tasks ({tasks.length})
                    </button>
                    <button
                        onClick={() => setStatusFilter('pending')}
                        className={`status-tab ${statusFilter === 'pending' ? 'active' : ''}`}
                    >
                        Pending ({pendingCount})
                    </button>
                    <button
                        onClick={() => setStatusFilter('in_progress')}
                        className={`status-tab ${statusFilter === 'in_progress' ? 'active' : ''}`}
                    >
                        In Progress ({inProgressCount})
                    </button>
                    <button
                        onClick={() => setStatusFilter('completed')}
                        className={`status-tab ${statusFilter === 'completed' ? 'active' : ''}`}
                    >
                        Completed ({completedCount})
                    </button>
                </div>
            )}

            {tasks.length === 0 ? (
                <div className="card">
                    <p className="text-center text-muted">
                        No tasks assigned yet. Check back later!
                    </p>
                </div>
            ) : filteredTasks.length === 0 ? (
                <div className="card">
                    <p className="text-center text-muted">
                        No tasks found with status: {statusFilter.replace('_', ' ')}
                    </p>
                </div>
            ) : (
                <>
                    <div className="card table-container">
                        <table className="table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Project</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Attachments</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {currentTasks.map((task) => (
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
                                        <td>
                                            {task.attachments && task.attachments.length > 0 ? (
                                                <div style={{ display: 'flex', flexDirection: 'column', gap: '0.25rem' }}>
                                                    {task.attachments.map((attachment, index) => (
                                                        <a 
                                                            key={index}
                                                            href={`/storage/${attachment.file_path}`}
                                                            target="_blank"
                                                            rel="noopener noreferrer"
                                                            style={{ 
                                                                color: '#0891b2',
                                                                textDecoration: 'none',
                                                                fontSize: '0.875rem'
                                                            }}
                                                        >
                                                            📎 {attachment.file_name}
                                                        </a>
                                                    ))}
                                                </div>
                                            ) : (
                                                <span style={{ color: '#94a3b8' }}>No attachments</span>
                                            )}
                                        </td>
                                        <td>{new Date(task.created_at).toLocaleDateString()}</td>
                                        <td>
                                            <Link to={`/tasks/${task.id}`} className="btn btn-sm">
                                                View
                                            </Link>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {/* Pagination */}
                    {totalPages > 1 && (
                        <div className="pagination">
                            <button 
                                onClick={prevPage} 
                                disabled={currentPage === 1}
                                className="btn btn-secondary btn-sm"
                            >
                                Previous
                            </button>
                            <span className="pagination-info">
                                Page {currentPage} of {totalPages}
                            </span>
                            <button 
                                onClick={nextPage} 
                                disabled={currentPage === totalPages}
                                className="btn btn-secondary btn-sm"
                            >
                                Next
                            </button>
                        </div>
                    )}

                    {/* Mobile Card View */}
                    <div className="mobile-card-view">
                        {currentTasks.map((task) => (
                            <div key={task.id} className="task-card">
                                <div className="task-card-header">
                                    <div className="task-card-title">{task.title}</div>
                                    <Link to={`/tasks/${task.id}`} className="btn btn-sm btn-primary">
                                        View
                                    </Link>
                                </div>
                                <div className="task-card-body">
                                    <div className="task-card-row">
                                        <span className="task-card-label">Project:</span>
                                        <span className="task-card-value">{task.project?.name}</span>
                                    </div>
                                    <div className="task-card-row">
                                        <span className="task-card-label">Category:</span>
                                        <span className={`badge ${getCategoryBadge(task.category)}`}>
                                            {task.category.charAt(0).toUpperCase() + task.category.slice(1)}
                                        </span>
                                    </div>
                                    <div className="task-card-row">
                                        <span className="task-card-label">Status:</span>
                                        <span className={`badge ${getStatusBadge(task.status)}`}>
                                            {task.status.replace('_', ' ').charAt(0).toUpperCase() + task.status.replace('_', ' ').slice(1)}
                                        </span>
                                    </div>
                                    <div className="task-card-row">
                                        <span className="task-card-label">Created:</span>
                                        <span className="task-card-value">{new Date(task.created_at).toLocaleDateString()}</span>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </>
            )}
        </Layout>
    );
}
