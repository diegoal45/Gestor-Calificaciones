<template>
  <div class="tareas-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="fw-bold mb-1">Gestión de Tareas</h4>
        <p class="text-muted small m-0">Administra las evaluaciones del curso</p>
      </div>
      <div class="d-flex gap-2">
        <button type="button" class="btn btn-light border d-flex align-items-center gap-2 fw-medium" @click="exportModal?.open()">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
          Exportar planilla
        </button>
        <button class="btn btn-primary-custom d-flex align-items-center gap-2 fw-bold" @click="abrirModal('crear')">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
          Nueva Tarea
        </button>
      </div>
    </div>

    <!-- Error State -->
    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <!-- Lista de Tareas -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
      <div v-if="loading" class="p-5 text-center">
        <div class="spinner-border text-primary" role="status"></div>
      </div>
      
      <div v-else-if="tareas.length === 0" class="p-5 text-center text-muted">
        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-3 opacity-50"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
        <h5>No hay tareas creadas</h5>
        <p>Comienza creando la primera evaluación del curso.</p>
      </div>

      <div v-else class="table-responsive">
        <table class="table table-hover align-middle mb-0 custom-table">
          <thead class="table-light">
            <tr>
              <th class="px-4 py-3 text-muted fw-medium">Nombre de la Tarea</th>
              <th class="py-3 text-muted fw-medium">Fecha Límite</th>
              <th class="py-3 text-muted fw-medium text-center">Peso (%)</th>
              <th class="py-3 text-muted fw-medium text-center">Promedio</th>
              <th class="py-3 text-muted fw-medium">Tipo</th>
              <th class="px-4 py-3 text-end text-muted fw-medium">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="tarea in tareas" :key="tarea.id" class="transition">
              <td class="px-4 py-3">
                <div class="fw-bold text-dark">{{ tarea.nombre }}</div>
                <div class="small text-muted text-truncate" style="max-width: 250px;">{{ tarea.descripcion || 'Sin descripción' }}</div>
              </td>
              <td class="py-3">
                <div class="d-flex align-items-center gap-2" :class="isPast(tarea.fecha_limite) ? 'text-danger fw-medium' : 'text-dark'">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                  {{ formatDate(tarea.fecha_limite) }}
                </div>
              </td>
              <td class="py-3 text-center fw-bold text-primary">{{ tarea.porcentaje }}%</td>
              <td class="py-3 text-center">
                <span class="badge" :class="getBadgeColor(tarea.promedio)">{{ tarea.promedio || 'N/A' }}</span>
              </td>
              <td class="py-3">
                <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1">
                  <svg v-if="(tarea.tipo || '').toLowerCase() === 'rubrica'" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                  {{ (tarea.tipo || '').toLowerCase() === 'rubrica' ? 'Rúbrica' : 'Manual' }}
                </span>
              </td>
              <td class="px-4 py-3 text-end">
                <div class="btn-group">
                  <button class="btn btn-sm btn-light border text-muted hover-primary" @click="irACalificar(tarea.id)" title="Calificar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                  </button>
                  <button class="btn btn-sm btn-light border text-muted hover-info" @click="abrirModal('editar', tarea)" title="Editar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                  </button>
                  <button class="btn btn-sm btn-light border text-muted hover-danger" @click="eliminarTarea(tarea.id)" title="Eliminar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Formulario Modal Simulado (Overlay) -->
    <div v-if="showModal" class="modal-overlay d-flex align-items-center justify-content-center z-3">
      <div class="card border-0 shadow-lg rounded-4 modal-content-custom">
        <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center rounded-top-4">
          <h5 class="fw-bold m-0">{{ modalMode === 'crear' ? 'Nueva Tarea' : 'Editar Tarea' }}</h5>
          <button class="btn-close shadow-none" @click="cerrarModal"></button>
        </div>
        <div class="card-body p-4">
          <form @submit.prevent="guardarTarea">
            <div class="mb-3">
              <label class="form-label fw-medium text-muted small">Nombre de la Tarea</label>
              <input v-model="formData.nombre" type="text" class="form-control form-control-lg custom-input" placeholder="Ej: Parcial 1" required>
            </div>
            
            <div class="mb-3">
              <label class="form-label fw-medium text-muted small">Descripción</label>
              <textarea v-model="formData.descripcion" class="form-control custom-input" rows="2" placeholder="Instrucciones para los estudiantes..." required></textarea>
            </div>
            
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label class="form-label fw-medium text-muted small">Fecha Límite</label>
                <input v-model="formData.fecha_limite" type="date" class="form-control custom-input" required>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-medium text-muted small d-flex justify-content-between">
                  <span>Peso / Porcentaje (%)</span>
                  <span class="text-primary">Disponible: {{ porcentajeDisponible }}%</span>
                </label>
                <div class="small text-muted mb-2" v-if="cursoConfig.usa_asistencia">
                  {{ cursoConfig.peso_asistencia }}% esta reservado para asistencia del curso.
                </div>
                <div class="form-check form-switch mb-2">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    id="usarPonderacion"
                    v-model="usaPonderacion"
                    :disabled="(cursoConfig.metodo_calificacion || 'ponderacion') === 'promedio'"
                  >
                  <label class="form-check-label small text-muted" for="usarPonderacion">
                    Usar ponderacion en nota final
                  </label>
                </div>
                <div class="small text-muted mb-2" v-if="(cursoConfig.metodo_calificacion || 'ponderacion') === 'promedio'">
                  Este curso usa <strong>promedio simple</strong>, así que las tareas no manejan porcentajes.
                </div>
                <input
                  v-model="formData.porcentaje"
                  type="number"
                  min="0"
                  :max="Math.max(0, porcentajeDisponible)"
                  class="form-control custom-input text-end"
                  :disabled="(cursoConfig.metodo_calificacion || 'ponderacion') === 'promedio' || !usaPonderacion"
                  :required="usaPonderacion"
                >
                <div class="small text-muted mt-1" v-if="!usaPonderacion">
                  Esta tarea no sumara porcentaje al promedio final.
                </div>
              </div>
            </div>

            <!-- Selector Grande de Tipo de Evaluación -->
            <div class="mb-4">
              <label class="form-label fw-medium text-muted small mb-2">Tipo de Evaluación</label>
              <div class="row g-3">
                <div class="col-6">
                  <label class="type-selector-label d-block cursor-pointer">
                    <input type="radio" v-model="formData.tipo" value="manual" class="d-none">
                    <div class="card border rounded-3 p-3 text-center transition" :class="formData.tipo === 'manual' ? 'border-primary bg-primary-light' : 'border-light-subtle'">
                      <svg class="mb-2" :class="formData.tipo === 'manual' ? 'text-primary' : 'text-muted'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                      <div class="fw-bold" :class="formData.tipo === 'manual' ? 'text-primary' : 'text-dark'">Manual</div>
                      <div class="small text-muted mt-1">Ingreso directo</div>
                    </div>
                  </label>
                </div>
                <div class="col-6">
                  <label class="type-selector-label d-block cursor-pointer">
                    <input type="radio" v-model="formData.tipo" value="rubrica" class="d-none">
                    <div class="card border rounded-3 p-3 text-center transition" :class="formData.tipo === 'rubrica' ? 'border-primary bg-primary-light' : 'border-light-subtle'">
                      <svg class="mb-2" :class="formData.tipo === 'rubrica' ? 'text-primary' : 'text-muted'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                      <div class="fw-bold" :class="formData.tipo === 'rubrica' ? 'text-primary' : 'text-dark'">Rúbrica</div>
                      <div class="small text-muted mt-1">Cálculo asistido</div>
                    </div>
                  </label>
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
              <button type="button" class="btn btn-light fw-medium px-4" @click="cerrarModal">Cancelar</button>
              <button type="submit" class="btn btn-primary-custom fw-bold px-4" :disabled="saving">
                <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
                {{ modalMode === 'crear' ? 'Crear Tarea' : 'Guardar Cambios' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <ExportPlanillaModal ref="exportModal" :curso-id="cursoId" />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { apiRequest } from '../../../api.js'
import ExportPlanillaModal from '../../../components/ExportPlanillaModal.vue'

const route = useRoute()
const router = useRouter()
const cursoId = route.params.id

const exportModal = ref(null)
const loading = ref(true)
const error = ref('')
const tareas = ref([])

// Form State
const showModal = ref(false)
const modalMode = ref('crear') // 'crear' o 'editar'
const saving = ref(false)
const formData = ref({
  id: null,
  nombre: '',
  descripcion: '',
  fecha_limite: '',
  porcentaje: 10,
  tipo: 'manual'
})
const usaPonderacion = ref(true)
const cursoConfig = ref({ usa_asistencia: false, peso_asistencia: 0, metodo_calificacion: 'ponderacion' })

const porcentajeAsignado = computed(() => {
  const currentId = formData.value.id
  return (tareas.value || []).reduce((acc, t) => {
    if (currentId && t.id === currentId) return acc
    const p = Number(t.porcentaje || 0)
    return acc + (Number.isNaN(p) ? 0 : p)
  }, 0)
})

const porcentajeDisponible = computed(() => {
  const reservadoAsistencia = cursoConfig.value.usa_asistencia ? Number(cursoConfig.value.peso_asistencia || 0) : 0
  const restante = 100 - reservadoAsistencia - porcentajeAsignado.value
  return restante > 0 ? Number(restante.toFixed(2)) : 0
})

onMounted(async () => {
  await loadTareas()
})

async function loadTareas() {
  loading.value = true
  try {
    const [res, cursoRes] = await Promise.all([
      apiRequest(`/api/tareas/curso/${cursoId}`),
      apiRequest(`/api/cursos/${cursoId}`)
    ])
    tareas.value = res || []
    const curso = cursoRes?.curso || cursoRes
    cursoConfig.value = {
      usa_asistencia: !!curso?.usa_asistencia,
      peso_asistencia: Number(curso?.peso_asistencia || 0),
      metodo_calificacion: String(curso?.metodo_calificacion || 'ponderacion').toLowerCase(),
    }
  } catch (e) {
    console.error("Error cargando Tareas", e)
    error.value = "No se pudieron cargar las tareas: " + e.message
  } finally {
    loading.value = false
  }
}

function abrirModal(mode, tarea = null) {
  modalMode.value = mode
  if (mode === 'editar' && tarea) {
    formData.value = { ...tarea }
    usaPonderacion.value = cursoConfig.value.metodo_calificacion === 'promedio'
      ? false
      : (Number(tarea.porcentaje || 0) > 0)
  } else {
    formData.value = { id: null, nombre: '', descripcion: '', fecha_limite: '', porcentaje: 0, tipo: 'manual' }
    usaPonderacion.value = false
  }
  showModal.value = true
}

function cerrarModal() {
  showModal.value = false
}

async function guardarTarea() {
  saving.value = true
  error.value = ''
  try {
    const payload = { ...formData.value }
    const metodo = String(cursoConfig.value.metodo_calificacion || 'ponderacion').toLowerCase()
    if (metodo === 'promedio') {
      // En promedio simple, los porcentajes no aplican.
      payload.porcentaje = 0
      usaPonderacion.value = false
    } else {
    if (usaPonderacion.value) {
      const porcentaje = Number(payload.porcentaje)
      if (Number.isNaN(porcentaje) || porcentaje <= 0) {
        throw new Error('Debes ingresar una ponderacion valida mayor a 0.')
      }
      if (porcentaje > porcentajeDisponible.value) {
        throw new Error(`La ponderacion supera el restante disponible (${porcentajeDisponible.value}%).`)
      }
      payload.porcentaje = porcentaje
    } else {
      payload.porcentaje = 0
    }
    }

    const endpoint = modalMode.value === 'crear' 
      ? `/api/tareas/curso/${cursoId}` 
      : `/api/tareas/${formData.value.id}`
    const method = modalMode.value === 'crear' ? 'POST' : 'PUT'
    
    await apiRequest(endpoint, method, payload)
    await loadTareas()
    cerrarModal()
  } catch (e) {
    console.error("Error guardando tarea", e)
    error.value = "No se pudo guardar la tarea: " + e.message
  } finally {
    saving.value = false
  }
}

async function eliminarTarea(id) {
  if(!confirm('¿Estás seguro de eliminar esta tarea?')) return
  error.value = ''
  try {
    await apiRequest(`/api/tareas/${id}`, 'DELETE')
    await loadTareas()
  } catch(e) {
    console.error("Error eliminando tarea", e)
    error.value = "No se pudo eliminar la tarea: " + e.message
  }
}

function irACalificar(tareaId) {
  router.push(`/curso/${cursoId}/calificar?tarea=${tareaId}`)
}

// Utils
function formatDate(dateString) {
  if(!dateString) return 'Sin fecha'
  const options = { year: 'numeric', month: 'short', day: 'numeric' }
  return new Date(dateString).toLocaleDateString('es-ES', options)
}

function isPast(dateString) {
  if(!dateString) return false
  return new Date(dateString) < new Date()
}

function getBadgeColor(promedio) {
  if(!promedio) return 'bg-light text-muted border'
  const val = parseFloat(promedio)
  if (val >= 4.0) return 'bg-success text-white'
  if (val >= 3.0) return 'bg-warning text-dark'
  return 'bg-danger text-white'
}
</script>

<style scoped>
.btn-primary-custom {
  background-color: #1e40af;
  color: white;
  border: none;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.btn-primary-custom:hover {
  background-color: #1e3a8a;
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(30, 64, 175, 0.2);
}

.custom-table th {
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.hover-primary:hover { color: #1e40af !important; border-color: #1e40af !important; background-color: #eff6ff !important; }
.hover-info:hover { color: #0ea5e9 !important; border-color: #0ea5e9 !important; background-color: #f0f9ff !important; }
.hover-danger:hover { color: #ef4444 !important; border-color: #ef4444 !important; background-color: #fef2f2 !important; }

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(4px);
}

.modal-content-custom {
  width: 100%;
  max-width: 600px;
  animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.custom-input {
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
}
.custom-input:focus {
  background-color: #ffffff;
  border-color: #1e40af;
  box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
}

.bg-primary-light { background-color: #eff6ff; }
.cursor-pointer { cursor: pointer; }
.transition { transition: all 0.2s ease; }
</style>
