<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h4 class="fw-bold mb-1">Calificar tarea</h4>
        <p class="text-muted small m-0">El feedback es obligatorio para guardar.</p>
      </div>
      <button class="btn btn-light border" @click="goPlanilla">Volver a planilla</button>
    </div>

    <div v-if="error" class="alert alert-danger">{{ error }}</div>
    <div v-if="loading" class="text-center py-4"><div class="spinner-border text-primary"></div></div>

    <div v-else class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label">Tarea</label>
            <select v-model="selectedTareaId" class="form-select" @change="loadContexto">
              <option v-for="t in tareas" :key="t.id" :value="t.id">{{ t.nombre }} ({{ t.tipo }})</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Estudiante</label>
            <select v-model="selectedEstudianteId" class="form-select">
              <option v-for="e in estudiantes" :key="e.id" :value="e.id">{{ e.nombre }}</option>
            </select>
          </div>
        </div>

        <div v-if="(tarea?.tipo || '').toLowerCase() === 'rubrica'" class="mb-3">
          <label class="form-label">Rúbrica</label>
          <select v-model="selectedRubricaId" class="form-select mb-2">
            <option :value="null" disabled>Selecciona una rúbrica</option>
            <option v-for="r in (tarea?.rubricas || [])" :key="r.id" :value="r.id">{{ r.nombre }}</option>
          </select>

          <div v-if="rubricaActiva">
            <div v-for="criterio in rubricaActiva.criterios" :key="criterio.id" class="mb-2 border rounded p-2">
              <div class="fw-semibold mb-1">{{ criterio.nombre }} ({{ criterio.peso }}%)</div>
              <select v-model.number="evaluaciones[criterio.id]" class="form-select mb-1">
                <option :value="null" disabled>Selecciona nivel</option>
                <option v-for="nivel in criterio.niveles" :key="nivel.id" :value="nivel.id">
                  {{ nivel.nombre }}
                </option>
              </select>
              <div class="row g-2">
                <div class="col-md-4">
                  <label class="small text-muted">Logro (%)</label>
                  <input v-model.number="porcentajes[criterio.id]" type="number" min="0" max="100" class="form-control form-control-sm" />
                </div>
                <div class="col-md-8">
                  <label class="small text-muted">Descripción</label>
                  <div class="small">
                    {{ descripcionNivel(criterio, evaluaciones[criterio.id]) }}
                  </div>
                </div>
              </div>
              <div v-if="criterio.niveles && criterio.niveles.length" class="small text-muted">
                <span v-for="nivel in criterio.niveles" :key="nivel.id + '-desc'">
                  <strong>{{ nivel.nombre }}:</strong> {{ nivel.descripcion || '---' }}<span v-if="!isLastNivel(criterio, nivel)"> · </span>
                </span>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="mb-3">
          <label class="form-label">Nota manual</label>
          <input v-model.number="notaManual" type="number" min="0" max="5" step="0.1" class="form-control" />
        </div>

        <div class="mb-3">
          <label class="form-label">Feedback (obligatorio)</label>
          <textarea v-model="feedback" class="form-control" rows="4" placeholder="Escribe retroalimentación para el estudiante"></textarea>
        </div>

        <div class="d-flex justify-content-end">
          <button class="btn btn-primary" :disabled="saving" @click="guardarCalificacion">Guardar calificación</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { apiRequest } from '../../../api.js'

const route = useRoute()
const router = useRouter()
const cursoId = route.params.id

const loading = ref(true)
const saving = ref(false)
const error = ref('')
const tareas = ref([])
const tarea = ref(null)
const estudiantes = ref([])
const selectedTareaId = ref(null)
const selectedEstudianteId = ref(null)
const selectedRubricaId = ref(null)
const evaluaciones = ref({})
const porcentajes = ref({})
const notaManual = ref(null)
const feedback = ref('')

const rubricaActiva = computed(() => (tarea.value?.rubricas || []).find(r => r.id === Number(selectedRubricaId.value)))

onMounted(async () => {
  await loadTareas()
  if (selectedTareaId.value) {
    await loadContexto()
  }
})

async function loadTareas() {
  loading.value = true
  error.value = ''
  try {
    tareas.value = await apiRequest(`/api/tareas/curso/${cursoId}`)
    const queryTarea = Number(route.query.tarea || 0)
    selectedTareaId.value = queryTarea || tareas.value[0]?.id || null
  } catch (e) {
    error.value = `No se pudieron cargar las tareas: ${e.message}`
  } finally {
    loading.value = false
  }
}

async function loadContexto() {
  if (!selectedTareaId.value) return
  loading.value = true
  error.value = ''
  try {
    const res = await apiRequest(`/api/notas/tarea/${selectedTareaId.value}/contexto-calificacion`)
    tarea.value = res.tarea
    estudiantes.value = res.estudiantes || []
    const queryEst = Number(route.query.estudiante || 0)
    selectedEstudianteId.value = queryEst || estudiantes.value[0]?.id || null
    selectedRubricaId.value = tarea.value?.rubricas?.[0]?.id || null
    evaluaciones.value = {}
    porcentajes.value = {}
    notaManual.value = null
    feedback.value = ''
  } catch (e) {
    error.value = `No se pudo cargar contexto de calificación: ${e.message}`
  } finally {
    loading.value = false
  }
}

async function guardarCalificacion() {
  if (!selectedEstudianteId.value || !selectedTareaId.value) return
  if (!feedback.value.trim()) {
    error.value = 'El feedback es obligatorio.'
    return
  }

  const payload = { feedback: feedback.value.trim() }
  if ((tarea.value?.tipo || '').toLowerCase() === 'rubrica') {
    payload.rubrica_id = selectedRubricaId.value
    payload.evaluaciones = (rubricaActiva.value?.criterios || []).map(c => ({
      id_criterio: c.id,
      id_nivel: evaluaciones.value[c.id],
      porcentaje: porcentajes.value[c.id],
    }))
  } else {
    payload.nota = notaManual.value
  }

  saving.value = true
  error.value = ''
  try {
    await apiRequest(`/api/notas/tarea/${selectedTareaId.value}/estudiante/${selectedEstudianteId.value}/calificar`, 'POST', payload)
    await goPlanilla()
  } catch (e) {
    error.value = `No se pudo guardar: ${e.message}`
  } finally {
    saving.value = false
  }
}

function goPlanilla() {
  router.push(`/curso/${cursoId}/planilla`)
}

function isLastNivel(criterio, nivel) {
  if (!criterio.niveles) return true
  const arr = criterio.niveles
  return arr[arr.length - 1]?.id === nivel.id
}

function descripcionNivel(criterio, nivelId) {
  if (!nivelId) return ''
  const n = (criterio?.niveles || []).find(x => x.id === Number(nivelId))
  return n?.descripcion || ''
}
</script>
