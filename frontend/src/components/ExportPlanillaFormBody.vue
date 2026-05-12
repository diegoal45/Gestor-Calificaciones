<template>
  <div>
    <div v-if="loading" class="text-center py-4">
      <div class="spinner-border text-primary" role="status"></div>
      <p class="text-muted small mt-2 mb-0">Cargando tareas del curso…</p>
    </div>

    <div v-else-if="error" class="alert alert-danger">{{ error }}</div>

    <template v-else>
      <div class="d-flex flex-wrap gap-2 mb-3">
        <button type="button" class="btn btn-sm btn-outline-secondary" @click="$emit('solo-definitiva')">
          Solo definitiva
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary" @click="$emit('seleccionar-todas')">
          Marcar todas las tareas
        </button>
      </div>

      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <div class="form-check">
            <input
              class="form-check-input"
              type="checkbox"
              id="exp-def"
              :checked="incluirDefinitiva"
              @change="$emit('update:incluirDefinitiva', $event.target.checked)"
            />
            <label class="form-check-label fw-medium" for="exp-def">Incluir nota definitiva</label>
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label small text-muted mb-1">Filtro</label>
          <select
            class="form-select form-select-sm"
            :value="filtroRiesgo"
            @change="$emit('update:filtroRiesgo', $event.target.value)"
          >
            <option value="todos">Todos los estudiantes</option>
            <option value="riesgo">Solo en riesgo (definitiva entre 0 y 3)</option>
          </select>
        </div>
      </div>

      <div class="mb-3">
        <div class="text-muted small fw-medium mb-2">Columnas extra</div>
        <div class="d-flex flex-wrap gap-3">
          <div class="form-check">
            <input
              class="form-check-input"
              type="checkbox"
              id="exp-mail"
              :checked="incluirEmail"
              @change="$emit('update:incluirEmail', $event.target.checked)"
            />
            <label class="form-check-label" for="exp-mail">Correo</label>
          </div>
          <div class="form-check">
            <input
              class="form-check-input"
              type="checkbox"
              id="exp-grupo"
              :checked="incluirGrupo"
              @change="$emit('update:incluirGrupo', $event.target.checked)"
            />
            <label class="form-check-label" for="exp-grupo">Grupo</label>
          </div>
          <div v-if="usaAsistencia" class="form-check">
            <input
              class="form-check-input"
              type="checkbox"
              id="exp-asist"
              :checked="incluirAsistenciaPct"
              @change="$emit('update:incluirAsistenciaPct', $event.target.checked)"
            />
            <label class="form-check-label" for="exp-asist">Porcentaje de asistencia</label>
          </div>
          <div class="form-check">
            <input
              class="form-check-input"
              type="checkbox"
              id="exp-feedback"
              :checked="incluirFeedback"
              @change="$emit('update:incluirFeedback', $event.target.checked)"
            />
            <label class="form-check-label" for="exp-feedback">Feedback de tareas</label>
          </div>
        </div>
      </div>

      <div class="mb-3">
        <div class="fw-medium mb-2">Tareas (notas por columna)</div>
        <p v-if="tareas.length === 0" class="text-muted small mb-0">No hay tareas en este curso.</p>
        <div v-else class="border rounded-3 p-3 bg-light-subtle" style="max-height: 200px; overflow-y: auto">
          <div v-for="t in tareas" :key="t.id" class="form-check mb-1">
            <input
              class="form-check-input"
              type="checkbox"
              :id="'t-' + t.id"
              :checked="!!tareasChecks[t.id]"
              @change="toggleTarea(t.id, $event.target.checked)"
            />
            <label class="form-check-label small" :for="'t-' + t.id">
              {{ t.nombre }}{{ Number(t.porcentaje || 0) > 0 ? ` (${t.porcentaje}%)` : '' }}
            </label>
          </div>
        </div>
      </div>

      <div class="alert alert-light border small py-2 mb-3">
        <span v-if="!canExport" class="text-danger">
          Activa la nota definitiva o marca al menos una tarea.
        </span>
        <span v-else class="text-muted">Listo para exportar.</span>
      </div>

      <div class="d-flex flex-column flex-sm-row gap-2">
        <button
          type="button"
          class="btn btn-outline-success fw-medium flex-grow-1"
          :disabled="!canExport || !!exporting"
          @click="$emit('download-csv')"
        >
          {{ exporting === 'csv' ? 'Generando…' : 'CSV' }}
        </button>
        <button
          type="button"
          class="btn btn-outline-primary fw-medium flex-grow-1"
          :disabled="!canExport || !!exporting"
          @click="$emit('download-xls')"
        >
          {{ exporting === 'xls' ? 'Generando…' : 'Excel (.xls)' }}
        </button>
        <button
          type="button"
          class="btn btn-outline-danger fw-medium flex-grow-1"
          :disabled="!canExport || !!exporting"
          @click="$emit('export-pdf')"
        >
          {{ exporting === 'pdf' ? 'Abriendo…' : 'PDF (imprimir)' }}
        </button>
      </div>

      <p class="text-muted small mt-3 mb-0">
        En PDF se abre una ventana nueva; usa <strong>Imprimir → Guardar como PDF</strong> en tu navegador.
      </p>
    </template>
  </div>
</template>

<script setup>
defineProps({
  loading: { type: Boolean, default: false },
  error: { type: String, default: '' },
  exporting: { type: String, default: '' },
  tareas: { type: Array, default: () => [] },
  usaAsistencia: { type: Boolean, default: false },
  incluirDefinitiva: { type: Boolean, default: true },
  incluirEmail: { type: Boolean, default: false },
  incluirGrupo: { type: Boolean, default: false },
  incluirAsistenciaPct: { type: Boolean, default: false },
  incluirFeedback: { type: Boolean, default: false },
  filtroRiesgo: { type: String, default: 'todos' },
  tareasChecks: { type: Object, required: true },
  canExport: { type: Boolean, default: false },
})

const emit = defineEmits([
  'solo-definitiva',
  'seleccionar-todas',
  'update:incluirDefinitiva',
  'update:incluirEmail',
  'update:incluirGrupo',
  'update:incluirAsistenciaPct',
  'update:incluirFeedback',
  'update:filtroRiesgo',
  'update:tarea',
  'download-csv',
  'download-xls',
  'export-pdf',
])

function toggleTarea(id, checked) {
  emit('update:tarea', { id, checked })
}
</script>
