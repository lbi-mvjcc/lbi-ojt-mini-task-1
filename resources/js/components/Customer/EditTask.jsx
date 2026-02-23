import { useState, useEffect } from 'react';
import { useNavigate, useParams, Link } from 'react-router-dom';
import axios from 'axios';
import Layout from '../Layout';
import ConfirmDialog from '../ConfirmDialog';
import SuccessModal from '../SuccessModal';

export default function EditTask() {
    const { id } = useParams();
    const navigate = useNavigate();
    const [formData, setFormData] = useState({
        title: '',
        description: '',
        link: '',
        category: '',
        project_id: '',
    });
    const [projects, setProjects] = useState([]);
    const [existingAttachments, setExistingAttachments] = useState([]);
    const [newAttachments, setNewAttachments] = useState([]);
    const [attachmentsToRemove, setAttachmentsToRemove] = useState([]);
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');
    const [successMessage, setSuccessMessage] = useState('');
    const [loading, setLoading] = useState(true);
    const [updating, setUpdating] = useState(false);
    const [showConfirm, setShowConfirm] = useState(false);

    useEffect(() => {
        fetchCustomerProjects();
        fetchTask();
    }, [id]);

    const fetchCustomerProjects = async () => {
        try {
            const response = await axios.get('/api/projects');
            setProjects(response.data);
        } catch (error) {
            console.error('Error fetching projects:', error);
        }
    };

    const fetchTask = async () => {
        try {
            const response = await axios.get(`/api/tasks/${id}`);
            const task = response.data;
            setFormData({
                title: task.title,
                description: task.description,
                link: task.link || '',
                category: task.category,
                project_id: task.project_id,
            });
            setExistingAttachments(task.attachments || []);
        } catch (error) {
            console.error('Error fetching task:', error);
            setError('Failed to load task details.');
        } finally {
            setLoading(false);
        }
    };

    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const handleFileChange = (e) => {
        const files = Array.from(e.target.files);
        setNewAttachments([...newAttachments, ...files]);
    };

    const removeNewFile = (index) => {
        setNewAttachments(newAttachments.filter((_, i) => i !== index));
    };

    const removeExistingAttachment = (attachmentId) => {
        setAttachmentsToRemove([...attachmentsToRemove, attachmentId]);
        setExistingAttachments(existingAttachments.filter(a => a.id !== attachmentId));
    };

    const formatFileSize = (bytes) => {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        setError('');
        setShowConfirm(true);
    };

    const handleConfirmUpdate = async () => {
        setShowConfirm(false);
        setUpdating(true);

        try {
            const formDataToSend = new FormData();
            formDataToSend.append('title', formData.title);
            formDataToSend.append('description', formData.description);
            formDataToSend.append('link', formData.link);
            formDataToSend.append('category', formData.category);
            formDataToSend.append('project_id', formData.project_id);
            formDataToSend.append('_method', 'PUT');
            
            // Append attachments to remove
            attachmentsToRemove.forEach((id) => {
                formDataToSend.append('remove_attachments[]', id);
            });
            
            // Append new files
            newAttachments.forEach((file) => {
                formDataToSend.append('attachments[]', file);
            });

            await axios.post(`/api/tasks/${id}`, formDataToSend, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            });
            
            setSuccessMessage('Task updated successfully!');
            setTimeout(() => {
                navigate('/customer/dashboard');
            }, 2000);
        } catch (err) {
            setError(err.response?.data?.message || 'Failed to update task. Please try again.');
        } finally {
            setUpdating(false);
        }
    };

    const handleCancelUpdate = () => {
        setShowConfirm(false);
    };

    const getProjectName = () => {
        const project = projects.find(p => p.id === parseInt(formData.project_id));
        return project ? project.name : '';
    };

    const getCategoryLabel = () => {
        const labels = {
            frontend: 'Frontend',
            backend: 'Backend',
            server: 'Server'
        };
        return labels[formData.category] || '';
    };

    if (loading) {
        return <Layout><div className="loading">Loading...</div></Layout>;
    }

    return (
        <Layout>
            <SuccessModal 
                message={successMessage}
                onClose={() => setSuccessMessage('')}
            />
            
            <ConfirmDialog
                isOpen={showConfirm}
                title="Confirm Task Update"
                message={`Are you sure you want to update this task? Title: ${formData.title}, Project: ${getProjectName()}, Category: ${getCategoryLabel()}. The task will be re-assigned if category changed.`}
                onConfirm={handleConfirmUpdate}
                onCancel={handleCancelUpdate}
            />
            
            <div className="form-container">
                <h1>Edit Task</h1>

                {error && <div className="alert alert-error">{error}</div>}
                {success && <div className="alert alert-success">{success}</div>}

                <div className="card">
                    <form onSubmit={handleSubmit}>
                        <div className="form-group">
                            <label>Project</label>
                            <select
                                name="project_id"
                                value={formData.project_id}
                                onChange={handleChange}
                                required
                                disabled={updating}
                            >
                                <option value="">Select Project</option>
                                {projects.map((project) => (
                                    <option key={project.id} value={project.id}>
                                        {project.name}
                                    </option>
                                ))}
                            </select>
                        </div>

                        <div className="form-group">
                            <label>Task Title</label>
                            <input
                                type="text"
                                name="title"
                                value={formData.title}
                                onChange={handleChange}
                                placeholder="Enter a descriptive task title"
                                required
                                disabled={updating}
                            />
                        </div>

                        <div className="form-group">
                            <label>Description</label>
                            <textarea
                                name="description"
                                value={formData.description}
                                onChange={handleChange}
                                rows="5"
                                placeholder="Provide detailed task description..."
                                required
                                disabled={updating}
                            />
                        </div>

                        <div className="form-group">
                            <label>Link (Optional)</label>
                            <input
                                type="url"
                                name="link"
                                value={formData.link}
                                onChange={handleChange}
                                placeholder="https://example.com"
                                disabled={updating}
                            />
                            <small className="form-text">
                                Add a relevant link (e.g., design mockup, documentation, reference)
                            </small>
                        </div>

                        <div className="form-group">
                            <label>Category</label>
                            <select
                                name="category"
                                value={formData.category}
                                onChange={handleChange}
                                required
                                disabled={updating}
                            >
                                <option value="">Select Category</option>
                                <option value="frontend">Frontend </option>
                                <option value="backend">Backend </option>
                                <option value="server">Server</option>
                            </select>
                           
                        </div>

                        {/* Existing Attachments */}
                        {existingAttachments.length > 0 && (
                            <div className="form-group">
                                <label>Current Attachments</label>
                                <div className="file-list">
                                    {existingAttachments.map((attachment) => (
                                        <div key={attachment.id} className="file-item">
                                            <div className="file-info">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                                    <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                                    <polyline points="13 2 13 9 20 9"></polyline>
                                                </svg>
                                                <div className="file-details">
                                                    <span className="file-name">{attachment.file_name}</span>
                                                    <span className="file-size">{formatFileSize(attachment.file_size)}</span>
                                                </div>
                                            </div>
                                            <button
                                                type="button"
                                                onClick={() => removeExistingAttachment(attachment.id)}
                                                className="file-remove"
                                                disabled={updating}
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                                </svg>
                                            </button>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}

                        {/* New Attachments */}
                        <div className="form-group">
                            <label>Add New Attachments (Optional)</label>
                            <div className="file-upload-wrapper">
                                <input
                                    type="file"
                                    id="file-upload"
                                    multiple
                                    onChange={handleFileChange}
                                    disabled={updating}
                                    accept="image/*,video/*,.pdf,.doc,.docx,.txt,.zip"
                                    className="file-input"
                                />
                                <label htmlFor="file-upload" className="file-upload-label">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="17 8 12 3 7 8"></polyline>
                                        <line x1="12" y1="3" x2="12" y2="15"></line>
                                    </svg>
                                    <span>Choose Files</span>
                                </label>
                            </div>
                            <small className="form-text">
                                Upload images, videos, or documents (Max 10MB per file)
                            </small>
                            
                            {newAttachments.length > 0 && (
                                <div className="file-list">
                                    {newAttachments.map((file, index) => (
                                        <div key={index} className="file-item">
                                            <div className="file-info">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                                    <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                                    <polyline points="13 2 13 9 20 9"></polyline>
                                                </svg>
                                                <div className="file-details">
                                                    <span className="file-name">{file.name}</span>
                                                    <span className="file-size">{formatFileSize(file.size)}</span>
                                                </div>
                                            </div>
                                            <button
                                                type="button"
                                                onClick={() => removeNewFile(index)}
                                                className="file-remove"
                                                disabled={updating}
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                                </svg>
                                            </button>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>

                        <div className="form-actions">
                            <button type="submit" className="btn btn-primary" disabled={updating}>
                                {updating ? 'Updating...' : 'Update Task'}
                            </button>
                            <Link to="/customer/dashboard" className="btn btn-secondary">
                                Cancel
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </Layout>
    );
}
