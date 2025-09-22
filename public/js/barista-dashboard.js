// JavaScript para el Dashboard del Barista
class BaristaDashboard {
    constructor() {
        this.refreshInterval = null;
        this.refreshTime = 3000; // 3 segundos
        this.baseUrl = 'http://172.80.15.22/cafeteria-nova/public/';
        this.isUpdating = false; // Prevenir múltiples actualizaciones simultáneas
        this.lastUpdate = null; // Timestamp de la última actualización
        this.init();
    }

    init() {
        console.log('🌸 Iniciando Dashboard del Barista...');
        this.setupAutoRefresh();
        this.setupClickHandlers();
        this.updateTimestamp();
    }

    // Auto-refresh para detectar nuevos pedidos confirmados
    setupAutoRefresh() {
        // Primera actualización inmediata
        this.actualizarPedidos();
        
        this.refreshInterval = setInterval(() => {
            this.actualizarPedidos();
        }, this.refreshTime);
        
        console.log('✅ Auto-refresh activado cada 3 segundos');
    }

    // Configurar manejadores de click para los pedidos
    setupClickHandlers() {
        document.addEventListener('click', (e) => {
            const pedidoCard = e.target.closest('.pedido-card');
            if (!pedidoCard) return;

            const pedidoId = pedidoCard.dataset.pedidoId;
            const estadoActual = pedidoCard.dataset.estado;

            if (!pedidoId || !estadoActual) return;

            // Prevenir múltiples clicks
            if (pedidoCard.classList.contains('processing')) return;

            this.cambiarEstadoPedido(pedidoId, estadoActual, pedidoCard);
        });
    }

    // Cambiar estado del pedido
    async cambiarEstadoPedido(pedidoId, estadoActual, cardElement) {
        try {
            cardElement.classList.add('processing');
            cardElement.style.opacity = '0.6';

            let action = '';
            let nuevoEstado = '';

            if (estadoActual === 'confirmado') {
                action = 'iniciarPreparacion';
                nuevoEstado = 'preparacion';
            } else if (estadoActual === 'preparacion') {
                action = 'finalizarPreparacion';
                nuevoEstado = 'listo';
            } else {
                throw new Error('Estado no válido para cambio');
            }

            const response = await fetch(this.baseUrl + 'barista_ajax.php?action=' + action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ id: pedidoId })
            });

            const result = await response.json();

            if (result.success) {
                this.mostrarNotificacion('✅ ' + result.message, 'success');
                // Actualizar inmediatamente después del cambio
                setTimeout(() => {
                    this.actualizarPedidos();
                }, 500);
            } else {
                throw new Error(result.message || 'Error desconocido');
            }

        } catch (error) {
            console.error('Error al cambiar estado:', error);
            this.mostrarNotificacion('❌ ' + error.message, 'error');
            cardElement.classList.remove('processing');
            cardElement.style.opacity = '1';
        }
    }

    // Actualizar pedidos mediante AJAX
    async actualizarPedidos() {
        // Prevenir múltiples actualizaciones simultáneas
        if (this.isUpdating) {
            console.log('⏳ Actualización en progreso, saltando...');
            return;
        }

        try {
            this.isUpdating = true;
            
            const response = await fetch(this.baseUrl + 'barista_ajax.php?action=obtenerPedidos', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            
            const data = await response.json();
            
            // Solo actualizar si hay cambios
            const dataString = JSON.stringify(data);
            if (this.lastUpdate && this.lastUpdate === dataString) {
                console.log('📄 Sin cambios detectados');
                return;
            }
            
            this.lastUpdate = dataString;
            this.renderizarPedidos(data);
            this.updateTimestamp();
            console.log('🔄 Pedidos actualizados');
            
        } catch (error) {
            console.error('❌ Error al actualizar pedidos:', error);
        } finally {
            this.isUpdating = false;
        }
    }

    // Renderizar pedidos en el DOM
    renderizarPedidos(data) {
        // Actualizar pedidos confirmados
        this.renderizarSeccionPedidos(
            'pedidos_confirmados', 
            data.pedidos_confirmados, 
            'confirmado'
        );
        
        // Actualizar pedidos en preparación
        this.renderizarSeccionPedidos(
            'pedidos_preparacion', 
            data.pedidos_preparacion, 
            'preparacion'
        );
        
        // Actualizar pedidos listos
        this.renderizarSeccionPedidos(
            'pedidos_listos', 
            data.pedidos_listos, 
            'listo'
        );
    }

    // Renderizar una sección específica de pedidos
    renderizarSeccionPedidos(containerId, pedidos, estado) {
        const container = document.getElementById(containerId);
        if (!container) return;

        if (!pedidos || pedidos.length === 0) {
            container.innerHTML = '<p class="text-muted text-center">✨ No hay pedidos en esta sección ✨</p>';
            return;
        }

        let html = '';
        
        pedidos.forEach(pedido => {
            const codigoPedido = "NV-" + String(pedido.id).padStart(4, "0");
            const esClickeable = estado === 'confirmado' || estado === 'preparacion';
            const cursorClass = esClickeable ? 'cursor-pointer' : '';
            const hoverEffect = esClickeable ? 'hover-effect' : '';

            html += `
                <div class="card mb-3 border-0 shadow-sm rounded-4 pedido-card ${cursorClass} ${hoverEffect}" 
                     data-pedido-id="${pedido.id}" 
                     data-estado="${estado}"
                     style="background-color: ${this.getBackgroundColor(estado)}; transition: all 0.3s ease;">
                    <div class="card-body">
                        <h6 class="fw-bold" style="color:${this.getTextColor(estado)};">
                            Pedido ${codigoPedido}
                            ${esClickeable ? '<span class="click-indicator">👆</span>' : ''}
                        </h6>
                        ${this.renderizarProductos(pedido.productos_categorizados)}
                        <p class="fw-bold mt-2" style="color:#d63384;">
                            Total: $${parseFloat(pedido.total).toFixed(2)}
                        </p>
                        ${this.renderizarBotonEstado(estado)}
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    // Renderizar productos categorizados
    renderizarProductos(productos) {
        if (!productos) return '';

        let html = '';

        // Bebidas
        if (productos.bebidas && productos.bebidas.length > 0) {
            html += `
                <div class="productos-seccion bebidas mb-2">
                    <h6 class="productos-titulo text-primary">🥤 Bebidas</h6>
            `;
            productos.bebidas.forEach(detalle => {
                html += `
                    <p>
                        <span class="badge bg-primary">${detalle.cantidad}x</span>
                        ${detalle.producto_nombre}
                    </p>
                `;
            });
            html += '</div>';
        }

        // Separador
        if (productos.bebidas && productos.bebidas.length > 0 && 
            productos.alimentos && productos.alimentos.length > 0) {
            html += '<hr class="productos-separador">';
        }

        // Alimentos
        if (productos.alimentos && productos.alimentos.length > 0) {
            html += `
                <div class="productos-seccion alimentos">
                    <h6 class="productos-titulo text-success">🍰 Alimentos</h6>
            `;
            productos.alimentos.forEach(detalle => {
                html += `
                    <p>
                        <span class="badge bg-success">${detalle.cantidad}x</span>
                        ${detalle.producto_nombre}
                    </p>
                `;
            });
            html += '</div>';
        }

        return html;
    }

    // Obtener color de fondo según estado
    getBackgroundColor(estado) {
        switch(estado) {
            case 'confirmado': return '#f9f1fb';
            case 'preparacion': return '#fff7e6';
            case 'listo': return '#f0fff4';
            default: return '#ffffff';
        }
    }

    // Obtener color de texto según estado
    getTextColor(estado) {
        switch(estado) {
            case 'confirmado': return '#8e44ad';
            case 'preparacion': return '#e67e22';
            case 'listo': return '#27ae60';
            default: return '#000000';
        }
    }

    // Renderizar botón según estado
    renderizarBotonEstado(estado) {
        switch(estado) {
            case 'confirmado':
                return '<small class="text-muted">👆 Click para iniciar preparación</small>';
            case 'preparacion':
                return '<small class="text-muted">👆 Click para marcar como listo</small>';
            case 'listo':
                return '<span class="badge" style="background: linear-gradient(45deg, #2ecc71, #ff66b2); font-size: 14px; padding: 8px 12px; border-radius: 15px;">✨ Listo ✨</span>';
            default:
                return '';
        }
    }

    // Actualizar timestamp de última actualización
    updateTimestamp() {
        const timestampElement = document.getElementById('ultimaActualizacion');
        if (timestampElement) {
            const now = new Date();
            const timeString = now.toLocaleTimeString('es-ES', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            timestampElement.textContent = `Última actualización: ${timeString}`;
        }
    }

    // Mostrar notificación
    mostrarNotificacion(mensaje, tipo = 'info') {
        // Crear elemento de notificación
        const notification = document.createElement('div');
        notification.className = `alert alert-${tipo === 'success' ? 'success' : 'danger'} position-fixed`;
        notification.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            animation: slideIn 0.3s ease;
        `;
        notification.textContent = mensaje;

        // Agregar al DOM
        document.body.appendChild(notification);

        // Remover después de 3 segundos
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    // Destruir el dashboard (limpiar intervalos)
    destroy() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
            console.log('🔴 Auto-refresh desactivado');
        }
    }
}

// CSS adicional para efectos
const additionalCSS = `
<style>
.cursor-pointer {
    cursor: pointer !important;
}

.hover-effect:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.click-indicator {
    font-size: 12px;
    margin-left: 5px;
    animation: bounce 1s infinite;
}

.processing {
    pointer-events: none;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-3px); }
    60% { transform: translateY(-2px); }
}

@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes slideOut {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(100%); opacity: 0; }
}
</style>
`;

// Agregar CSS al head
document.head.insertAdjacentHTML('beforeend', additionalCSS);

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    window.baristaDashboard = new BaristaDashboard();
});

// Limpiar al cerrar la página
window.addEventListener('beforeunload', () => {
    if (window.baristaDashboard) {
        window.baristaDashboard.destroy();
    }
});