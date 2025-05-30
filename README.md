# RelaxSpa

Sistema web completo para la gestión de un centro de bienestar (SPA).  
Incluye frontend, backend en PHP y base de datos MySQL.

---

## 🔧 Tecnologías utilizadas

- **Frontend:** HTML5, CSS3, Bootstrap, JavaScript
- **Backend:** PHP puro
- **Base de datos:** MySQL (`relaxsp.sql` incluida)

---

## 🧩 Funcionalidades principales

- Registro e inicio de sesión para clientes, empleados y administradores
- Paneles diferenciados según el tipo de usuario
- Gestión completa de servicios, clientes, turnos y empleados
- Auditoría de acciones (por ejemplo, registro de turnos)
- Diseño adaptado a móviles (responsive)
- Exportación de base de datos

---

## 📁 Estructura del proyecto

RelaxSpa/
├── admin_.php
├── cliente_.php
├── empleado_.php
├── panel_.php
├── scripts/
├── styles/
├── image/
├── relaxsp.sql
└── index.php



---

## ⚙️ Instalación local

1. Cloná el repositorio:
   ```bash
   git clone https://github.com/evaernet/spa-relaxsp.git


2) Mové la carpeta a htdocs (si usás XAMPP):
C:/xampp/htdocs/RelaxSpa


3)Abrí phpMyAdmin, creá una base de datos llamada relaxsp, y ejecutá el script relaxsp.sql para importar las tablas.

Accedé al sistema desde:
http://localhost/RelaxSpa

