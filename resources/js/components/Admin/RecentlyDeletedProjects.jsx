import { useState, useEffect } from 'react';
import axios from 'axios';
import ConfirmDialog from '../ConfirmDialog';
import Toast from '../Toast';

export default function RecentlyDeletedProjects() {
    const [projects, setProjects] = useState([]);
    const [loading, setLoading] = useState(true);
    const [toastMessage, setToastMessage] = useState('');
    const [toastType, setToastType] = useState('success');
    const [showRestoreConfirm, setShowRestoreConfirm] = useState(false);
    const [showDeleteConfirm, setShowDeleteConfirm] = useState(false);
    const [selectedProject, setSelectedProject] = useState(null);

    useEffect(() => {
        fetchDeletedProjects();
    }, []);

    const fetchDeletedProjects = async () => {
        try {
            const response = await axios.get('/api/admin/projects/trashed/all');
            setProjects(response.data);
        } catch (error) {
            console.error('Error fetching deleted projects:', error);
            setToastMessage('Failed to load deleted projects');
            setToastType('error');
        } finally {
            setLoading(false);
        }
    };

    const handleRestoreClick = (project) => {
        setSelectedProject(project);
        setShowRestoreConfirm(true);
    };

    const handleConfirmRestore = async () => {
        try {
            await axios.post(`/api/admin/projects/${selectedProject.id}/restore`);
            setToastMessage('Project restored successfully');
            setToastType('success');
            fetchDeletedProjects();
        } catch (error) {
            setToastMessage('Failed to restore project');
            setToastType('error');
        }
        setShowRestoreConfirm(false);
        setSelectedProject(null);
    };

    const handleDeleteClick = (project) => {
        setSelectedProject(project);
        setShowDeleteConfirm(true);
    };

    const handleConfirmDelete = async () => {
        try {
            await axios.delete(`/api/admin/projects/${selectedProject.id}/force`);
            setToastMessage('Project permanently deleted');
            setToastType('success');
            fetchDeletedProjects();
        } catch (error) {
            setToastMessage('Failed to delete project');
            setToastType('error');
        }
        setShowDeleteConfirm(false);
        setSelectedProject(null);
    };

    if (loading) {
        return <div className="loading">Loading deleted projects...</div>;
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
                message={`Are you sure you want to restore project "${selectedProject?.name}"? This will restore the project and all its data.`}
                onConfirm={handleConfirmRestore}
                onCancel={() => setShowRestoreConfirm(false)}
            />

            <ConfirmDialog
                isOpen={showDeleteConfirm}
                title="Confirm Permanent Delete"
                message={`Are you sure you want to permanently delete project "${selectedProject?.name}"? This action cannot be undone and will delete all associated tasks.`}
                onConfirm={handleConfirmDelete}
                onCancel={() => setShowDeleteConfirm(false)}
            />

            <h2 style={{ marginBottom: '2rem' }}>Recently Deleted Projects</h2>

            {projects.length === 0 ? (
                <div className="card">
                    <p className="text-center text-muted">
                        No deleted projects. Deleted projects will appear here.
                    </p>
                </div>
            ) : (
                <div className="card">
                    <div className="table-container">
                        <table className="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Members</th>
                                    <th>Deleted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {projects.map((project) => (
                                    <tr key={project.id}>
                                        <td>{project.name}</td>
                                        <td style={{ maxWidth: '300px', overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>
                                            {project.description}
                                        </td>
                                        <td>
                                            <span className={`badge ${project.is_active ? 'badge-completed' : 'badge-pending'}`}>
                                                {project.is_active ? 'Active' : 'Inactive'}
                                            </span>
                                        </td>
                                        <td>{project.members_count || 0}</td>
                                        <td>{new Date(project.deleted_at).toLocaleDateString()}</td>
                                        <td>
                                            <div className="table-actions">
                                                <button
                                                    onClick={() => handleRestoreClick(project)}
                                                    className="btn btn-sm btn-success"
                                                >
                                                    Restore
                                                </button>
                                                <button
                                                    onClick={() => handleDeleteClick(project)}
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
