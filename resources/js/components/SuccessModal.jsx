import { useTheme } from '../context/ThemeContext';

export default function SuccessModal({ message, onClose }) {
    const { isDark } = useTheme();
    
    if (!message) return null;

    return (
        <div className="modal-overlay" onClick={onClose}>
            <div className="modal-content" style={{ maxWidth: '400px', width: '95%' }} onClick={(e) => e.stopPropagation()}>
                <div className="modal-header" style={{ borderBottom: 'none', paddingBottom: '0.5rem' }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}>
                        <div style={{ 
                            width: '48px', 
                            height: '48px', 
                            borderRadius: '50%', 
                            background: 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            color: 'white',
                            fontSize: '24px',
                            flexShrink: 0
                        }}>
                            ✓
                        </div>
                        <h3 style={{ margin: 0, color: isDark ? '#ffffff' : '#0f172a' }}>Success!</h3>
                    </div>
                </div>
                <div className="modal-body">
                    <p style={{ fontSize: '1rem', color: isDark ? '#ffffff' : '#475569', margin: 0 }}>
                        {message}
                    </p>
                </div>
                <div className="modal-footer">
                    <button onClick={onClose} className="btn btn-primary" style={{ width: '100%' }}>
                        OK
                    </button>
                </div>
            </div>
        </div>
    );
}
