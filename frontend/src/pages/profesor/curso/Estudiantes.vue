<template>
  <div class="estudiantes-container d-flex h-100 position-relative overflow-hidden">
    <!-- Contenido Principal: Tabla de Estudiantes -->
    <div class="flex-grow-1 d-flex flex-column transition-all" :class="{'pe-4 me-4 border-end': selectedEstudiante}" style="transition: all 0.3s ease;">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h4 class="fw-bold mb-1">Directorio de Estudiantes</h4>
          <p class="text-muted small m-0">{{ estudiantes.length }} estudiantes inscritos</p>
        </div>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-light border d-flex align-items-center gap-2 fw-medium" @click="exportModal?.open()">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            Exportar
          </button>
          <div class="position-relative">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="position-absolute ms-3" style="top: 50%; transform: translateY(-50%)"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input type="text" class="form-control form-control-sm search-input" placeholder="Buscar estudiante..." v-model="searchQuery">
          </div>
        </div>
      </div>

      <!-- Tabla -->
      <div class="card border-0 shadow-sm rounded-4 flex-grow-1 overflow-hidden">
        <div v-if="loading" class="p-5 text-center">
          <div class="spinner-border text-primary" role="status"></div>
        </div>
        
        <div v-else class="table-responsive h-100">
          <table class="table table-hover align-middle mb-0 custom-table">
            <thead class="table-light sticky-top z-1">
              <tr>
                <th class="px-4 py-3 text-muted fw-medium border-0">Estudiante</th>
                <th class="py-3 text-muted fw-medium border-0">Correo</th>
                <th class="py-3 text-muted fw-medium border-0">Grupo</th>
                <th class="py-3 text-muted fw-medium text-center border-0">Promedio</th>
                <th class="py-3 text-muted fw-medium border-0">Estado</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(estudiante, idx) in filteredEstudiantes" :key="estudiante.id" 
                  class="cursor-pointer transition" 
                  :class="{'bg-primary-light': selectedEstudiante?.id === estudiante.id}"
                  @click="abrirPerfil(estudiante)">
                <td class="px-4 py-3">
                  <div class="d-flex align-items-center gap-3">
                    <div class="avatar-sm rounded-circle d-flex justify-content-center align-items-center text-white fw-bold" :class="`bg-color-${idx % 4}`">
                      {{ estudiante.nombre.charAt(0) }}
                    </div>
                    <div class="fw-bold text-dark">{{ estudiante.nombre }}</div>
                  </div>
                </td>
                <td class="py-3 text-muted small">{{ estudiante.email || '-' }}</td>
                <td class="py-3"><span class="badge bg-light text-dark border">{{ estudiante.grupo }}</span></td>
                <td class="py-3 text-center fw-bold" :class="getGradeTextColor(estudiante.promedio)">
                  {{ estudiante.promedio || '-' }}
                </td>
                <td class="py-3">
                  <span class="badge rounded-pill px-3 py-2 fw-medium" :class="getEstadoBadge(estudiante.estado)">
                    {{ estudiante.estado }}
                  </span>
                </td>
              </tr>
              <tr v-if="filteredEstudiantes.length === 0">
                <td colspan="5" class="text-center py-5 text-muted">No se encontraron estudiantes</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Panel Lateral: Perfil Académico -->
    <aside v-if="selectedEstudiante" class="profile-sidebar bg-white rounded-4 shadow-sm h-100 overflow-auto d-flex flex-column animation-slide-in" style="width: 400px; min-width: 400px;">
      <!-- Header Perfil -->
      <div class="p-4 border-bottom position-relative banner-color-0 text-white rounded-top-4">
        <button class="btn btn-sm btn-light position-absolute top-0 end-0 m-3 rounded-circle p-1 opacity-75 hover-opacity-100" @click="cerrarPerfil">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
        
        <div class="d-flex flex-column align-items-center text-center mt-3">
          <div class="avatar-lg rounded-circle bg-white text-primary fw-bold d-flex justify-content-center align-items-center shadow-sm mb-3" style="font-size: 2rem;">
            {{ selectedEstudiante.nombre.charAt(0) }}
          </div>
          <h4 class="fw-bold mb-0">{{ selectedEstudiante.nombre }}</h4>
          <p class="opacity-75 small m-0">{{ selectedEstudiante.email || '-' }}</p>
        </div>
        <div class="abstract-pattern"></div>
      </div>

      <!-- Stats Generales -->
      <div class="p-4 border-bottom d-flex justify-content-around text-center bg-light">
        <div>
          <div class="small text-muted fw-medium text-uppercase">Promedio</div>
          <div class="fs-3 fw-bold" :class="getGradeTextColor(selectedEstudiante.promedio)">{{ selectedEstudiante.promedio }}</div>
        </div>
        <div class="border-start"></div>
        <div>
          <div class="small text-muted fw-medium text-uppercase">Riesgo</div>
          <div class="mt-2">
            <span class="badge" :class="selectedEstudiante.riesgo === 'Alto' ? 'bg-danger' : selectedEstudiante.riesgo === 'Medio' ? 'bg-warning text-dark' : 'bg-success'">{{ selectedEstudiante.riesgo || 'Bajo' }}</span>
          </div>
        </div>
      </div>

      <!-- Body Perfil -->
      <div class="p-4 flex-grow-1">
        <h6 class="fw-bold mb-3 text-muted">Historial de Tareas</h6>
        
        <div v-if="loadingPerfil" class="text-center py-4">
          <div class="spinner-border spinner-border-sm text-primary"></div>
        </div>
        
        <div v-else class="task-timeline">
          <div v-for="tarea in selectedEstudiante.tareas" :key="tarea.id" class="timeline-item pb-4 position-relative">
            <div class="timeline-dot position-absolute bg-white border border-2 rounded-circle" :class="`border-${getGradeColorWord(tarea.nota)}`"></div>
            <div class="ms-4 card border border-light-subtle shadow-sm">
              <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <div class="fw-bold">{{ tarea.nombre }}</div>
                  <div class="fw-bold" :class="getGradeTextColor(tarea.nota)">{{ tarea.nota || '-' }}</div>
                </div>
                <p v-if="tarea.feedback" class="small text-muted m-0 bg-light p-2 rounded border-start border-3" :class="`border-${getGradeColorWord(tarea.nota)}`">
                  "{{ tarea.feedback }}"
                </p>
                <div v-else class="small text-muted fst-italic">Sin observaciones.</div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </aside>

    <ExportPlanillaModal ref="exportModal" :curso-id="cursoId" />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { apiRequest } from '../../../api.js'
import ExportPlanillaModal from '../../../components/ExportPlanillaModal.vue'

const route = useRoute()
const cursoId = route.params.id

const loading = ref(true)
const error = ref('')
const estudiantes = ref([])
const searchQuery = ref('')

const selectedEstudiante = ref(null)
const loadingPerfil = ref(false)
const exportModal = ref(null)

onMounted(async () => {
  await loadEstudiantes()
})

async function loadEstudiantes() {
  loading.value = true
  try {
    const res = await apiRequest(`/api/cursos/${cursoId}/estudiantes`)
    estudiantes.value = res || []
  } catch (e) {
    console.error("Error cargando estudiantes", e)
    error.value = "No se pudieron cargar los estudiantes: " + e.message
  } finally {
    loading.value = false
  }
}

const filteredEstudiantes = computed(() => {
  if (!searchQuery.value) return estudiantes.value
  const q = searchQuery.value.toLowerCase()
  return estudiantes.value.filter(e =>
    (e.nombre || '').toLowerCase().includes(q) ||
    (e.email || '').toLowerCase().includes(q)
  )
})

async function abrirPerfil(est) {
  selectedEstudiante.value = est
  loadingPerfil.value = true
  try {
    const res = await apiRequest(`/api/estudiantes/${est.id}/perfil?curso_id=${cursoId}`)
    selectedEstudiante.value.tareas = res.tareas || []
  } catch(e) {
    console.error('Error cargando perfil', e)
    selectedEstudiante.value.tareas = []
    error.value = 'No se pudo cargar el historial de tareas: ' + e.message
  } finally {
    loadingPerfil.value = false
  }
}

function cerrarPerfil() {
  selectedEstudiante.value = null
}

// Utils
function getGradeTextColor(val) {
  if(!val || val === '-') return 'text-muted'
  const nota = parseFloat(val)
  if (nota >= 4.0) return 'text-success'
  if (nota >= 3.0) return 'text-warning'
  return 'text-danger'
}

function getGradeColorWord(val) {
  if(!val || val === '-') return 'secondary'
  const nota = parseFloat(val)
  if (nota >= 4.0) return 'success'
  if (nota >= 3.0) return 'warning'
  return 'danger'
}

function getEstadoBadge(estado) {
  switch(estado) {
    case 'Excelente':
    case 'Bueno': return 'bg-success-subtle text-success'
    case 'Normal': return 'bg-light text-dark'
    case 'Riesgo': return 'bg-warning-subtle text-warning-emphasis'
    case 'Crítico': return 'bg-danger-subtle text-danger'
    default: return 'bg-light text-dark'
  }
}
</script>

<style scoped>
.search-input {
  padding-left: 2.2rem;
  border-radius: 20px;
  width: 250px;
}

.custom-table th {
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.avatar-sm { width: 32px; height: 32px; }
.avatar-lg { width: 80px; height: 80px; z-index: 2; position: relative; }

.bg-color-0 { background-color: #3b82f6; }
.bg-color-1 { background-color: #10b981; }
.bg-color-2 { background-color: #8b5cf6; }
.bg-color-3 { background-color: #f43f5e; }

.bg-primary-light { background-color: #eff6ff !important; }

/* Timeline */
.task-timeline {
  position: relative;
}
.task-timeline::before {
  content: '';
  position: absolute;
  top: 0;
  bottom: 0;
  left: 7px;
  width: 2px;
  background-color: #e2e8f0;
}
.timeline-dot {
  width: 16px;
  height: 16px;
  left: 0;
  top: 4px;
}

.animation-slide-in {
  animation: slideInRight 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes slideInRight {
  from { opacity: 0; transform: translateX(50px); }
  to { opacity: 1; transform: translateX(0); }
}

.banner-color-0 { background: linear-gradient(135deg, #2563eb, #1e40af); overflow: hidden; }
.abstract-pattern {
  position: absolute;
  top: -50px;
  right: -50px;
  width: 150px;
  height: 150px;
  background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 70%);
  border-radius: 50%;
  z-index: 0;
}
.hover-opacity-100:hover { opacity: 1 !important; }
</style>
