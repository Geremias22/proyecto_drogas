-- ⚠️ SOLO PARA INICIALIZAR O RESETEAR EN DESARROLLO
-- DROP DATABASE IF EXISTS `el_punto_ciego`;
CREATE DATABASE `el_punto_ciego`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `el_punto_ciego`;

-- ==============================
--  USUARIOS
-- ==============================
CREATE TABLE users (
    id INT AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    role VARCHAR(50)  NOT NULL DEFAULT 'customer',
    gmail VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    edad TINYINT UNSIGNED,
    img VARCHAR(255),
    date_create DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_date DATETIME NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    CONSTRAINT pk_users PRIMARY KEY (id),
    CONSTRAINT uq_users_gmail UNIQUE (gmail)
) ENGINE=InnoDB;

-- ==============================
--  IMPUESTOS
-- ==============================
CREATE TABLE taxes (
    id INT AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    rate_iva  DECIMAL(5,2) NOT NULL,
    CONSTRAINT pk_taxes PRIMARY KEY (id)
) ENGINE=InnoDB;

-- ==============================
--  CATEGORÍAS
-- ==============================
CREATE TABLE categories (
    id INT AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    parent_id INT NULL,
    image VARCHAR(255),
    tax_id INT NULL,
    date_create DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_date DATETIME NULL,
    CONSTRAINT pk_categories PRIMARY KEY (id),
    CONSTRAINT fk_categories_parent
        FOREIGN KEY (parent_id) REFERENCES categories(id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_categories_tax
        FOREIGN KEY (tax_id) REFERENCES taxes(id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ==============================
--  PROVEEDORES
-- ==============================
CREATE TABLE suppliers (
    id INT AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50)  NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(50),
    address VARCHAR(255),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_suppliers PRIMARY KEY (id),
    CONSTRAINT uq_suppliers_code UNIQUE (code)
) ENGINE=InnoDB;

-- ==============================
--  PRODUCTOS
-- ==============================
CREATE TABLE products (
    id INT AUTO_INCREMENT,
    category_id INT NOT NULL,
    supplier_id INT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    date_create DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_date DATETIME NULL,
    CONSTRAINT pk_products PRIMARY KEY (id),
    CONSTRAINT fk_products_category
        FOREIGN KEY (category_id) REFERENCES categories(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_products_supplier
        FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ==============================
--  PACKS
-- ==============================
CREATE TABLE packs (
    id INT AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    parent_id INT NULL,
    image VARCHAR(255),
    date_create DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_date DATETIME NULL,
    CONSTRAINT pk_packs PRIMARY KEY (id),
    CONSTRAINT fk_packs_parent
        FOREIGN KEY (parent_id) REFERENCES packs(id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Relación N:N entre packs y productos
CREATE TABLE pack_products (
    pack_id INT NOT NULL,
    product_id INT NOT NULL,
    qty INT NOT NULL DEFAULT 1,
    CONSTRAINT pk_pack_products PRIMARY KEY (pack_id, product_id),
    CONSTRAINT fk_pack_products_pack
        FOREIGN KEY (pack_id) REFERENCES packs(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_pack_products_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ==============================
--  STOCK (1:1 con products)
-- ==============================
CREATE TABLE stock (
    id INT AUTO_INCREMENT,
    product_id INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 0,
    cantidad_max INT NULL,
    cantidad_min INT NULL,
    CONSTRAINT pk_stock PRIMARY KEY (id),
    CONSTRAINT uq_stock_product UNIQUE (product_id),
    CONSTRAINT fk_stock_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ==============================
--  MOVIMIENTOS DE STOCK
-- ==============================
CREATE TABLE stock_movements (
    id INT AUTO_INCREMENT,
    stock_id INT NOT NULL,
    type VARCHAR(20) NOT NULL,   -- 'IN', 'OUT', 'ADJUST', etc.
    quantity INT NOT NULL,
    date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_stock_movements PRIMARY KEY (id),
    CONSTRAINT fk_stock_movements_stock
        FOREIGN KEY (stock_id) REFERENCES stock(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ==============================
--  PEDIDOS (ORDERS)
-- ==============================
CREATE TABLE orders (
    id INT AUTO_INCREMENT,
    user_id INT NOT NULL,
    order_reference VARCHAR(50) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    phone VARCHAR(30),
    address VARCHAR(255),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_orders PRIMARY KEY (id),
    CONSTRAINT uq_orders_reference UNIQUE (order_reference),
    CONSTRAINT fk_orders_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Relación N:N entre orders y products (líneas de pedido)
CREATE TABLE order_products (
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    CONSTRAINT pk_order_products PRIMARY KEY (order_id, product_id),
    CONSTRAINT fk_order_products_order
        FOREIGN KEY (order_id) REFERENCES orders(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_order_products_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =====================================
--  DATOS DE PRUEBA
-- =====================================

-- Impuestos
INSERT INTO taxes (name, rate_iva) VALUES
('IVA general',       21.00),
('IVA reducido',      10.00),
('IVA superreducido', 4.00);

-- Categorías
-- 1: materia_prima -> flor, hachís
-- 2: productos_preparados -> porros, edibles
-- 3: material_fumador -> papel, filtros, mecheros, grinders
-- 4: merch -> camisetas, merchandising aso
INSERT INTO categories (name, parent_id, tax_id, image) VALUES
('materia_prima',       NULL, 1, NULL), 
('productos_preparados',NULL, 1, NULL),
('material_fumador',    NULL, 1, NULL),
('merch',               NULL, 1, NULL);

-- Proveedores
INSERT INTO suppliers (name, code, email, phone, address) VALUES
('Proveedor Norte', 'SUP-001', 'norte@example.com', '600000001', 'Calle Norte 1'),
('Proveedor Sur',   'SUP-002', 'sur@example.com',   '600000002', 'Av. Sur 22');

-- Productos de ejemplo (aso)
-- category_id:
--   1 = materia_prima
--   2 = productos_preparados
--   3 = material_fumador
--   4 = merch
-- supplier_id:
--   1 = Proveedor Norte
--   2 = Proveedor Sur
INSERT INTO products (category_id, supplier_id, name, description, price, is_active) VALUES
(1, 1, 'Flor Sativa 1g', 
 'Flor seca tipo sativa, paquete orientativo de 1 gramo.', 
 7.00, 1),

(1, 1, 'Hachís clásico 1g', 
 'Resina prensada clásica, unidad orientativa de 1 gramo.', 
 6.50, 1),

(2, 2, 'Porro liado híbrido', 
 'Cigarro liado preparado en la asociación (uso interno).', 
 4.00, 1),

(4, 2, 'Camiseta logo asociación', 
 'Camiseta de algodón con el logo de la asociación.', 
 15.00, 1),

(3, 2, 'Papel de liar King Size', 
 'Librito de papel de liar tamaño king size.', 
 1.20, 1),

(3, 1, 'Grinder metálico 4 partes', 
 'Grinder metálico con depósito y tamiz.', 
 12.90, 1);

-- Stock inicial para esos productos
-- product_id 1..6 corresponden a los INSERT anteriores en orden
INSERT INTO stock (product_id, cantidad, cantidad_max, cantidad_min) VALUES
(1, 100, 500, 10),   -- Flor Sativa 1g
(2,  80, 400, 10),   -- Hachís clásico 1g
(3,  50, 200,  5),   -- Porro liado híbrido
(4,  20, 100,  2),   -- Camiseta logo asociación
(5, 200,1000, 20),   -- Papel de liar King Size
(6,  30, 150,  5);   -- Grinder metálico

-- Pack de ejemplo (pack de bienvenida con material de fumador + porro)
INSERT INTO packs (name, parent_id, image) VALUES
('Pack Bienvenida', NULL, NULL);

INSERT INTO pack_products (pack_id, product_id, qty) VALUES
(1, 3, 1),   -- Porro liado híbrido
(1, 5, 1),   -- Papel de liar King Size
(1, 6, 1);   -- Grinder metálico

-- Usuario admin de prueba (password en claro para desarrollo: 'admin')
INSERT INTO users (name, role, gmail, password, edad, is_active) VALUES
('Admin', 'admin', 'admin@admin.com', 'admin', 30, 1);



-- ALTER TABLE orders ADD payment_method VARCHAR(30) NULL AFTER status;

-- ALTER TABLE packs
--   ADD COLUMN final_price DECIMAL(10,2) NULL AFTER image,
--   ADD COLUMN discount_percent DECIMAL(5,2) NULL AFTER final_price;

-- ALTER TABLE users
--   ADD COLUMN security_question VARCHAR(255) NULL AFTER password,
--   ADD COLUMN security_answer_hash VARCHAR(255) NULL AFTER security_question;


-- pendiente ajax catalogo