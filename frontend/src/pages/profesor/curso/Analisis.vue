<template>
  <div class="analisis-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="fw-bold mb-1">Análisis de Rendimiento</h4>
        <p class="text-muted small m-0">Métricas avanzadas y comportamiento del curso</p>
      </div>
      <button type="button" class="btn btn-light border d-flex align-items-center gap-2 text-muted fw-medium" @click="exportModal?.open()">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
        Exportar planilla
      </button>
    </div>

    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary"></div>
    </div>
    
    <div v-else-if="error || !data" class="alert alert-secondary border-0 shadow-sm rounded-4 text-center py-5">
      <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="mb-3 opacity-50"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
      <h5>{{ error || 'Sin datos de análisis' }}</h5>
      <p class="text-muted">El backend aún no implementa este reporte.</p>
    </div>

    <div v-else class="row g-4">
      <!-- Indicadores Rápidos -->
      <div class="col-12">
        <div class="row g-4">
          <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-gradient-danger text-white">
              <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                  <div class="bg-white bg-opacity-25 rounded p-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                  </div>
                  <h6 class="fw-bold m-0 opacity-75">Estudiantes en Riesgo</h6>
                </div>
                <h2 class="fw-bold mb-1">{{ data.riesgoCount }}</h2>
                <div class="small bg-white bg-opacity-25 d-inline-block px-2 py-1 rounded">
                  Promedio < 3.0
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-gradient-success text-white">
              <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                  <div class="bg-white bg-opacity-25 rounded p-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                  </div>
                  <h6 class="fw-bold m-0 opacity-75">Rendimiento Óptimo</h6>
                </div>
                <h2 class="fw-bold mb-1">{{ data.optimoCount }}</h2>
                <div class="small bg-white bg-opacity-25 d-inline-block px-2 py-1 rounded">
                  Promedio > 4.5
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-gradient-warning text-dark">
              <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                  <div class="bg-dark bg-opacity-10 rounded p-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                  </div>
                  <h6 class="fw-bold m-0 opacity-75">Tarea más difícil</h6>
                </div>
                <h3 class="fw-bold mb-1 text-truncate">{{ data.tareaDificil.nombre }}</h3>
                <div class="small bg-dark bg-opacity-10 d-inline-block px-2 py-1 rounded fw-medium">
                  Promedio: {{ data.tareaDificil.promedio }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Gráficas (CSS Mocks para mantener UI rápida sin dependencias pesadas en fase 1) -->
      <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
          <div class="card-body p-4">
            <h6 class="fw-bold mb-4 text-muted">Rendimiento por Tarea</h6>
            
            <div class="d-flex flex-column gap-3">
              <div v-for="t in data.rendimientoTareas" :key="t.nombre">
                <div class="d-flex justify-content-between small fw-medium mb-1">
                  <span>{{ t.nombre }}</span>
                  <span :class="t.promedio >= 3.0 ? 'text-success' : 'text-danger'">{{ t.promedio }}</span>
                </div>
                <div class="progress" style="height: 8px;">
                  <div class="progress-bar" :class="t.promedio >= 3.0 ? 'bg-primary' : 'bg-danger'" :style="{width: `${(t.promedio / 5) * 100}%`}"></div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
          <div class="card-body p-4">
            <h6 class="fw-bold mb-4 text-muted">Tendencia de Asistencia vs Notas</h6>
            
            <div class="d-flex align-items-end justify-content-between h-75 px-2 chart-container mt-4">
              <!-- Mock Line Chart points via CSS -->
              <div class="chart-col d-flex flex-column align-items-center position-relative w-100" v-for="pt in data.tendencia" :key="pt.semana">
                <div class="data-point bg-primary position-absolute" :style="{bottom: `${(pt.nota / 5) * 100}%`}" :title="`Nota: ${pt.nota}`"></div>
                <div class="data-point bg-success position-absolute rounded-0" :style="{bottom: `${pt.asistencia}%`}" :title="`Asistencia: ${pt.asistencia}%`"></div>
                
                <div class="mt-auto pt-2 small text-muted text-center" style="transform: translateY(20px);">{{ pt.semana }}</div>
              </div>
            </div>
            
            <div class="d-flex justify-content-center gap-4 mt-4 pt-4 border-top">
              <div class="d-flex align-items-center gap-2 small fw-medium text-muted">
                <div class="data-point bg-primary position-static"></div> Promedio Notas
              </div>
              <div class="d-flex align-items-center gap-2 small fw-medium text-muted">
                <div class="data-point bg-success rounded-0 position-static"></div> Asistencia (%)
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <ExportPlanillaModal ref="exportModal" :curso-id="cursoId" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

import { useRoute } from 'vue-router'
import { apiRequest } from '../../../api.js'
import ExportPlanillaModal from '../../../components/ExportPlanillaModal.vue'

const route = useRoute()
const cursoId = route.params.id

const loading = ref(true)
const error = ref('')
const data = ref(null)
const exportModal = ref(null)

onMounted(async () => {
  await loadAnalisis()
})

async function loadAnalisis() {
  loading.value = true
  try {
    const res = await apiRequest(`/api/cursos/${cursoId}/analisis`)
    data.value = res
  } catch (e) {
    error.value = 'Error al cargar los datos de análisis: ' + (e.message || 'Error desconocido');
    data.value = null
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.bg-gradient-danger { background: linear-gradient(135deg, #ef4444, #b91c1c); }
.bg-gradient-success { background: linear-gradient(135deg, #10b981, #047857); }
.bg-gradient-warning { background: linear-gradient(135deg, #f59e0b, #d97706); }

.chart-container {
  border-bottom: 2px solid #f1f5f9;
  border-left: 2px solid #f1f5f9;
  padding-bottom: 10px;
}

.chart-col {
  height: 100%;
}

.data-point {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  box-shadow: 0 0 0 3px rgba(255,255,255,0.8);
  cursor: pointer;
  transition: transform 0.2s;
  z-index: 2;
}

.data-point:hover {
  transform: scale(1.5);
}
</style>
