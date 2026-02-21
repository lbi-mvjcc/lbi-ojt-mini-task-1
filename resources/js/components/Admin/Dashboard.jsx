import { useState, useEffect } from 'react';
import axios from 'axios';
import { useSearchParams } from 'react-router-dom';
import Layout from '../Layout';
import UsersManagement from './UsersManagement';
import ProjectsManagement from './ProjectsManagement';

export default function AdminDashboard() {
    const [searchParams] = useSearchParams();
    const [stats, setStats] = useState(null);
    const [activeTab, setActiveTab] = useState(searchParams.get('tab') || 'overview');
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetchStats();
    }, []);

    useEffect(() => {
        // Check if we should open the users tab with edit profile
        const tab = searchParams.get('tab');
        if (tab) {
            setActiveTab(tab);
        }
    }, [searchParams]);

    const fetchStats = async () => {
        try {
            const response = await axios.get('/api/admin/stats');
            setStats(response.data);
        } catch (error) {
            console.error('Error fetching stats:', error);
        } finally {
            setLoading(false);
        }
    };

    if (loading) {
        return <Layout><div className="loading">Loading...</div></Layout>;
    }

    const navItems = [
        {
            label: 'Users',
            active: activeTab === 'users',
            onClick: () => setActiveTab('users')
        },
        {
            label: 'Projects',
            active: activeTab === 'projects',
            onClick: () => setActiveTab('projects')
        },
        {
            label: 'Tasks',
            active: activeTab === 'tasks',
            onClick: () => setActiveTab('tasks')
        }
    ];

    return (
        <Layout navItems={navItems} onTitleClick={() => setActiveTab('overview')}>
            <div className="dashboard-header">
                <h1>Admin Dashboard</h1>
            </div>

            {/* Statistics Cards */}
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(250px, 1fr))', gap: '1.5rem', marginBottom: '3rem' }}>
                <div className="card stat-card stat-card-cyan" style={{ textAlign: 'center' }}>
                    <h3 style={{ color: '#0e7490', fontSize: '0.875rem', marginBottom: '0.5rem', fontWeight: '600' }}>Total Users</h3>
                    <p style={{ fontSize: '2.5rem', fontWeight: '700', color: '#0891b2', margin: 0 }}>{stats?.total_users || 0}</p>
                </div>
                <div className="card stat-card stat-card-teal" style={{ textAlign: 'center' }}>
                    <h3 style={{ color: '#0f766e', fontSize: '0.875rem', marginBottom: '0.5rem', fontWeight: '600' }}>Total Projects</h3>
                    <p style={{ fontSize: '2.5rem', fontWeight: '700', color: '#0d9488', margin: 0 }}>{stats?.total_projects || 0}</p>
                </div>
                <div className="card stat-card stat-card-blue" style={{ textAlign: 'center' }}>
                    <h3 style={{ color: '#0369a1', fontSize: '0.875rem', marginBottom: '0.5rem', fontWeight: '600' }}>Total Tasks</h3>
                    <p style={{ fontSize: '2.5rem', fontWeight: '700', color: '#0284c7', margin: 0 }}>{stats?.total_tasks || 0}</p>
                </div>
                <div className="card stat-card stat-card-purple" style={{ textAlign: 'center' }}>
                    <h3 style={{ color: '#7e22ce', fontSize: '0.875rem', marginBottom: '0.5rem', fontWeight: '600' }}>Developers</h3>
                    <p style={{ fontSize: '2.5rem', fontWeight: '700', color: '#9333ea', margin: 0 }}>{stats?.developers || 0}</p>
                </div>
            </div>

            {/* Tab Content */}
            {activeTab === 'overview' && <OverviewTab stats={stats} />}
            {activeTab === 'users' && <UsersManagement />}
            {activeTab === 'projects' && <ProjectsManagement />}
            {activeTab === 'tasks' && <TasksTab />}
        </Layout>
    );
}

// Overview Tab Component
function OverviewTab({ stats }) {
    return (
        <div className="card">
            <h2 style={{ marginBottom: '2rem' }}>System Overview</h2>
            <div style={{ display: 'grid', gap: '1.5rem' }}>
                <div className="stat-row">
                    <span style={{ fontWeight: '600' }}>Customers:</span>
                    <span style={{ color: '#0891b2', fontWeight: '700' }}>{stats?.customers || 0}</span>
                </div>
                <div className="stat-row">
                    <span style={{ fontWeight: '600' }}>Developers:</span>
                    <span style={{ color: '#0d9488', fontWeight: '700' }}>{stats?.developers || 0}</span>
                </div>
                <div className="stat-row">
                    <span style={{ fontWeight: '600' }}>Active Projects:</span>
                    <span style={{ color: '#06b6d4', fontWeight: '700' }}>{stats?.active_projects || 0}</span>
                </div>
                <div className="stat-row">
                    <span style={{ fontWeight: '600' }}>Pending Tasks:</span>
                    <span style={{ color: '#d97706', fontWeight: '700' }}>{stats?.pending_tasks || 0}</span>
                </div>
                <div className="stat-row">
                    <span style={{ fontWeight: '600' }}>Completed Tasks:</span>
                    <span style={{ color: '#059669', fontWeight: '700' }}>{stats?.completed_tasks || 0}</span>
                </div>
            </div>
        </div>
    );
}

// Tasks Tab Component
function TasksTab() {
    const [tasks, setTasks] = useState([]);
    const [filteredTasks, setFilteredTasks] = useState([]);
    const [loading, setLoading] = useState(true);
    const [searchTerm, setSearchTerm] = useState('');
    const [statusFilter, setStatusFilter] = useState('all');

    useEffect(() => {
        fetchTasks();
    }, []);

    useEffect(() => {
        // Filter tasks based on search term and status
        let filtered = tasks;
        
        // Apply status filter
        if (statusFilter !== 'all') {
            filtered = filtered.filter(task => task.status === statusFilter);
        }
        
        // Apply search filter
        if (searchTerm) {
            filtered = filtered.filter(task => 
                task.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
                task.project?.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                task.customer?.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                task.assigned_developer?.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                task.category.toLowerCase().includes(searchTerm.toLowerCase()) ||
                task.status.toLowerCase().includes(searchTerm.toLowerCase())
            );
        }
        
        setFilteredTasks(filtered);
    }, [searchTerm, statusFilter, tasks]);

    const fetchTasks = async () => {
        try {
            const response = await axios.get('/api/admin/tasks');
            setTasks(response.data);
            setFilteredTasks(response.data);
        } catch (error) {
            console.error('Error fetching tasks:', error);
        } finally {
            setLoading(false);
        }
    };

    // Task counts by status
    const pendingCount = tasks.filter(t => t.status === 'pending').length;
    const inProgressCount = tasks.filter(t => t.status === 'in_progress').length;
    const completedCount = tasks.filter(t => t.status === 'completed').length;

    if (loading) {
        return <div className="loading">Loading tasks...</div>;
    }

    return (
        <div>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '2rem', gap: '1rem', flexWrap: 'wrap' }}>
                <h2>All Tasks Overview</h2>
                <div style={{ position: 'relative', flex: 1, maxWidth: '500px' }}>
                    <input
                        type="text"
                        placeholder="Search by title, project, customer, developer, category, or status..."
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
            </div>

            {/* Task Status Filter */}
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

            <div className="card">
                <div style={{ color: '#64748b', fontSize: '0.95rem', marginBottom: '1rem' }}>
                    Showing {filteredTasks.length} of {tasks.length} task(s)
                </div>
                <div className="table-container">
                    <table className="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Project</th>
                                <th>Customer</th>
                                <th>Assigned To</th>
                                <th>Category</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            {filteredTasks.map(task => (
                                <tr key={task.id}>
                                    <td>{task.title}</td>
                                    <td>{task.project?.name}</td>
                                    <td>{task.customer?.name}</td>
                                    <td>{task.assigned_developer?.name || 'Unassigned'}</td>
                                    <td>
                                        <span className={`badge badge-${task.category}`}>
                                            {task.category}
                                        </span>
                                    </td>
                                    <td>
                                        <span className={`badge badge-${task.status.replace('_', '-')}`}>
                                            {task.status.replace('_', ' ')}
                                        </span>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );
}

