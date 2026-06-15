# Plan de Implementación: Módulo de Citas (Appointments)

## Contexto
- Framework: Laravel + Livewire + Tailwind
- El módulo vive exclusivamente en el panel Admin (sin validaciones de roles/permisos)
- Asignación de cita: búsqueda de disponibilidad dinámica (Livewire) — sin módulo de horarios automáticos, los slots se validan contra citas ya existentes
- ConsultationManager: SÍ guarda en DB (requerido por "Consultas Anteriores")
- El campo "DNI" del PDF corresponde a `users.id_number` (ya existe en la BD, no requiere migración)

---

## Estado general

- [x] **Fase 1** — Base de datos (migraciones y modelos)
- [x] **Fase 2** — Rutas
- [x] **Fase 3** — Módulo de Citas: Index ✅ | Create ✅
- [x] **Fase 4** — ConsultationManager (correcciones de layout y detalles)
- [x] **Fase 5** — Vista de Horarios del Doctor (corregir grid + icono)
- [x] **Fase 6** — Sidebar y detalles finales

---

## Fase 1 — Base de datos ✅ COMPLETA

Migraciones corridas. Modelos con relaciones correctas.
- `users.id_number` = campo "DNI" que muestra el PDF (ya existía, sin cambios necesarios)

---

## Fase 2 — Rutas ✅ COMPLETA

`routes/admin.php` tiene las 3 rutas correctas:
- `Route::resource('appointments', AppointmentController::class)`
- `Route::get('doctors/{doctor}/schedules', ...)`
- `Route::get('appointments/{appointment}/consultation', ConsultationManager::class)`

---

## Fase 3 — Módulo de Citas: CRUD

### 3.1 Index ✅ COMPLETO
AppointmentController@index, AppointmentTable datatable, vista con badges de estado.

### 3.2 Create — ❌ REDISEÑAR COMPLETO

El PDF NO muestra un form simple. Muestra un **componente Livewire de búsqueda de disponibilidad**.

#### 3.2.1 Crear Livewire `AppointmentCreate`
- [ ] Archivo: `app/Livewire/Admin/AppointmentCreate.php`
- [ ] Propiedades:
  - `$searchDate`, `$searchHour` (ej. "08"), `$searchSpecialtyId`
  - `$searched = false`, `$doctorsWithSlots = []`
  - `$selectedDoctorId`, `$selectedDoctorName`
  - `$selectedStartTime`, `$selectedEndTime`, `$selectedDate`
  - `$patientId`, `$reason`
- [ ] `searchAvailability()`:
  - Valida que `searchDate` y `searchHour` estén presentes
  - Genera los 4 slots de 15 min de esa hora: `HH:00`, `HH:15`, `HH:30`, `HH:45`
  - Filtra doctores por especialidad (si se proporcionó) con `with('user', 'specialty')`
  - Para cada doctor, excluye slots donde ya existe una cita en esa fecha/hora (`start_time`)
  - Retorna array `$doctorsWithSlots = [{doctor, availableSlots: []}]`
  - Si no hay resultados, `$doctorsWithSlots = []` y muestra mensaje vacío
- [ ] `selectSlot($doctorId, $doctorName, $startTime)`:
  - Setea `$selectedDoctorId`, `$selectedDoctorName`, `$selectedStartTime`
  - Calcula `$selectedEndTime` = start + 15 min
  - Setea `$selectedDate = $searchDate`
- [ ] `store()`:
  - Valida: `patientId` required|exists:patients,id, `reason` required|string|min:3
  - Valida que `selectedDoctorId`, `selectedStartTime`, `selectedDate` no estén vacíos
  - Crea el `Appointment` y redirige al index con flash swal de éxito
- [ ] `render()`: layout admin, carga `$patients`, `$specialties`

#### 3.2.2 Vista `resources/views/livewire/admin/appointment-create.blade.php`

Layout de 2 columnas dentro de una `x-wire-card`:

**Columna izquierda — Búsqueda:**
- Título: "Buscar disponibilidad" + subtítulo "Encuentra el horario perfecto para tu cita."
- Fila de 3 inputs + botón:
  - `wire:model="searchDate"` → type="date"
  - `wire:model="searchHour"` → select de horas (08:00:00 - 09:00:00, 09:00:00 - 10:00:00 ... hasta 19:00:00 - 20:00:00)
  - `wire:model="searchSpecialtyId"` → select de especialidades (opcional)
  - Botón "Buscar disponibilidad" → `wire:click="searchAvailability"`
- Lista de resultados (cuando `$searched = true`):
  - Por cada doctor en `$doctorsWithSlots`:
    - Avatar (iniciales) + Nombre + Especialidad
    - "Horarios disponibles:" + badges de slots clicables → `wire:click="selectSlot(...)"`
    - El slot seleccionado se resalta (color primary)
  - Si `$searched && count($doctorsWithSlots) === 0`: mensaje "No hay horarios disponibles"

**Columna derecha — Resumen:**
- Aparece solo cuando hay un slot seleccionado (`$selectedDoctorId`)
- Título: "Resumen de la cita"
- Grid de datos: Doctor, Fecha, Horario (start – end), Duración: 15 minutos
- Select de Paciente (`wire:model="patientId"`)
- Textarea Motivo de la cita (`wire:model="reason"`)
- Botón "Confirmar cita" → `wire:click="store"`

#### 3.2.3 Actualizar ruta de create
- [ ] En `routes/admin.php`, añadir ANTES del resource:
  ```php
  Route::get('appointments/create', \App\Livewire\Admin\AppointmentCreate::class)->name('appointments.create');
  ```
  Y excluir create del resource:
  ```php
  Route::resource('appointments', AppointmentController::class)->except(['create']);
  ```

#### 3.2.4 Eliminar método create del AppointmentController
- [ ] El método `create()` ya no es necesario (lo reemplaza el Livewire)

### 3.3 Actions ✅ COMPLETO
`actions.blade.php` tiene: editar (azul) + estetoscopio (verde) + eliminar (rojo).

### 3.4 Validaciones store() ✅ COMPLETO
(Las validaciones del create se manejan ahora desde el componente Livewire)

---

## Fase 4 — ConsultationManager

### 4.1 Componente PHP ✅ COMPLETO
Lógica de tabs, modales, addMedicine, removeMedicine, saveConsultation implementada.

### 4.2 Vista — ❌ CORREGIR LAYOUT

El PDF muestra un layout MÁS SIMPLE que lo implementado:

- [ ] **Header de página** (fuera de la card, en la página directamente):
  - Nombre del paciente: `<h2>` grande
  - `DNI: {{ $appointment->patient->user->id_number }}`
  - Botones "Ver Historia" y "Consultas Anteriores" alineados a la derecha
  - **SIN** card de header con foto, info del doctor, fecha, etc.
- [ ] **Card única** con:
  - Tabs inline (Consulta | Receta) sin borde inferior complejo
  - Tab Consulta: 3 textareas (Diagnóstico, Tratamiento, Notas)
  - Tab Receta: tabla de medicamentos dinámica
  - Botón "Guardar Consulta" abajo a la derecha
- [ ] Eliminar la card de header que implementamos (con foto, doctor, fecha, etc.)
- [ ] El botón "Volver" puede moverse al breadcrumb o eliminarse (el PDF no lo muestra)

### 4.3 Modal "Consultas Anteriores" — ❌ AGREGAR BOTÓN

- [ ] Agregar botón **"Consultar Detalle"** en cada entrada del modal (placeholder, sin acción)
- [ ] Cada consulta muestra también las **Notas** además de diagnóstico y tratamiento

### 4.4 Modal "Historia Médica" — ❌ CORREGIR LAYOUT

El PDF muestra un grid horizontal compacto:
- [ ] Fila con 4 columnas: Tipo de sangre | Alergias | Enfermedades crónicas | Antecedentes quirúrgicos
- [ ] Botón "Ver / Editar Historia Médica" → `route('admin.patients.edit', $appointment->patient)`
- [ ] Layout más compacto que lo implementado

---

## Fase 5 — Vista de Horarios del Doctor

### 5.1 Método schedules() ✅ COMPLETO

### 5.2 Vista `admin/doctors/schedules.blade.php` — ❌ REDISEÑAR GRID

El PDF muestra una estructura diferente a la implementada:

| DÍA/HORA | LUNES | MARTES | MIÉRCOLES | ... |
|----------|-------|--------|-----------|-----|
| 08:00:00 | Todos | Todos  | Todos     | ... |
|          | 08:00-08:15 | 08:00-08:15 | ... | |
|          | 08:15-08:30 | 08:15-08:30 | ... | |
|          | 08:30-08:45 | 08:30-08:45 | ... | |
|          | 08:45-09:00 | 08:45-09:00 | ... | |
| 09:00:00 | Todos | Todos  | Todos     | ... |
|          | ...   | ...    | ...       | ... |

- [ ] **Columna 1** (`DÍA/HORA`): muestra la hora agrupadora (08:00:00) con `rowspan="5"`
- [ ] **Columnas 2–8** (un día cada una): dentro de cada grupo de hora, primera sub-fila = "Todos" checkbox, luego 4 sub-filas con el rango de 15 min
- [ ] El "Todos" es por día-hora (no por fila completa como lo tenemos)
- [ ] Mantener Alpine.js para la lógica de checkboxes
- [ ] Mantener header con nombre del doctor y botón "Guardar horario" decorativo

### 5.3 Botón en `admin/doctors/actions.blade.php` — ❌ CORREGIR ÍCONO

- [ ] Cambiar `fa-stethoscope` por **`fa-clock`** (el PDF dice "ícono de reloj" en los criterios de evaluación)

---

## Fase 6 — Sidebar ✅ COMPLETA

"Citas médicas" ya está en el sidebar con `fa-calendar-check`.

---

## Resumen de pendientes

| # | Tarea | Prioridad |
|---|-------|-----------|
| A | Livewire `AppointmentCreate` con búsqueda de disponibilidad | Alta |
| B | Vista `appointment-create.blade.php` (2 columnas) | Alta |
| C | Actualizar rutas (create → Livewire) | Alta |
| D | Corregir header ConsultationManager (nombre + id_number, sin card) | ✅ |
| E | Corregir modal Historia Médica (layout horizontal 4 columnas) | ✅ |
| F | Agregar botón "Consultar Detalle" en modal Consultas Anteriores | ✅ |
| G | Rediseñar grid de horarios (rowspan por hora, "Todos" por día-columna) | ✅ |
| H | Cambiar ícono del botón de horarios en doctors/actions → `fa-clock` | ✅ |
