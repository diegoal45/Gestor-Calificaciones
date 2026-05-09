<template>
  <div class="resumen-container">
    
    <!-- Loading State -->
    <div v-if="loading" class="row g-4">
      <div class="col-md-3" v-for="i in 4" :key="i">
        <div class="card border-0 shadow-sm rounded-4 p-4 placeholder-glow">
          <div class="placeholder col-6 mb-3"></div>
          <div class="placeholder col-8 h2 mb-0"></div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="alert alert-danger">
      {{ error }}
    </div>

    <!-- Dashboard Content -->
    <div v-else>
      <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-3 mb-4">
        <div>
          <h4 class="fw-bold mb-1">Panel del curso</h4>
          <p class="text-muted small m-0">Indicadores generales y exportación de calificaciones.</p>
        </div>
        <button type="button" class="btn btn-light border d-flex align-items-center gap-2 fw-medium" @click="exportModal?.open()">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
          Exportar planilla
        </button>
      </div>

      <!-- KPIS -->
      <div class="row g-4 mb-4">
        <!-- Promedio -->
        <div class="col-md-6 col-xl-3">
          <div class="card border-0 shadow-sm rounded-4 h-100 kpi-card">
            <div class="card-body p-4 d-flex flex-column">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="text-muted fw-medium">Promedio Curso</div>
                <div class="icon-box bg-primary-light text-primary rounded-circle d-flex align-items-center justify-content-center">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
                </div>
              </div>
              <div class="mt-auto">
                <h2 class="fw-bold mb-1" :class="getColorByValue(stats.promedio)">{{ stats.promedio }}</h2>
                <div class="small" :class="stats.tendenciaPromedio > 0 ? 'text-success' : 'text-danger'">
                  <svg v-if="stats.tendenciaPromedio > 0" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"></polyline><polyline points="17 18 23 18 23 12"></polyline></svg>
                  <span class="ms-1 fw-medium">{{ Math.abs(stats.tendenciaPromedio) }}% vs mes anterior</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Aprobando -->
        <div class="col-md-6 col-xl-3">
          <div class="card border-0 shadow-sm rounded-4 h-100 kpi-card">
            <div class="card-body p-4 d-flex flex-column">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="text-muted fw-medium">Aprobando</div>
                <div class="icon-box bg-success-light text-success rounded-circle d-flex align-items-center justify-content-center">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
              </div>
              <div class="mt-auto">
                <h2 class="fw-bold mb-1">{{ stats.aprobando }} <span class="fs-5 text-muted">/ {{ props.curso?.estudiantes_count || 0 }}</span></h2>
                <div class="small text-muted fw-medium">Estudiantes al día</div>
              </div>
            </div>
          </div>
        </div>

        <!-- En Riesgo -->
        <div class="col-md-6 col-xl-3">
          <div class="card border-0 shadow-sm rounded-4 h-100 kpi-card">
            <div class="card-body p-4 d-flex flex-column">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="text-muted fw-medium">En Riesgo</div>
                <div class="icon-box bg-danger-light text-danger rounded-circle d-flex align-items-center justify-content-center">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                </div>
              </div>
              <div class="mt-auto">
                <h2 class="fw-bold mb-1 text-danger">{{ stats.riesgo }}</h2>
                <div class="small text-muted fw-medium">Requieren atención</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tareas Activas -->
        <div class="col-md-6 col-xl-3">
          <div class="card border-0 shadow-sm rounded-4 h-100 kpi-card">
            <div class="card-body p-4 d-flex flex-column">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="text-muted fw-medium">Tareas Activas</div>
                <div class="icon-box bg-warning-light text-warning rounded-circle d-flex align-items-center justify-content-center">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                </div>
              </div>
              <div class="mt-auto">
                <h2 class="fw-bold mb-1">{{ stats.tareasActivas }}</h2>
                <div class="small text-muted fw-medium">Próximas a vencer</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Charts Area -->
      <div class="row g-4 mb-4">
        <div class="col-lg-8">
          <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
              <h5 class="fw-bold mb-4">Distribución de Notas</h5>
              <div v-if="mockChartData.length > 0" class="d-flex align-items-end justify-content-between mt-4 chart-container px-3">
                <div v-for="bar in mockChartData" :key="bar.label" class="d-flex flex-column align-items-center w-100">
                  <div class="fw-bold text-muted mb-2 small">{{ bar.value }}</div>
                  <div class="chart-bar rounded-top transition" :class="bar.colorClass" :style="{ height: `${bar.height}%` }"></div>
                  <div class="mt-2 text-muted small fw-medium">{{ bar.label }}</div>
                </div>
              </div>
              <div v-else class="text-muted small">
                Aun no hay suficientes datos para mostrar la distribucion.
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-lg-4">
          <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex flex-column">
              <h5 class="fw-bold mb-4">Actividad Reciente</h5>
              <div class="flex-grow-1 overflow-auto pe-2 activity-feed">
                <div v-for="(act, idx) in recentActivity" :key="idx" class="d-flex gap-3 mb-4">
                  <div class="mt-1">
                    <div class="activity-dot rounded-circle" :class="act.dotClass"></div>
                  </div>
                  <div>
                    <div class="fw-medium text-dark">{{ act.title }}</div>
                    <div class="text-muted small">{{ act.desc }}</div>
                    <div class="text-muted small opacity-75 mt-1">{{ act.time }}</div>
                  </div>
                </div>
                <div v-if="recentActivity.length === 0" class="text-muted small">
                  No hay actividad reciente para mostrar.
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Course Code section -->
      <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary-light">
        <div class="card-body p-4 d-flex justify-content-between align-items-center">
          <div>
            <h5 class="fw-bold text-primary mb-1">Código de Invitación del Curso</h5>
            <p class="text-muted small m-0">Comparte este código con tus estudiantes para que puedan unirse.</p>
          </div>
          <div>
            <div v-if="props.curso?.codigo_invitacion" class="d-flex align-items-center gap-3">
              <span class="fs-3 fw-bold text-dark font-monospace bg-white px-4 py-2 rounded shadow-sm border border-primary">
                {{ props.curso.codigo_invitacion }}
              </span>
              <button class="btn btn-light border fw-bold px-3 py-2" @click="copiarCodigo" :disabled="generatingCode">
                {{ copiedCode ? 'Copiado' : 'Copiar' }}
              </button>
              <button class="btn btn-outline-primary fw-bold px-3 py-2" @click="regenerarCodigo" :disabled="generatingCode">
                <span v-if="generatingCode" class="spinner-border spinner-border-sm me-2"></span>
                Regenerar
              </button>
            </div>
            <button v-else class="btn btn-primary-custom fw-bold px-4 py-2" @click="generarCodigo" :disabled="generatingCode">
              <span v-if="generatingCode" class="spinner-border spinner-border-sm me-2"></span>
              Generar Código
            </button>
          </div>
        </div>
      </div>

      <ExportPlanillaModal ref="exportModal" :curso-id="cursoId" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { apiRequest } from '../../../api.js'
import ExportPlanillaModal from '../../../components/ExportPlanillaModal.vue'

const props = defineProps(['curso'])
const route = useRoute()
const cursoId = route.params.id

const loading = ref(true)
const error = ref('')

const stats = ref({
  promedio: '0.0',
  tendenciaPromedio: 0,
  aprobando: 0,
  riesgo: 0,
  tareasActivas: 0
})

const mockChartData = ref([])
const recentActivity = ref([])
const generatingCode = ref(false)
const copiedCode = ref(false)
const exportModal = ref(null)

onMounted(async () => {
  await loadResumen()
})

async function loadResumen() {
  loading.value = true
  try {
    const res = await apiRequest(`/api/cursos/${cursoId}/resumen`)
    stats.value = res.stats || stats.value
    mockChartData.value = res.chartData || []
    recentActivity.value = res.activity || []
  } catch (e) {
    error.value = 'Error al cargar el resumen: ' + e.message
  } finally {
    loading.value = false
  }
}

async function generarCodigo() {
  generatingCode.value = true
  try {
    await apiRequest(`/api/cursos/${cursoId}/generar-codigo`, 'POST')

    // Releer desde backend para confirmar persistencia real en DB
    const cursoRes = await apiRequest(`/api/cursos/${cursoId}`)
    const cursoActual = cursoRes?.curso || cursoRes
    if (cursoActual?.codigo_invitacion && props.curso) {
      props.curso.codigo_invitacion = cursoActual.codigo_invitacion
    } else {
      throw new Error('El codigo no se guardo en base de datos.')
    }
  } catch (e) {
    alert('Error al generar código: ' + e.message)
  } finally {
    generatingCode.value = false
  }
}

async function regenerarCodigo() {
  const confirmar = confirm('Se reemplazara el codigo actual. ¿Deseas continuar?')
  if (!confirmar) return
  await generarCodigo()
}

async function copiarCodigo() {
  if (!props.curso?.codigo_invitacion) return
  try {
    await navigator.clipboard.writeText(props.curso.codigo_invitacion)
    copiedCode.value = true
    setTimeout(() => {
      copiedCode.value = false
    }, 1500)
  } catch (e) {
    alert('No se pudo copiar el codigo.')
  }
}

function getColorByValue(val) {
  const nota = parseFloat(val)
  if (nota >= 4.0) return 'text-success'
  if (nota >= 3.0) return 'text-warning'
  return 'text-danger'
}
</script>

<style scoped>
.kpi-card {
  transition: transform 0.2s;
}
.kpi-card:hover {
  transform: translateY(-5px);
}

.icon-box {
  width: 48px;
  height: 48px;
}

.bg-primary-light { background-color: #eff6ff; }
.bg-success-light { background-color: #ecfdf5; }
.bg-danger-light { background-color: #fef2f2; }
.bg-warning-light { background-color: #fffbeb; }

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
}

/* CSS Mock Chart */
.chart-container {
  height: 200px;
  border-bottom: 2px solid #e2e8f0;
}
.chart-bar {
  width: 40px;
  background-color: #cbd5e1;
  min-height: 5px;
}

/* Activity Feed */
.activity-feed::-webkit-scrollbar {
  width: 4px;
}
.activity-feed::-webkit-scrollbar-thumb {
  background-color: #cbd5e1;
  border-radius: 4px;
}

.activity-dot {
  width: 12px;
  height: 12px;
  box-shadow: 0 0 0 4px rgba(255,255,255,0.8);
}
</style>
