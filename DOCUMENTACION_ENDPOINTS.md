# Ejemplos de Request y Response

## CursoController

### Crear curso
**POST /cursos**
```json
{
	"nombre": "Matemáticas 2026",
	"descripcion": "Curso anual de matemáticas.",
	"nota_minima": 3.0,
	"nota_maxima": 5.0,
	"id_profesor": 1,
	"usa_asistencia": true,
	"peso_asistencia": 0.1
}
```
**Response:**
```json
{
	"id": 1,
	"nombre": "Matemáticas 2026",
	"descripcion": "Curso anual de matemáticas.",
	"nota_minima_aprobatoria": 3.0,
	"nota_maxima": 5.0,
	"id_profesor": 1,
	"usa_asistencia": true,
	"peso_asistencia": 0.1,
	...
}
```

### Listar cursos
**GET /cursos**
**Response:**
```json
[
	{
		"id": 1,
		"nombre": "Matemáticas 2026",
		"profesor": { "id": 1, "nombre": "Prof. Juan" },
		...
	}
]
```

## InscripcionController

### Inscribir estudiante
**POST /cursos/1/inscripciones**
```json
{
	"id_estudiante": 2,
	"grupo": "A"
}
```
**Response:**
```json
{
	"id": 1,
	"id_curso": 1,
	"id_estudiante": 2,
	"grupo": "A"
}
```

## TareaController

### Crear tarea
**POST /cursos/1/tareas**
```json
{
	"nombre": "Examen Parcial",
	"descripcion": "Primer examen del año",
	"tipo": "manual",
	"porcentaje": 30,
	"fecha_limite": "2026-06-01"
}
```
**Response:**
```json
{
	"id": 1,
	"id_curso": 1,
	"nombre": "Examen Parcial",
	...
}
```

## NotaController

### Registrar nota
**POST /tareas/1/notas**
```json
{
	"id_estudiante": 2,
	"nota": 4.5,
	"feedback": "Buen trabajo"
}
```
**Response:**
```json
{
	"id": 1,
	"id_tarea": 1,
	"id_estudiante": 2,
	"nota": 4.5,
	"feedback": "Buen trabajo"
}
```

### Simulador de notas
**POST /cursos/1/estudiantes/2/simular**
```json
{
	"notas_simuladas": [4.0, 5.0]
}
```
**Response:**
```json
{
	"nota_final_simulada": 4.25
}
```

## ReclamoController

### Crear reclamo
**POST /notas/1/reclamos**
```json
{
	"motivo": "No se consideró una respuesta correcta"
}
```
**Response:**
```json
{
	"id": 1,
	"id_nota": 1,
	"motivo": "No se consideró una respuesta correcta",
	"estado": "pendiente"
}
```
# Documentación de Controladores y Endpoints

## CursoController
- `GET /cursos` — Listar todos los cursos con profesor, tareas e inscripciones.
- `GET /cursos/{id}` — Mostrar detalles de un curso, tareas, inscripciones, asistencias y resumen de rendimiento.
- `POST /cursos` — Crear un nuevo curso (requiere datos y profesor).
- `PUT /cursos/{id}` — Actualizar un curso existente.
- `DELETE /cursos/{id}` — Eliminar un curso.
- `GET /cursos/{id}/export` — Exportar datos del curso (inscripciones y notas).

## InscripcionController
- `GET /cursos/{cursoId}/inscripciones` — Listar inscripciones de un curso.
- `POST /cursos/{cursoId}/inscripciones` — Inscribir estudiante manualmente.
- `POST /cursos/{cursoId}/inscripciones/import` — Importar inscripciones desde archivo Excel.
- `DELETE /inscripciones/{id}` — Eliminar inscripción.
- `GET /cursos/{cursoId}/inscripciones/export` — Exportar inscripciones de un curso.

## TareaController
- `GET /cursos/{cursoId}/tareas` — Listar tareas de un curso con notas.
- `GET /tareas/{id}` — Mostrar tarea con notas y rúbricas.
- `POST /cursos/{cursoId}/tareas` — Crear una nueva tarea.
- `PUT /tareas/{id}` — Actualizar una tarea existente.
- `DELETE /tareas/{id}` — Eliminar una tarea.
- `GET /cursos/{cursoId}/tareas/export` — Exportar tareas y notas de un curso.

## RubricaController
- `GET /tareas/{tareaId}/rubricas` — Listar rúbricas de una tarea con criterios y niveles.
- `GET /rubricas/{id}` — Mostrar una rúbrica específica.
- `POST /tareas/{tareaId}/rubricas` — Crear una nueva rúbrica.
- `PUT /rubricas/{id}` — Actualizar una rúbrica existente.
- `DELETE /rubricas/{id}` — Eliminar una rúbrica.
- `GET /tareas/{tareaId}/rubricas/export` — Exportar rúbricas y criterios de una tarea.

## CriterioController
- `GET /rubricas/{rubricaId}/criterios` — Listar criterios de una rúbrica con niveles.
- `GET /criterios/{id}` — Mostrar un criterio específico.
- `POST /rubricas/{rubricaId}/criterios` — Crear un nuevo criterio.
- `PUT /criterios/{id}` — Actualizar un criterio existente.
- `DELETE /criterios/{id}` — Eliminar un criterio.
- `GET /rubricas/{rubricaId}/criterios/export` — Exportar criterios y niveles de una rúbrica.

## NivelCriterioController
- `GET /criterios/{criterioId}/niveles` — Listar niveles de un criterio.
- `GET /niveles/{id}` — Mostrar un nivel específico.
- `POST /criterios/{criterioId}/niveles` — Crear un nuevo nivel.
- `PUT /niveles/{id}` — Actualizar un nivel existente.
- `DELETE /niveles/{id}` — Eliminar un nivel.
- `GET /criterios/{criterioId}/niveles/export` — Exportar niveles de un criterio.

## NotaController
- `GET /tareas/{tareaId}/notas` — Listar notas de una tarea.
- `GET /notas/{id}` — Mostrar una nota específica con feedback e historial.
- `POST /tareas/{tareaId}/notas` — Registrar una nueva nota.
- `PUT /notas/{id}` — Actualizar una nota existente.
- `DELETE /notas/{id}` — Eliminar una nota.
- `POST /notas/{id}/feedback` — Registrar feedback para una nota.
- `GET /cursos/{cursoId}/estudiantes/{estudianteId}/promedio` — Calcular promedio y nota final de un estudiante en un curso.
- `POST /cursos/{cursoId}/estudiantes/{estudianteId}/simular` — Simulador de notas.
- `GET /tareas/{tareaId}/notas/export` — Exportar notas de una tarea.

## AsistenciaController
- `GET /cursos/{cursoId}/asistencias` — Listar asistencias de un curso.
- `GET /cursos/{cursoId}/estudiantes/{estudianteId}/asistencias` — Listar asistencias de un estudiante en un curso.
- `POST /cursos/{cursoId}/asistencias` — Registrar una nueva asistencia.
- `PUT /asistencias/{id}` — Actualizar una asistencia existente.
- `DELETE /asistencias/{id}` — Eliminar una asistencia.
- `GET /cursos/{cursoId}/asistencias/export` — Exportar asistencias de un curso.

## ReclamoController
- `GET /estudiantes/{estudianteId}/reclamos` — Listar reclamos de un estudiante.
- `GET /notas/{notaId}/reclamos` — Listar reclamos de una nota.
- `POST /notas/{notaId}/reclamos` — Crear un nuevo reclamo.
- `POST /reclamos/{id}/responder` — Responder un reclamo.
- `POST /reclamos/{id}/cerrar` — Cerrar un reclamo.
- `DELETE /reclamos/{id}` — Eliminar un reclamo.
- `GET /estudiantes/{estudianteId}/reclamos/export` — Exportar reclamos de un estudiante.
