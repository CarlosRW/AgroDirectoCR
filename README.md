# 🌾 AgroDirectoCR

AgroDirectoCR es una plataforma web para conectar directamente a productores agrícolas con consumidores finales en Costa Rica, eliminando intermediarios y fomentando el comercio justo y local.  

El sistema cuenta con funcionalidades completas para registro de usuarios, gestión de productos, (carrito de compras hace falta terminarlo) y paneles administrativos diferenciados por rol de usuarios (Productores y Consumidores) desarrollada con **HTML, CSS, Bootstrap, PHP**.

## 📋 Características actuales

### 🔐 Sistema de Autenticación
- Registro de usuarios con validación de datos
- Inicio de sesión seguro con contraseñas encriptadas
- Dos roles diferenciados: Productor y Consumidor
- Gestión de perfiles con información editable
- Protección de rutas según rol de usuario

### 👨‍🌾 Panel del Productor
- Dashboard personalizado con estadísticas básicas
- Publicación de productos con categorías (Frutas, Vegetales, Granos)
- Gestión de inventario: crear, editar y eliminar productos
- Control de stock y precios por producto
- Vista de productos propios organizados y fáciles de administrar

### 🛒 Panel del Consumidor
- Catálogo de productos con información detallada
- (No Terminado) Carrito de compras funcional con gestión de cantidades
- Vista de productos disponibles con stock en tiempo real
- Navegación intuitiva entre productos y categorías
- (No Terminado) Gestión de compras (carrito, pedidos)

### 🗄️ Base de Datos
- MySQL
- Tablas principales: users, products, categories, orders, order_items


## Actualización
- Panel principal según el rol
- Productor: acceso a publicación de productos y pedidos
- Consumidor: acceso al catálogo y pedidos (en desarrollo)
- Publicación de productos (solo para productores):
- Formulario para agregar productos con:
- Nombre
- Categoría (Frutas, Vegetales, Granos)
- Precio
- Cantidad disponible
- Fecha estimada de cosecha

✅ Completamente Funcional

 - Registro y autenticación de usuarios
 - Paneles diferenciados por rol (Productor/Consumidor)
 - CRUD completo de productos para productores
 - Catálogo de productos para consumidores
 - Carrito de compras funcional
 - Gestión de perfil de usuario
 - Sistema de navegación adaptativo
 - Validaciones de formularios
 - Manejo de errores y mensajes


## 🔧 Requisitos

- [XAMPP](https://www.apachefriends.org/) o servidor con:
  - PHP >= 7.4
  - MySQL >= 5.7
  - Apache

## 🚀 Instalación y Configuración

### 1. Preparar el entorno
```
# Descargar e instalar XAMPP
# Iniciar Apache y MySQL desde el panel de control
```

### 2. Configurar la base de datos
```
-- Ejecutar el archivo DBAgroDirectoCR.sql en phpMyAdmin
-- Esto creará la base de datos y todas las tablas necesarias
```

### 3. Configurar la conexión
```
// Actualizar conexion.php con tus credenciales
$host = "localhost";
$dbname = "agro_directo_cr";
$user = "root";
$pass = "tu_password_mysql";
```

### 4. Desplegar archivos
```
# Copiar todos los archivos del proyecto en htdocs/AgroDirectoCR/
# Asegurar permisos de lectura/escritura
```

### 5. Inicia Apache desde el panel de XAMPP.

### 6. Abre tu navegador y visita:
http://localhost/AgroDirectoCR
