# 🏥 Medical Appointment App

## 📋 Descripción y Objetivo General
Este proyecto es una aplicación web enfocada en la **gestión de citas médicas**. Su objetivo es optimizar la administración de consultorios, facilitando el manejo de pacientes, doctores y agendas a través de una plataforma segura y eficiente.

## 🛠️ Tecnologías Actuales del Proyecto
El sistema cuenta actualmente con la siguiente base tecnológica instalada y configurada:

* **Backend:** [Laravel 12.0](https://laravel.com) con PHP ^8.2
* **Frontend Stack:** [Livewire ^3.6.4](https://livewire.laravel.com) para componentes dinámicos
* **Auth & Scaffolding:** Laravel Jetstream ^5.4
* **Estilos:** [Tailwind CSS ^3.4](https://tailwindcss.com)
* **Empaquetador:** Vite ^7.0
* **Base de Datos:** MySQL

---

## ⚙️ Configuraciones Realizadas

Se han establecido las siguientes configuraciones base en el código:

### 1. Zona Horaria (Timezone)
Configurada para sincronizar los registros con la hora local de Mérida.
* **Valor:** `'America/Merida'`
* **Ubicación:** `config/app.php`

### 2. Idioma (Locale)
El proyecto incorpora archivos de traducción al español para autenticación, validaciones y paginación.
* **Archivos:** `lang/es.json` y carpeta `lang/es/`
* **Nota:** Se debe asegurar que la variable `APP_LOCALE=es` esté definida en el archivo `.env`.

### 3. Fotos de Perfil
Habilitada la funcionalidad para que los usuarios suban y administren sus avatares.
* **Estado:** Activo (`Features::profilePhotos()`)
* **Ubicación:** `config/jetstream.php`

---

## ✅ Verificación de Instalación

Para confirmar que las configuraciones actuales funcionan correctamente:

1.  **Verificar Hora (Timezone):**
    * Ejecuta: `php artisan tinker`
    * Comando: `now();`
    * **Correcto si:** Muestra la hora actual de Mérida, Yucatán.

2.  **Verificar Idioma:**
    * Intenta registrarte dejando campos vacíos.
    * **Correcto si:** Los mensajes de error aparecen en español (ej. *"El campo contraseña es obligatorio"*).

3.  **Verificar Base de Datos:**
    * Ejecuta: `php artisan migrate`
    * **Correcto si:** Las migraciones se ejecutan sin errores de conexión.

4.  **Verificar Fotos:**
    * Ve a "Perfil" en el menú de usuario.
    * **Correcto si:** Aparece la opción para seleccionar y subir una foto.
