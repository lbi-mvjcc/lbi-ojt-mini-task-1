import { useState, useEffect } from 'react';
import axios from 'axios';
import ConfirmDialog from '../ConfirmDialog';
import Toast from '../Toast';
import SuccessModal from '../SuccessModal';
import { useTheme } from '../../context/ThemeContext';

export default function ProjectsManagement() {
    const { isDark } = useTheme();
    const [projects, setProjects] = useState([]);
    const [filteredProjects, setFilteredProjects] = useState([]);
    const [users, setUsers] = useState([]);
    const [loading, setLoading] = useState(true);
    const [showForm, setShowForm] = useState(false);
    const [editingProject, setEditingProject] = useState(null);
    const [showConfirm, setShowConfirm] = useState(false);
    const [projectToDelete, setProjectToDelete] = useState(null);
    const [showMembersModal, setShowMembersModal] = useState(false);
    const [selectedProject, setSelectedProject] = useState(null);
    const [projectMembers, setProjectMembers] = useState([]);
    const [toastMessage, setToastMessage] = useState('');
    const [toastType, setToastType] = useState('success');
    const [successMessage, setSuccessMessage] = useState('');
    const [searchTerm, setSearchTerm] = useState('');
    const [viewMode, setViewMode] = useState('active');
    const [formData, setFormData] = useState({
        name: '',
        description: '',
        is_active: true,
    });

    useEffect(() => {
        setLoading(true);
        fetchProjects();
        fetchUsers();
    }, [viewMode]);

    useEffect(() => {
        // Filter projects based on search term
        const filtered = projects.filter(project => 
            project.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
            project.description.toLowerCase().includes(searchTerm.toLowerCase())
        );
        setFilteredProjects(filtered);
    }, [searchTerm, projects]);

    const fetchProjects = async () => {
        try {
            const endpoint = viewMode === 'deleted' ? '/api/admin/projects/trashed/all' : '/api/admin/projects';
            const response = await axios.get(endpoint);
            // Sort projects alphabetically by name
            const sortedProjects = response.data.sort((a, b) => a.name.localeCompare(b.name));
            setProjects(sortedProjects);
            setFilteredProjects(sortedProjects);
        } catch (error) {
            setToastMessage('Failed to load projects');
            setToastType('error');
            console.error('Error fetching projects:', error);
        } finally {
            setLoading(false);
        }
    };

    const fetchUsers = async () => {
        try {
            const response = await axios.get('/api/admin/users');
            setUsers(response.data.filter(u => ['frontend_developer', 'backend_developer', 'server_admin'].includes(u.role)));
        } catch (error) {
            console.error('Error fetching users:', error);
        }
    };

    const fetchProjectMembers = async (projectId) => {
        try {
            const response = await axios.get(`/api/admin/projects/${projectId}/members`);
            setProjectMembers(response.data);
        } catch (error) {
            console.error('Error fetching members:', error);
        }
    };

    const handleChange = (e) => {
        const value = e.target.type === 'checkbox' ? e.target.checked : e.target.value;
        setFormData({ ...formData, [e.target.name]: value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        try {
            if (editingProject) {
                await axios.put(`/api/admin/projects/${editingProject.id}`, formData);
                setSuccessMessage('Project updated successfully');
            } else {
                await axios.post('/api/admin/projects', formData);
                setSuccessMessage('Project created successfully');
            }
            fetchProjects();
            resetForm();
        } catch (err) {
            setToastMessage(err.response?.data?.message || 'Operation failed');
            setToastType('error');
        }
    };

    const handleEdit = (project) => {
        setEditingProject(project);
        setFormData({
            name: project.name,
            description: project.description,
            is_active: project.is_active,
        });
        setShowForm(true);
    };

    const handleDeleteClick = (project) => {
        setProjectToDelete(project);
        setShowConfirm(true);
    };

    const handleConfirmDelete = async () => {
        try {
            if (viewMode === 'deleted') {
                await axios.delete(`/api/admin/projects/${projectToDelete.id}/force`);
                setToastMessage('Project permanently deleted');
            } else {
                await axios.delete(`/api/admin/projects/${projectToDelete.id}`);
                setToastMessage('Project deleted successfully');
            }
            setToastType('success');
            fetchProjects();
        } catch (err) {
            setToastMessage(err.response?.data?.message || 'Failed to delete project');
            setToastType('error');
        }
        setShowConfirm(false);
        setProjectToDelete(null);
    };

    const handleRestore = async (project) => {
        try {
            await axios.post(`/api/admin/projects/${project.id}/restore`);
            setToastMessage('Project restored successfully');
            setToastType('success');
            fetchProjects();
        } catch (err) {
            setToastMessage(err.response?.data?.message || 'Failed to restore project');
            setToastType('error');
        }
    };

    const handleManageMembers = async (project) => {
        setSelectedProject(project);
        await fetchProjectMembers(project.id);
        setShowMembersModal(true);
    };

    const handleAddMember = async (userId, role) => {
        try {
            await axios.post(`/api/admin/projects/${selectedProject.id}/members`, {
                user_id: userId,
                role: role,
            });
            setToastMessage('Member added successfully');
            setToastType('success');
            fetchProjectMembers(selectedProject.id);
            fetchProjects();
        } catch (err) {
            setToastMessage(err.response?.data?.message || 'Failed to add member');
            setToastType('error');
        }
    };

    const handleRemoveMember = async (userId) => {
        try {
            await axios.delete(`/api/admin/projects/${selectedProject.id}/members/${userId}`);
            setToastMessage('Member removed successfully');
            setToastType('success');
            fetchProjectMembers(selectedProject.id);
            fetchProjects();
        } catch (err) {
            setToastMessage(err.response?.data?.message || 'Failed to remove member');
            setToastType('error');
        }
    };

    const resetForm = () => {
        setFormData({
            name: '',
            description: '',
            is_active: true,
        });
        setEditingProject(null);
        setShowForm(false);
    };

    if (loading) {
        return <div className="loading">Loading projects...</div>;
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
                message={`Are you sure you want to delete project "${projectToDelete?.name}"? This will also delete all associated tasks.`}
                onConfirm={handleConfirmDelete}
                onCancel={() => setShowConfirm(false)}
            />

            {showMembersModal && (
                <MembersModal
                    project={selectedProject}
                    members={projectMembers}
                    users={users}
                    onClose={() => setShowMembersModal(false)}
                    onAddMember={handleAddMember}
                    onRemoveMember={handleRemoveMember}
                />
            )}

            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '2rem', gap: '1rem', flexWrap: 'wrap' }}>
                <h2>Project Management</h2>
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
                            placeholder="Search by name or description..."
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
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
                            onClick={() => setShowForm(!showForm)}
                            className="btn btn-primary"
                            style={{ whiteSpace: 'nowrap' }}
                        >
                            {showForm ? 'Cancel' : '+ Add Project'}
                        </button>
                    )}
                </div>
            </div>

            {/* Edit/Create Project Modal */}
            {showForm && (
                <div className="modal-overlay" onClick={() => setShowForm(false)}>
                    <div className="modal-content" style={{ maxWidth: '600px', width: '95%' }} onClick={(e) => e.stopPropagation()}>
                        <div className="modal-header">
                            <h3>{editingProject ? 'Edit Project' : 'Create New Project'}</h3>
                        </div>
                        <div className="modal-body">
                            <form onSubmit={handleSubmit} id="projectForm">
                                <div className="form-group">
                                    <label>Project Name</label>
                                    <input
                                        type="text"
                                        name="name"
                                        value={formData.name}
                                        onChange={handleChange}
                                        required
                                    />
                                </div>

                                <div className="form-group">
                                    <label>Description</label>
                                    <textarea
                                        name="description"
                                        value={formData.description}
                                        onChange={handleChange}
                                        rows="4"
                                        required
                                    />
                                </div>

                                <div className="form-group">
                                    <label className="checkbox-label">
                                        <input
                                            type="checkbox"
                                            name="is_active"
                                            checked={formData.is_active}
                                            onChange={handleChange}
                                        />
                                        <span>Active Project</span>
                                    </label>
                                </div>
                            </form>
                        </div>
                        <div className="modal-footer">
                            <button type="button" onClick={resetForm} className="btn btn-secondary">
                                Cancel
                            </button>
                            <button type="submit" form="projectForm" className="btn btn-primary">
                                {editingProject ? 'Update Project' : 'Create Project'}
                            </button>
                        </div>
                    </div>
                </div>
            )}

            <div className="card">
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '1rem', flexWrap: 'wrap', gap: '1rem' }}>
                    <div style={{ color: '#64748b', fontSize: '0.95rem' }}>
                        Showing {filteredProjects.length} of {projects.length} project(s)
                    </div>
                </div>

                {filteredProjects.length === 0 ? (
                    <div style={{ padding: '3rem', textAlign: 'center', color: '#64748b' }}>
                        <p style={{ fontSize: '1.1rem', marginBottom: '0.5rem' }}>
                            {viewMode === 'deleted' ? 'No deleted projects found' : 'No projects found'}
                        </p>
                        <p style={{ fontSize: '0.9rem' }}>
                            {viewMode === 'deleted' ? 'Deleted projects will appear here' : searchTerm ? 'Try adjusting your search' : 'Click "+ Add Project" to create a new project'}
                        </p>
                    </div>
                ) : (
                <div className="table-container">
                    <table className="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Tasks</th>
                                <th>Members</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {filteredProjects.map(project => (
                                <tr key={project.id}>
                                    <td>{project.name}</td>
                                    <td>{project.description}</td>
                                    <td>
                                        <span className={`badge ${project.is_active ? 'badge-completed' : 'badge-pending'}`}>
                                            {project.is_active ? 'Active' : 'Inactive'}
                                        </span>
                                    </td>
                                    <td>{project.tasks_count || 0}</td>
                                    <td>{project.members_count || 0}</td>
                                    <td>
                                        {viewMode === 'deleted' ? (
                                            <div className="table-actions">
                                                <button
                                                    onClick={() => handleRestore(project)}
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
                                        ) : (
                                            <div className="table-actions">
                                                <button
                                                    onClick={() => handleManageMembers(project)}
                                                    className="btn btn-sm btn-success"
                                                >
                                                    Members
                                                </button>
                                                <button
                                                    onClick={() => handleEdit(project)}
                                                    className="btn btn-sm btn-secondary"
                                                >
                                                    Edit
                                                </button>
                                                <button
                                                    onClick={() => handleDeleteClick(project)}
                                                    className="btn btn-sm btn-danger"
                                                >
                                                    Delete
                                                </button>
                                            </div>
                                        )}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
                )}
            </div>
        </div>
    );
}

function MembersModal({ project, members, users, onClose, onAddMember, onRemoveMember }) {
    const { isDark } = useTheme();
    const [selectedUser, setSelectedUser] = useState('');
    const [selectedRole, setSelectedRole] = useState('');

    const maxMembers = 3;
    const isAtMaxCapacity = members.length >= maxMembers;

    const handleAdd = () => {
        if (selectedUser && selectedRole && !isAtMaxCapacity) {
            onAddMember(parseInt(selectedUser), selectedRole);
            setSelectedUser('');
            setSelectedRole('');
        }
    };

    const getRoleBadge = (role) => {
        const badges = {
            frontend_developer: 'badge-frontend',
            backend_developer: 'badge-backend',
            server_admin: 'badge-server',
        };
        return badges[role] || 'badge-pending';
    };

    const getRoleLabel = (role) => {
        const labels = {
            frontend_developer: 'Frontend Dev',
            backend_developer: 'Backend Dev',
            server_admin: 'Server Admin',
        };
        return labels[role] || role;
    };

    const availableUsers = users.filter(u => !members.find(m => m.id === u.id));
    const takenRoles = members.map(m => m.pivot.role);
    const availableRoles = [
        { value: 'frontend_developer', label: 'Frontend Developer' },
        { value: 'backend_developer', label: 'Backend Developer' },
        { value: 'server_admin', label: 'Server Admin' }
    ].filter(role => !takenRoles.includes(role.value));

    return (
        <div className="modal-overlay">
            <div className="modal-content" style={{ maxWidth: '700px', width: '95%' }}>
                <div className="modal-header">
                    <h3>Manage Members - {project.name}</h3>
                    <div style={{ 
                        fontSize: '0.9rem', 
                        color: isAtMaxCapacity ? '#dc2626' : '#0891b2',
                        fontWeight: '600',
                        marginTop: '0.5rem'
                    }}>
                        Members: {members.length}/{maxMembers}
                    </div>
                </div>
                <div className="modal-body">
                    <div style={{ marginBottom: '2rem' }}>
                        <h4 style={{ marginBottom: '1rem' }}>Add New Member</h4>
                        {isAtMaxCapacity ? (
                            <div style={{
                                padding: '1rem',
                                background: '#fef2f2',
                                border: '1px solid #fecaca',
                                borderRadius: '8px',
                                color: '#991b1b',
                                marginBottom: '1rem'
                            }}>
                                This project already has the maximum of 3 developers (1 Frontend, 1 Backend, 1 Server Admin).
                            </div>
                        ) : (
                            <div style={{ display: 'flex', gap: '1rem', flexWrap: 'wrap' }}>
                                <select
                                    value={selectedUser}
                                    onChange={(e) => setSelectedUser(e.target.value)}
                                    style={{ flex: 1, minWidth: '200px' }}
                                    disabled={isAtMaxCapacity}
                                >
                                    <option value="">Select User</option>
                                    {availableUsers.map(user => (
                                        <option key={user.id} value={user.id}>
                                            {user.name} ({user.email})
                                        </option>
                                    ))}
                                </select>
                                <select
                                    value={selectedRole}
                                    onChange={(e) => setSelectedRole(e.target.value)}
                                    style={{ flex: 1, minWidth: '150px' }}
                                    disabled={isAtMaxCapacity}
                                >
                                    <option value="">Select Role</option>
                                    {availableRoles.map(role => (
                                        <option key={role.value} value={role.value}>
                                            {role.label}
                                        </option>
                                    ))}
                                </select>
                                <button 
                                    onClick={handleAdd} 
                                    className="btn btn-sm btn-primary"
                                    disabled={isAtMaxCapacity || !selectedUser || !selectedRole}
                                >
                                    Add
                                </button>
                            </div>
                        )}
                    </div>

                    <h4 style={{ marginBottom: '1rem' }}>Current Members</h4>
                    {members.length === 0 ? (
                        <p style={{ color: isDark ? '#ffffff' : '#64748b' }}>No members assigned yet</p>
                    ) : (
                        <div style={{ display: 'flex', flexDirection: 'column', gap: '0.75rem' }}>
                            {members.map(member => (
                                <div
                                    key={member.id}
                                    style={{
                                        display: 'flex',
                                        justifyContent: 'space-between',
                                        alignItems: 'center',
                                        padding: '1rem',
                                        background: isDark ? '#3d5266' : '#f8fafc',
                                        borderRadius: '8px',
                                        border: isDark ? '1px solid #4a6278' : '1px solid #e2e8f0',
                                    }}
                                >
                                    <div>
                                        <strong style={{ color: isDark ? '#ffffff' : '#0f172a' }}>{member.name}</strong>
                                        <br />
                                        <small style={{ color: isDark ? '#ffffff' : '#64748b' }}>{member.email}</small>
                                    </div>
                                    <div style={{ display: 'flex', gap: '0.5rem', alignItems: 'center' }}>
                                        <span className={`badge ${getRoleBadge(member.pivot.role)}`}>
                                            {getRoleLabel(member.pivot.role)}
                                        </span>
                                        <button
                                            onClick={() => onRemoveMember(member.id)}
                                            className="btn btn-sm btn-danger"
                                        >
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>
                <div className="modal-footer">
                    <button onClick={onClose} className="btn btn-secondary">
                        Close
                    </button>
                </div>
            </div>
        </div>
    );
}
