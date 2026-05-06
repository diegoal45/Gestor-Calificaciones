# Documentación de Modelos y Relaciones

Este documento describe los modelos Eloquent de la aplicación y sus relaciones principales.

---

## Usuario
- **Tabla:** usuarios
- **Atributos:** nombre, email, password, rol
- **Relaciones:**
  - `cursos()`: Un usuario (profesor) tiene muchos cursos
  - `inscripciones()`: Un usuario (estudiante) tiene muchas inscripciones
  - `notas()`: Un usuario (estudiante) tiene muchas notas
  - `reclamos()`: Un usuario (estudiante) tiene muchos reclamos

## Curso
- **Tabla:** cursos
- **Atributos:** nombre, descripcion, id_profesor, nota_minima_aprobatoria, nota_maxima, usa_asistencia, peso_asistencia
- **Relaciones:**
  - `profesor()`: Pertenece a un usuario (profesor)
  - `tareas()`: Tiene muchas tareas
  - `inscripciones()`: Tiene muchas inscripciones
  - `asistencias()`: Tiene muchas asistencias

## Inscripcion
- **Tabla:** inscripciones
- **Atributos:** id_curso, id_estudiante, grupo
- **Relaciones:**
  - `curso()`: Pertenece a un curso
  - `estudiante()`: Pertenece a un usuario (estudiante)

## Tarea
- **Tabla:** tareas
- **Atributos:** id_curso, nombre, descripcion, porcentaje, tipo, fecha_limite
- **Relaciones:**
  - `curso()`: Pertenece a un curso
  - `notas()`: Tiene muchas notas
  - `rubricas()`: Tiene muchas rúbricas

## Nota
- **Tabla:** notas
- **Atributos:** id_tarea, id_estudiante, nota, feedback
- **Relaciones:**
  - `tarea()`: Pertenece a una tarea
  - `estudiante()`: Pertenece a un usuario (estudiante)
  - `historialNotas()`: Tiene muchos historiales de nota
  - `evaluacionesRubrica()`: Tiene muchas evaluaciones de rúbrica

## HistorialNota
- **Tabla:** historial_notas
- **Atributos:** id_nota, nota_anterior, nota_nueva, changed_at
- **Relaciones:**
  - `nota()`: Pertenece a una nota

## Reclamo
- **Tabla:** reclamos
- **Atributos:** id_nota, id_estudiante, mensaje, respuesta, estado
- **Relaciones:**
  - `nota()`: Pertenece a una nota
  - `estudiante()`: Pertenece a un usuario (estudiante)

## Rubrica
- **Tabla:** rubricas
- **Atributos:** id_tarea, nombre
- **Relaciones:**
  - `tarea()`: Pertenece a una tarea
  - `criterios()`: Tiene muchos criterios

## Criterio
- **Tabla:** criterios
- **Atributos:** id_rubrica, nombre, peso
- **Relaciones:**
  - `rubrica()`: Pertenece a una rúbrica
  - `niveles()`: Tiene muchos niveles de criterio
  - `evaluacionesRubrica()`: Tiene muchas evaluaciones de rúbrica

## NivelCriterio
- **Tabla:** niveles_criterio
- **Atributos:** id_criterio, nombre, valor
- **Relaciones:**
  - `criterio()`: Pertenece a un criterio
  - `evaluacionesRubrica()`: Tiene muchas evaluaciones de rúbrica

## EvaluacionRubrica
- **Tabla:** evaluaciones_rubrica
- **Atributos:** id_nota, id_criterio, id_nivel
- **Relaciones:**
  - `nota()`: Pertenece a una nota
  - `criterio()`: Pertenece a un criterio
  - `nivel()`: Pertenece a un nivel de criterio

## Asistencia
- **Tabla:** asistencia
- **Atributos:** id_curso, id_estudiante, fecha, estado
- **Relaciones:**
  - `curso()`: Pertenece a un curso
  - `estudiante()`: Pertenece a un usuario (estudiante)

---

> **Nota:** El modelo `User` es el modelo de autenticación de Laravel y no tiene relaciones personalizadas en este contexto.
