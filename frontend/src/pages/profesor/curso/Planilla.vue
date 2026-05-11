<template>
  <div class="planilla-container d-flex flex-column h-100">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="fw-bold mb-1">Planilla de Calificaciones</h4>
        <p class="text-muted small m-0">Consulta promedios y entra al flujo de calificación con feedback obligatorio.</p>
      </div>
      <div class="d-flex gap-2">
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-white border-end-0">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
          </span>
          <input v-model="filtroNombre" type="text" class="form-control border-start-0" placeholder="Filtrar estudiante..." />
        </div>
        <select v-model="filtroTipoTarea" class="form-select form-select-sm w-auto">
          <option value="todas">Todas las tareas</option>
          <option value="manual">Solo manuales</option>
          <option value="rubrica">Solo rúbricas</option>
        </select>
        <button type="button" class="btn btn-light border d-flex align-items-center gap-2 text-muted fw-medium" @click="exportModal?.open()">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
          Exportar
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
      <p class="text-muted mt-3">Cargando planilla...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="alert alert-danger">
      {{ error }}
    </div>

    <!-- Planilla Table -->
    <div v-else class="table-responsive bg-white border rounded-3 shadow-sm flex-grow-1 planilla-wrapper">
      <table class="table table-bordered mb-0 planilla-table">
        <thead class="table-light sticky-top z-3">
          <tr>
            <th class="sticky-col-left z-3 bg-light shadow-right name-col">Estudiante</th>
            <th v-for="tarea in headersFiltrados" :key="tarea.id" class="text-center align-middle" style="min-width: 100px;">
              <div class="text-truncate fw-bold" :title="tarea.nombre">{{ tarea.nombre }}</div>
              <div class="small text-muted fw-normal">{{ Number(tarea.porcentaje || 0) > 0 ? `${tarea.porcentaje}%` : 'Sin ponderacion' }}</div>
            </th>
            <th v-if="cursoConfig.usa_asistencia" class="text-center align-middle" style="min-width: 120px;">
              <div class="fw-bold">Asistencia</div>
              <div class="small text-muted fw-normal">{{ cursoConfig.peso_asistencia }}%</div>
            </th>
            <th class="text-center align-middle bg-light fw-bold sticky-col-right shadow-left" style="min-width: 100px;">
              Promedio Final
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(estudiante, idx) in estudiantesFiltrados" :key="estudiante.id" class="align-middle">
            <!-- Columna Fija: Nombre -->
            <td class="sticky-col-left bg-white shadow-right fw-medium name-col d-flex align-items-center gap-2">
              <div class="avatar-sm rounded-circle d-flex justify-content-center align-items-center text-white small" :class="`bg-color-${idx % 4}`">
                {{ estudiante.nombre.charAt(0) }}
              </div>
              <div class="text-truncate" :title="estudiante.nombre">{{ estudiante.nombre }}</div>
            </td>

            <!-- Celdas de Calificación -->
            <td v-for="tarea in headersFiltrados" :key="tarea.id" class="text-center p-0 position-relative cell-hover">
              <button
                class="btn btn-sm w-100 h-100 border-0 rounded-0 grade-cell d-flex align-items-center justify-content-center fw-bold"
                :class="getGradeColor(getNota(estudiante.id, tarea.id))"
                @click="irACalificar(estudiante.id, tarea.id)"
                :title="'Calificar con feedback obligatorio'"
              >
                {{ getNota(estudiante.id, tarea.id) || '-' }}
              </button>
            </td>

            <td v-if="cursoConfig.usa_asistencia" class="text-center fw-bold">
              <span :class="getGradeColor(getAsistenciaNota(estudiante.id))">
                {{ getAsistenciaNota(estudiante.id).toFixed(1) }}
              </span>
              <div class="small text-muted">{{ getAsistenciaPct(estudiante.id) }}%</div>
            </td>

            <!-- Columna Fija: Promedio Final -->
            <td class="text-center fw-bold sticky-col-right bg-light shadow-left" :class="getGradeColor(calcularPromedio(estudiante.id), true)">
              {{ calcularPromedio(estudiante.id) }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <ExportPlanillaModal ref="exportModal" :curso-id="cursoId" />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { apiRequest } from '../../../api.js'
import ExportPlanillaModal from '../../../components/ExportPlanillaModal.vue'

const props = defineProps(['curso'])
const route = useRoute()
const router = useRouter()
const cursoId = route.params.id

const loading = ref(true)
const error = ref('')
const headers = ref([])
const estudiantes = ref([])
const calificaciones = ref({}) // Mapa { 'estId_tareaId': nota }
const asistenciaMap = ref({}) // Mapa { 'estId': porcentaje asistencia }
const cursoConfig = ref({ usa_asistencia: false, peso_asistencia: 0, nota_maxima: 5, metodo_calificacion: 'ponderacion' })
const filtroNombre = ref('')
const filtroTipoTarea = ref('todas')
const exportModal = ref(null)

onMounted(async () => {
  await loadPlanilla()
})

async function loadPlanilla() {
  loading.value = true
  try {
    // API Call (Simulado/Real según disponibilidad)
    const res = await apiRequest(`/api/cursos/${cursoId}/planilla`)
    headers.value = res.tareas || []
    estudiantes.value = res.estudiantes || []
    cursoConfig.value = res.curso || { usa_asistencia: false, peso_asistencia: 0, nota_maxima: 5, metodo_calificacion: 'ponderacion' }
    
    // Convert array of grades to map for O(1) lookup
    const map = {}
    if (res.calificaciones) {
      res.calificaciones.forEach(c => {
        map[`${c.estudiante_id}_${c.tarea_id}`] = parseFloat(c.nota)
      })
    }
    calificaciones.value = map

    const asistencia = {}
    if (res.asistencia) {
      res.asistencia.forEach(a => {
        asistencia[`${a.estudiante_id}`] = Number(a.porcentaje || 0)
      })
    }
    asistenciaMap.value = asistencia
  } catch (e) {
    console.error("Error cargando planilla:", e)
    error.value = "No se pudo cargar la planilla. " + e.message
  } finally {
    loading.value = false
  }
}

function getNota(estId, tareaId) {
  return calificaciones.value[`${estId}_${tareaId}`]
}

const headersFiltrados = computed(() => {
  if (filtroTipoTarea.value === 'todas') return headers.value
  return headers.value.filter(t => (t.tipo || '').toLowerCase() === filtroTipoTarea.value)
})

const estudiantesFiltrados = computed(() => {
  const texto = filtroNombre.value.trim().toLowerCase()
  if (!texto) return estudiantes.value
  return estudiantes.value.filter(e => (e.nombre || '').toLowerCase().includes(texto))
})

function calcularPromedio(estId) {
  const metodo = String(cursoConfig.value.metodo_calificacion || 'ponderacion').toLowerCase()
  if (metodo === 'promedio') {
    let suma = 0
    let count = 0
    headers.value.forEach(t => {
      const notaRaw = getNota(estId, t.id)
      const nota = Number(notaRaw)
      if (notaRaw === undefined || notaRaw === null || Number.isNaN(nota)) return
      suma += nota
      count++
    })
    if (count <= 0) return '-'

    let promedioBase = suma / count
    if (cursoConfig.value.usa_asistencia) {
      const pesoAsis = Number(cursoConfig.value.peso_asistencia || 0)
      const pesoNotas = Math.max(0, 100 - pesoAsis)
      const notaAsis = getAsistenciaNota(estId)
      const final = (promedioBase * (pesoNotas / 100)) + (notaAsis * (pesoAsis / 100))
      return final.toFixed(1)
    }
    return promedioBase.toFixed(1)
  }

  let sumaPonderada = 0
  let totalPorcentaje = 0
  let sumaSimple = 0
  let cantidadSimple = 0

  headers.value.forEach(t => {
    const notaRaw = getNota(estId, t.id)
    const nota = Number(notaRaw)
    if (notaRaw === undefined || notaRaw === null || Number.isNaN(nota)) return

    const porcentaje = Number(t.porcentaje || 0)
    if (porcentaje > 0) {
      sumaPonderada += nota * (porcentaje / 100)
      totalPorcentaje += porcentaje
    } else {
      // Tareas sin ponderacion: promedio simple de las no ponderadas
      sumaSimple += nota
      cantidadSimple++
    }
  })

  let promedioBase = null
  if (totalPorcentaje > 0) {
    promedioBase = (sumaPonderada / (totalPorcentaje / 100))
  } else if (cantidadSimple > 0) {
    promedioBase = (sumaSimple / cantidadSimple)
  }

  if (promedioBase === null) return '-'

  if (cursoConfig.value.usa_asistencia) {
    const pesoAsis = Number(cursoConfig.value.peso_asistencia || 0)
    const pesoNotas = Math.max(0, 100 - pesoAsis)
    const notaAsis = getAsistenciaNota(estId)
    const final = (promedioBase * (pesoNotas / 100)) + (notaAsis * (pesoAsis / 100))
    return final.toFixed(1)
  }

  return promedioBase.toFixed(1)
}

function getAsistenciaPct(estId) {
  return Number(asistenciaMap.value[`${estId}`] || 0)
}

function getAsistenciaNota(estId) {
  const pct = getAsistenciaPct(estId)
  const notaMaxima = Number(cursoConfig.value.nota_maxima || 5)
  return (pct / 100) * notaMaxima
}

function getGradeColor(val, isBg = false) {
  if (!val || val === '-') return isBg ? '' : 'text-muted'
  const nota = parseFloat(val)
  if (nota >= 4.0) return isBg ? 'text-success' : 'text-success'
  if (nota >= 3.0) return isBg ? 'text-warning' : 'text-warning'
  return isBg ? 'text-danger' : 'text-danger'
}

function irACalificar(estudianteId, tareaId) {
  router.push(`/curso/${cursoId}/calificar?tarea=${tareaId}&estudiante=${estudianteId}`)
}
</script>

<style scoped>
.planilla-wrapper {
  overflow-x: auto;
  overflow-y: auto;
  max-height: calc(100vh - 250px);
}

.planilla-table {
  border-collapse: separate;
  border-spacing: 0;
}

.planilla-table th, .planilla-table td {
  border-bottom: 1px solid #e2e8f0;
  border-right: 1px solid #e2e8f0;
  height: 48px; /* Fixed height for uniformity */
}

/* Scroll Fijo */
.sticky-top {
  top: 0;
  position: sticky;
}

.sticky-col-left {
  position: sticky;
  left: 0;
  z-index: 2;
}

.sticky-col-right {
  position: sticky;
  right: 0;
  z-index: 2;
}

.shadow-right {
  box-shadow: 2px 0 5px -2px rgba(0,0,0,0.1);
}

.shadow-left {
  box-shadow: -2px 0 5px -2px rgba(0,0,0,0.1);
}

.name-col {
  min-width: 220px;
  max-width: 250px;
}

/* Celda Hover & Interacción */
.grade-cell {
  padding: 0.5rem;
}

.grade-cell:hover {
  background-color: #f1f5f9;
}

.cell-hover:hover .cell-tooltip {
  display: block !important;
  bottom: 100%;
  left: 50%;
  transform: translateX(-50%);
  margin-bottom: 5px;
  white-space: nowrap;
}

.edit-input {
  border-radius: 0;
  border-width: 2px;
}

/* Utilidades de Avatar */
.avatar-sm {
  width: 28px;
  height: 28px;
  font-weight: bold;
}
.bg-color-0 { background-color: #3b82f6; }
.bg-color-1 { background-color: #10b981; }
.bg-color-2 { background-color: #8b5cf6; }
.bg-color-3 { background-color: #f43f5e; }
</style>
