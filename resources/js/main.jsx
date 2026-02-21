import './bootstrap';
import { createRoot } from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';
import App from './App';

console.log('React main.jsx loaded');

const rootElement = document.getElementById('app');
console.log('Root element:', rootElement);

if (rootElement) {
    const root = createRoot(rootElement);
    root.render(
        <BrowserRouter>
            <App />
        </BrowserRouter>
    );
    console.log('React app rendered');
} else {
    console.error('Root element #app not found!');
}
