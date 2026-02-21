import React, { createContext, useContext, useState, useEffect } from 'react';
import axios from 'axios';

const AuthContext = createContext();

export function useAuth() {
    return useContext(AuthContext);
}

export function AuthProvider({ children }) {
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const token = localStorage.getItem('token');
        if (token) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            fetchUser();
        } else {
            setLoading(false);
        }
    }, []);

    const fetchUser = async () => {
        try {
            const response = await axios.get('/api/me');
            const userData = response.data;
            // Add helper methods to user object
            userData.isCustomer = () => userData.role === 'customer';
            userData.isDeveloper = () => ['frontend_developer', 'backend_developer', 'server_admin'].includes(userData.role);
            userData.isAdmin = () => userData.role === 'admin';
            setUser(userData);
        } catch (error) {
            localStorage.removeItem('token');
            delete axios.defaults.headers.common['Authorization'];
        } finally {
            setLoading(false);
        }
    };

    const login = async (email, password) => {
        const response = await axios.post('/api/login', { email, password });
        const { token, user: userData } = response.data;
        localStorage.setItem('token', token);
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        // Add helper methods to user object
        userData.isCustomer = () => userData.role === 'customer';
        userData.isDeveloper = () => ['frontend_developer', 'backend_developer', 'server_admin'].includes(userData.role);
        userData.isAdmin = () => userData.role === 'admin';
        setUser(userData);
        return userData;
    };

    const register = async (data) => {
        const response = await axios.post('/api/register', data);
        const { token, user: userData } = response.data;
        localStorage.setItem('token', token);
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        // Add helper methods to user object
        userData.isCustomer = () => userData.role === 'customer';
        userData.isDeveloper = () => ['frontend_developer', 'backend_developer', 'server_admin'].includes(userData.role);
        userData.isAdmin = () => userData.role === 'admin';
        setUser(userData);
        return userData;
    };

    const logout = async () => {
        try {
            await axios.post('/api/logout');
        } catch (error) {
            console.error('Logout error:', error);
        }
        localStorage.removeItem('token');
        delete axios.defaults.headers.common['Authorization'];
        setUser(null);
    };

    const value = {
        user,
        setUser,
        loading,
        login,
        register,
        logout,
    };

    return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}
