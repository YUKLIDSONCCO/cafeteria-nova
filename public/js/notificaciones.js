class Notificaciones {
    constructor() {
        this.ultimaNotificacionId = 0;
        this.intervalo = null;
        this.init();
    }

    init() {
        this.iniciarPolling();
        this.setupEventListeners();
    }

    iniciarPolling() {
        // Polling cada 5 segundos
        this.intervalo = setInterval(() => {
            this.verificarNotificaciones();
        }, 5000);

        // Verificar inmediatamente al cargar
        this.verificarNotificaciones();
    }

    async verificarNotificaciones() {
        try {
            const response = await fetch(`${BASE_URL}notificacion/obtener?ultima_id=${this.ultimaNotificacionId}`);
            const data = await response.json();

            if (data.success && data.notificaciones.length > 0) {
                this.mostrarNotificaciones(data.notificaciones);
                this.actualizarContador(data.notificaciones.length);
                
                // Actualizar última ID
                const maxId = Math.max(...data.notificaciones.map(n => parseInt(n.id)));
                this.ultimaNotificacionId = maxId;
            }
        } catch (error) {
            console.error('Error al obtener notificaciones:', error);
        }
    }

    mostrarNotificaciones(notificaciones) {
        notificaciones.forEach(notificacion => {
            this.mostrarNotificacion(notificacion);
        });
    }

    mostrarNotificacion(notificacion) {
        // Crear notificación toast de Bootstrap
        const toastHtml = `
            <div class="toast notificacion-toast" role="alert" data-notificacion-id="${notificacion.id}">
                <div class="toast-header">
                    <strong class="me-auto">Nueva notificación</strong>
                    <small class="text-muted">${new Date().toLocaleTimeString()}</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    ${notificacion.mensaje}
                    ${notificacion.pedido_id ? `<br><small>Pedido ID: ${notificacion.pedido_id}</small>` : ''}
                </div>
            </div>
        `;

        // Agregar al contenedor
        const container = document.getElementById('notificaciones-toast-container');
        if (container) {
            container.insertAdjacentHTML('beforeend', toastHtml);
            
            // Mostrar toast
            const toastElement = container.lastElementChild;
            const toast = new bootstrap.Toast(toastElement);
            toast.show();

            // Marcar como leída cuando se cierra
            toastElement.addEventListener('hidden.bs.toast', () => {
                this.marcarComoLeida(notificacion.id);
            });
        }

        // Reproducir sonido de notificación
        this.reproducirSonido();
    }

    async marcarComoLeida(id) {
        try {
            await fetch(`${BASE_URL}notificacion/marcarLeida/${id}`, {
                method: 'POST'
            });
            this.actualizarContador(-1);
        } catch (error) {
            console.error('Error al marcar notificación como leída:', error);
        }
    }

    async marcarTodasLeidas() {
        try {
            await fetch(`${BASE_URL}notificacion/marcarTodasLeidas`, {
                method: 'POST'
            });
            this.actualizarContador(0, true);
        } catch (error) {
            console.error('Error al marcar todas como leídas:', error);
        }
    }

    actualizarContador(cantidad, reset = false) {
        const badge = document.getElementById('notificaciones-badge');
        if (badge) {
            if (reset) {
                badge.textContent = '0';
                badge.classList.add('d-none');
            } else {
                const current = parseInt(badge.textContent) || 0;
                const newCount = Math.max(0, current + cantidad);
                badge.textContent = newCount;
                badge.classList.toggle('d-none', newCount === 0);
            }
        }
    }

    reproducirSonido() {
        // Crear sonido de notificación simple
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBSt5t/LQciUADIW99tiAGgAHe7z43Y8VAAV6vPnelBwABHq8+d+WHQACebz535gfAAJ6vfrimyEAAXq9+uOcIwABe7775J0kAAF7vvvlniUAAHy+/OafJgAAfL/856AoAAB8wP3ooSkAAHzA/eihKQAA');
        audio.volume = 0.3;
        audio.play().catch(() => {});
    }

    setupEventListeners() {
        // Botón para marcar todas como leídas
        const markAllBtn = document.getElementById('mark-all-notifications');
        if (markAllBtn) {
            markAllBtn.addEventListener('click', () => this.marcarTodasLeidas());
        }

        // Botón para mostrar panel de notificaciones
        const notificationsBtn = document.getElementById('notifications-btn');
        if (notificationsBtn) {
            notificationsBtn.addEventListener('click', () => this.mostrarPanelNotificaciones());
        }
    }

    async mostrarPanelNotificaciones() {
        // Implementar panel de notificaciones si es necesario
        console.log('Mostrar panel de notificaciones');
    }

    detener() {
        if (this.intervalo) {
            clearInterval(this.intervalo);
        }
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.notificaciones = new Notificaciones();
});