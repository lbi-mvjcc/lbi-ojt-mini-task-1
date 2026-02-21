import { useState } from 'react';
import { Link, useNavigate, useSearchParams } from 'react-router-dom';
import axios from 'axios';

export default function ResetPassword() {
    const [searchParams] = useSearchParams();
    const [formData, setFormData] = useState({
        email: searchParams.get('email') || '',
        token: searchParams.get('token') || '',
        password: '',
        password_confirmation: '',
    });
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');
    const [loading, setLoading] = useState(false);
    const [showPassword, setShowPassword] = useState(false);
    const [showConfirmPassword, setShowConfirmPassword] = useState(false);
    const navigate = useNavigate();

    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError('');
        setSuccess('');

        if (formData.password !== formData.password_confirmation) {
            setError('Passwords do not match');
            return;
        }

        setLoading(true);

        try {
            const response = await axios.post('/api/password/reset', formData);
            setSuccess(response.data.message);
            
            setTimeout(() => {
                navigate('/login');
            }, 2000);
        } catch (err) {
            setError(err.response?.data?.message || err.response?.data?.errors?.email?.[0] || 'Failed to reset password');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="auth-container">
            <div className="auth-card">
                <h1>Task Sync</h1>
                <h2>Reset Password</h2>
                {error && <div className="alert alert-error">{error}</div>}
                {success && <div className="alert alert-success">{success}</div>}
                <form onSubmit={handleSubmit}>
                    <div className="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value={formData.email} onChange={handleChange} required disabled={loading} />
                    </div>
                    <div className="form-group">
                        <label>New Password</label>
                        <div className="password-input-wrapper">
                            <input type={showPassword ? 'text' : 'password'} name="password" value={formData.password} onChange={handleChange} required minLength={8} disabled={loading} />
                            <button type="button" onClick={() => setShowPassword(!showPassword)} className="password-toggle" disabled={loading}>
                                {showPassword ? <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg> : <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>}
                            </button>
                        </div>
                    </div>
                    <div className="form-group">
                        <label>Confirm Password</label>
                        <div className="password-input-wrapper">
                            <input type={showConfirmPassword ? 'text' : 'password'} name="password_confirmation" value={formData.password_confirmation} onChange={handleChange} required minLength={8} disabled={loading} />
                            <button type="button" onClick={() => setShowConfirmPassword(!showConfirmPassword)} className="password-toggle" disabled={loading}>
                                {showConfirmPassword ? <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg> : <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>}
                            </button>
                        </div>
                    </div>
                    <button type="submit" className="btn btn-primary" disabled={loading}>{loading ? 'Resetting...' : 'Reset Password'}</button>
                </form>
                <p className="auth-link">Remember your password? <Link to="/login">Back to Login</Link></p>
            </div>
        </div>
    );
}
