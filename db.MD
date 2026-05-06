🧠 MODELO GENERAL

Relación principal:

usuarios → cursos → inscripciones → tareas → notas
                           ↘
                        asistencia

Rúbricas:

rubricas → criterios → niveles
                     ↘
              evaluaciones_rubrica
🗄️ TABLAS DEFINIDAS
👤 1. usuarios

(Usa base de Laravel + roles simples)

usuarios
- id (PK)
- nombre
- email (unique)
- password
- rol ENUM('profesor','estudiante')
- created_at
- updated_at
📚 2. cursos
cursos
- id (PK)
- nombre
- descripcion
- id_profesor (FK → usuarios.id)
- nota_minima_aprobatoria DECIMAL(5,2)
- nota_maxima DECIMAL(5,2)
- usa_asistencia BOOLEAN
- peso_asistencia DECIMAL(5,2) NULL
- created_at
- updated_at
🧾 3. inscripciones

(Relación muchos a muchos)

inscripciones
- id (PK)
- id_curso (FK)
- id_estudiante (FK → usuarios.id)
- grupo VARCHAR NULL
- created_at
📝 4. tareas
tareas
- id (PK)
- id_curso (FK)
- nombre
- descripcion
- porcentaje DECIMAL(5,2)  -- peso en el curso
- tipo ENUM('manual','rubrica')
- fecha_limite DATE NULL
- created_at
📊 5. notas
notas
- id (PK)
- id_tarea (FK)
- id_estudiante (FK)
- nota DECIMAL(5,2)
- feedback TEXT NULL
- created_at
- updated_at

👉 Para rúbricas, esta nota es el resultado final calculado

🧩 RÚBRICAS
📋 6. rubricas
rubricas
- id (PK)
- id_tarea (FK)
- nombre
- created_at
🎯 7. criterios
criterios
- id (PK)
- id_rubrica (FK)
- nombre
- peso DECIMAL(5,2)
📏 8. niveles_criterio
niveles_criterio
- id (PK)
- id_criterio (FK)
- nombre
- valor DECIMAL(5,2)

(Ej: Bajo=1, Medio=3, Alto=5)

🧮 9. evaluaciones_rubrica

(Qué nivel obtuvo el estudiante en cada criterio)

evaluaciones_rubrica
- id (PK)
- id_nota (FK → notas.id)
- id_criterio (FK)
- id_nivel (FK)

👉 Desde aquí calculas:

nota = Σ(valor_nivel × peso_criterio)
📅 ASISTENCIA
📆 10. asistencia
asistencia
- id (PK)
- id_curso (FK)
- id_estudiante (FK)
- fecha DATE
- estado ENUM('presente','ausente')

👉 Puedes calcular porcentaje dinámicamente (mejor que guardarlo)

💬 RECLAMOS
⚠️ 11. reclamos
reclamos
- id (PK)
- id_nota (FK)
- id_estudiante (FK)
- mensaje TEXT
- respuesta TEXT NULL
- estado ENUM('pendiente','respondido','cerrado')
- created_at
- updated_at
📈 (OPCIONAL PERO PRO) HISTÓRICO / LOG
🧾 12. historial_notas (opcional)
historial_notas
- id
- id_nota
- nota_anterior
- nota_nueva
- changed_at
⚙️ DECISIONES IMPORTANTES (CLAVE)
✅ 1. NO guardar promedio final

Se calcula dinámicamente → más flexible

✅ 2. NO guardar asistencia como porcentaje

Se calcula:

presentes / total_clases
✅ 3. notas es el centro del sistema
Manual → directo
Rúbrica → calculado
✅ 4. porcentajes en tareas

Permiten simulador y cálculos dinámicos

🔗 RELACIONES (RESUMEN)
usuario (profesor) → cursos (1:N)
usuario (estudiante) → inscripciones (1:N)
cursos → tareas (1:N)
tareas → notas (1:N)
tareas → rubricas (1:1 opcional)
rubricas → criterios (1:N)
criterios → niveles (1:N)
notas → evaluaciones_rubrica (1:N)
cursos → asistencia (1:N)
notas → reclamos (1:N)