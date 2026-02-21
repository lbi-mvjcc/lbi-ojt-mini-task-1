import { createContext, useContext, useState, useEffect } from 'react';
import { useLocation } from 'react-router-dom';

const ThemeContext = createContext();

export function useTheme() {
    return useContext(ThemeContext);
}

export function ThemeProvider({ children }) {
    const location = useLocation();
    const [theme, setTheme] = useState(() => {
        return localStorage.getItem('theme') || 'light';
    });

    useEffect(() => {
        localStorage.setItem('theme', theme);
        
        // List of auth routes that should never have dark mode
        const authRoutes = ['/', '/login', '/register', '/password/forgot', '/password/reset'];
        const isAuthPage = authRoutes.includes(location.pathname);
        
        // Only apply theme if not on auth page
        if (!isAuthPage) {
            document.documentElement.setAttribute('data-theme', theme);
        } else {
            document.documentElement.setAttribute('data-theme', 'light');
        }
    }, [theme, location.pathname]);

    const toggleTheme = () => {
        setTheme(prev => prev === 'light' ? 'dark' : 'light');
    };

    const value = {
        theme,
        toggleTheme,
        isDark: theme === 'dark'
    };

    return <ThemeContext.Provider value={value}>{children}</ThemeContext.Provider>;
}
