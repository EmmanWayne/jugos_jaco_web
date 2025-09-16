<style>
    /* Toast Notifications Styles */
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
    }

    .toast {
        min-width: 350px;
        margin-bottom: 10px;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        border: 1px solid;
        opacity: 1;
        transition: all 0.3s ease;
    }

    .toast.show {
        display: block;
        animation: slideInRight 0.3s ease-out;
    }

    .toast-header {
        border-radius: 10px 10px 0 0;
        padding: 0.75rem 1rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .toast-body {
        padding: 1rem;
        font-size: 0.95rem;
    }

    .toast .close {
        padding: 0.25rem 0.5rem;
        background-color: transparent;
        border: 0;
        color: inherit;
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1;
        opacity: 0.7;
        cursor: pointer;
        transition: opacity 0.15s ease-in-out;
        margin-left: auto;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border-radius: 4px;
    }

    .toast .close:hover {
        opacity: 1;
        background-color: rgba(0, 0, 0, 0.1);
    }

    .toast .close:focus {
        outline: none;
        opacity: 1;
        background-color: rgba(0, 0, 0, 0.1);
    }

    .toast .close span {
        font-size: 1.2rem;
        line-height: 1;
        font-weight: bold;
        display: block;
    }

    .toast .mr-auto {
        margin-right: auto !important;
    }

    .toast .mr-2 {
        margin-right: 0.5rem !important;
    }

    .toast .ml-2 {
        margin-left: 0.5rem !important;
    }

    .toast .mb-1 {
        margin-bottom: 0.25rem !important;
    }

    .toast .text-success {
        color: #28a745 !important;
    }

    .toast .text-danger {
        color: #dc3545 !important;
    }

    .toast .text-warning {
        color: #ffc107 !important;
    }

    .toast .fas {
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
    }

    /* Fallback para iconos si no tienes FontAwesome */
    .toast .icon-check::before {
        content: "✓";
        font-weight: bold;
        margin-right: 0.5rem;
    }

    .toast .icon-error::before {
        content: "✕";
        font-weight: bold;
        margin-right: 0.5rem;
    }

    .toast .icon-warning::before {
        content: "⚠";
        font-weight: bold;
        margin-right: 0.5rem;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @media (max-width: 768px) {
        .toast-container {
            right: 10px;
            left: 10px;
            top: 10px;
        }

        .toast {
            min-width: auto;
            width: 100%;
        }
    }
</style>
<!-- Toast Notifications -->
<div class="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
    @if (session()->has('success'))
        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true"
            style="background-color: #d4edda; border-color: #c3e6cb; color: #155724;">
            <div class="toast-header" style="background-color: #d4edda; border-bottom: 1px solid #c3e6cb;">
                <div style="display: flex; align-items: center;">
                    <i class="fas fa-check-circle text-success mr-2"></i>
                    <strong class="text-success">Éxito</strong>
                </div>
                <button type="button" class="close" aria-label="Close" onclick="closeToast(this)">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="toast-body">
                <i class="fas fa-info-circle mr-2"></i>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true"
            style="background-color: #f8d7da; border-color: #f5c6cb; color: #721c24;">
            <div class="toast-header" style="background-color: #f8d7da; border-bottom: 1px solid #f5c6cb;">
                <div style="display: flex; align-items: center;">
                    <i class="fas fa-exclamation-triangle text-danger mr-2"></i>
                    <strong class="text-danger">Error</strong>
                </div>
                <button type="button" class="close" aria-label="Close" onclick="closeToast(this)">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="toast-body">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        </div>
    @endif

    @if (session()->has('warning'))
        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true"
            style="background-color: #fff3cd; border-color: #ffeeba; color: #856404;">
            <div class="toast-header" style="background-color: #fff3cd; border-bottom: 1px solid #ffeeba;">
                <div style="display: flex; align-items: center;">
                    <i class="fas fa-exclamation-circle text-warning mr-2"></i>
                    <strong class="text-warning">Advertencia</strong>
                </div>
                <button type="button" class="close" aria-label="Close" onclick="closeToast(this)">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="toast-body">
                {{ session('warning') }}
            </div>
        </div>
    @endif

    @if (session()->has('info'))
        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true"
            style="background-color: #d1ecf1; border-color: #bee5eb; color: #0c5460;">
            <div class="toast-header" style="background-color: #d1ecf1; border-bottom: 1px solid #bee5eb;">
                <div style="display: flex; align-items: center;">
                    <i class="fas fa-info-circle text-info mr-2"></i>
                    <strong class="text-info">Información</strong>
                </div>
                <button type="button" class="close" aria-label="Close" onclick="closeToast(this)">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="toast-body">
                {{ session('info') }}
            </div>
        </div>
    @endif
</div>

<script>
    // Variable global para evitar múltiples inicializaciones
    window.globalToastsInitialized = window.globalToastsInitialized || false;

    function initToasts() {
        // Solo seleccionar toasts que no han sido inicializados
        const toasts = document.querySelectorAll('.toast.show:not([data-toast-initialized])');
        
        if (toasts.length === 0) return;

        toasts.forEach(function(toast) {
            // Marcar como inicializado para evitar duplicados
            toast.setAttribute('data-toast-initialized', 'true');
            
            // Auto-hide después de 5 segundos
            const hideTimeout = setTimeout(function() {
                hideToast(toast);
            }, 2000);
            
            // Guardar el timeout en el elemento para poder cancelarlo
            toast._hideTimeout = hideTimeout;
        });
    }

    function hideToast(toast) {
        if (!toast || !toast.parentNode) return;
        
        // Cancelar timeout si existe
        if (toast._hideTimeout) {
            clearTimeout(toast._hideTimeout);
            toast._hideTimeout = null;
        }
        
        toast.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        
        setTimeout(function() {
            if (toast && toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }

    // Manual close function
    function closeToast(button) {
        const toast = button.closest('.toast');
        if (toast) {
            hideToast(toast);
        }
    }

    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initToasts();
        });
    } else {
        initToasts();
    }

    // Escuchar eventos de Livewire para nuevos toasts
    document.addEventListener('livewire:updated', function() {
        // Pequeño delay para asegurar que el DOM se ha actualizado
        setTimeout(function() {
            initToasts();
        }, 10);
    });

    // También escuchar cambios en el DOM para mayor compatibilidad
    if (window.MutationObserver) {
        const observer = new MutationObserver(function(mutations) {
            let shouldInit = false;
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1 && (node.classList.contains('toast') || node.querySelector('.toast'))) {
                            shouldInit = true;
                        }
                    });
                }
            });
            if (shouldInit) {
                setTimeout(initToasts, 10);
            }
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
</script>
