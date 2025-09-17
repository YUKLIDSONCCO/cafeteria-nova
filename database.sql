-- Crear base de datos
CREATE DATABASE cafeteria_nova;
USE cafeteria_nova;

-- Tabla de usuarios
CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  rol ENUM('cliente', 'mesero', 'barista', 'cajero', 'administrador') NOT NULL,
  activo BOOLEAN DEFAULT FALSE,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  ultimo_login TIMESTAMP NULL,
  ip_ultimo_acceso VARCHAR(45)
);

-- Insertar administradores (contraseña: "password")
INSERT INTO usuarios (nombre, email, password_hash, rol, activo) VALUES
('Admin Principal', 'admin1@cafenova.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador', 1),
('Admin Secundario', 'admin2@cafenova.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador', 1);

-- Tabla de tickets de cliente
CREATE TABLE tickets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(20) UNIQUE NOT NULL,
  cliente_info TEXT,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  expira_en DATETIME NOT NULL,
  usado BOOLEAN DEFAULT FALSE
);

-- Tabla de tokens de menú
CREATE TABLE tokens_menu (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ticket_id INT,
  token VARCHAR(64) UNIQUE NOT NULL,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  expira_en DATETIME NOT NULL,
  FOREIGN KEY (ticket_id) REFERENCES tickets(id)
);

-- Tabla de mesas
CREATE TABLE mesas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(10) UNIQUE NOT NULL,
  capacidad INT NOT NULL,
  estado ENUM('libre', 'ocupada', 'reservada') DEFAULT 'libre'
);

-- Tabla de productos
CREATE TABLE productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  categoria VARCHAR(50) NOT NULL,
  precio DECIMAL(10,2) NOT NULL,
  stock INT DEFAULT 0,
  disponible BOOLEAN DEFAULT TRUE,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de pedidos
CREATE TABLE pedidos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(20) UNIQUE NOT NULL,
  cliente_id INT,
  tipo ENUM('mesa', 'llevar', 'reserva') NOT NULL,
  estado ENUM('creado', 'pendiente', 'confirmado', 'preparacion', 'listo', 'entregado', 'pagado') DEFAULT 'creado',
  total DECIMAL(10,2) DEFAULT 0,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (cliente_id) REFERENCES usuarios(id)
);

-- Tabla de detalles de pedido
CREATE TABLE detalle_pedido (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pedido_id INT,
  producto_id INT,
  cantidad INT NOT NULL,
  precio_unit DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
  FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- Tabla de reservas
CREATE TABLE reservas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pedido_id INT,
  hora_reserva TIME NOT NULL,
  fecha_reserva DATE NOT NULL,
  nombre_cliente VARCHAR(100) NOT NULL,
  codigo_reserva VARCHAR(20) UNIQUE NOT NULL,
  FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
);

-- Tabla de pagos
CREATE TABLE pagos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pedido_id INT,
  monto DECIMAL(10,2) NOT NULL,
  metodo ENUM('efectivo', 'tarjeta', 'transferencia') NOT NULL,
  estado ENUM('pendiente', 'completado', 'fallido') DEFAULT 'pendiente',
  timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
);

-- Tabla de inventario
CREATE TABLE inventario (
  id INT AUTO_INCREMENT PRIMARY KEY,
  producto VARCHAR(100) NOT NULL,
  cantidad_actual INT DEFAULT 0,
  minimo INT DEFAULT 10,
  actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de notificaciones
CREATE TABLE notificaciones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tipo VARCHAR(50) NOT NULL,
  destinatario_rol VARCHAR(20),
  mensaje TEXT NOT NULL,
  leido BOOLEAN DEFAULT FALSE,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de logs de eventos
CREATE TABLE logs_eventos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  evento_tipo VARCHAR(50) NOT NULL,
  usuario_id INT,
  rol VARCHAR(20),
  meta_json JSON,
  ip VARCHAR(45),
  timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);
