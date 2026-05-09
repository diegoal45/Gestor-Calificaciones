<template>
  <div class="asistencia-container d-flex flex-column h-100">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="fw-bold mb-1">Registro de Asistencia</h4>
        <p class="text-muted small m-0">Asistencia global del curso y su impacto opcional en la nota final</p>
      </div>
      <div class="d-flex gap-3 align-items-center flex-wrap">
        <button type="button" class="btn btn-light border fw-medium" @click="exportModal?.open()">
          Exportar planilla
        </button>
        <input type="date" class="form-control" v-model="fechaActual" @change="loadAsistencia">
        <button class="btn btn-primary-custom px-4 fw-bold" @click="guardarAsistencia" :disabled="saving">
          <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
          Guardar Registro
        </button>
      </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
      <div class="row g-3 align-items-end">
        <div class="col-md-4">
          <label class="form-label fw-medium">¿Asistencia cuenta en nota final?</label>
          <select class="form-select" v-model="usaAsistencia">
            <option :value="true">Sí</option>
            <option :value="false">No</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-medium">% peso asistencia</label>
          <input type="number" min="0" max="100" class="form-control" v-model.number="pesoAsistencia" :disabled="!usaAsistencia">
        </div>
        <div class="col-md-4">
          <button class="btn btn-light border fw-medium w-100" @click="guardarConfigAsistencia" :disabled="savingConfig">
            <span v-if="savingConfig" class="spinner-border spinner-border-sm me-2"></span>
            Guardar configuracion
          </button>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 flex-grow-1 overflow-hidden">
      <div v-if="loading" class="p-5 text-center">
        <div class="spinner-border text-primary"></div>
      </div>
      
      <div v-else class="table-responsive h-100">
        <table class="table align-middle mb-0 custom-table table-hover">
          <thead class="table-light sticky-top z-1">
            <tr>
              <th class="px-4 py-3 text-muted fw-medium border-0">Estudiante</th>
              <th class="py-3 text-muted fw-medium border-0 text-center">Asistió</th>
              <th class="py-3 text-muted fw-medium border-0 text-center">Falta Injustificada</th>
              <th class="px-4 py-3 text-muted fw-medium border-0 text-end">Asistencia Global (%)</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="est in estudiantes" :key="est.id" class="transition" :class="getRowClass(est.estado)">
              <td class="px-4 py-3 fw-medium text-dark">{{ est.nombre }}</td>
              
              <!-- Radios para Estado -->
              <td class="py-3 text-center">
                <input type="radio" class="form-check-input attendance-radio radio-success" :name="`estado_${est.id}`" value="P" v-model="est.estado">
              </td>
              <td class="py-3 text-center">
                <input type="radio" class="form-check-input attendance-radio radio-danger" :name="`estado_${est.id}`" value="F" v-model="est.estado">
              </td>
              <!-- Stats -->
              <td class="px-4 py-3 text-end">
                <div class="d-flex align-items-center justify-content-end gap-2">
                  <div class="progress" style="width: 60px; height: 6px;">
                    <div class="progress-bar" :class="est.porcentaje >= 80 ? 'bg-success' : 'bg-danger'" :style="{width: `${est.porcentaje}%`}"></div>
                  </div>
                  <span class="fw-bold" :class="est.porcentaje >= 80 ? 'text-success' : 'text-danger'">{{ est.porcentaje }}%</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <ExportPlanillaModal ref="exportModal" :curso-id="cursoId" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { apiRequest } from '../../../api.js'
import ExportPlanillaModal from '../../../components/ExportPlanillaModal.vue'

import { useRoute } from 'vue-router'

const route = useRoute()
const cursoId = route.params.id

const exportModal = ref(null)
const loading = ref(true)
const saving = ref(false)
const savingConfig = ref(false)
const error = ref('')
const fechaActual = ref(new Date().toISOString().substr(0, 10))
const estudiantes = ref([])
const usaAsistencia = ref(false)
const pesoAsistencia = ref(0)

onMounted(async () => {
  await loadAsistencia()
})

async function loadAsistencia() {
  loading.value = true
  error.value = ''
  try {
    const [estudiantesRes, asistenciasHoy, asistenciasGlobal, cursoRes] = await Promise.all([
      apiRequest(`/api/cursos/${cursoId}/estudiantes`),
      apiRequest(`/api/asistencias/curso/${cursoId}?fecha=${fechaActual.value}`),
      apiRequest(`/api/asistencias/curso/${cursoId}`),
      apiRequest(`/api/cursos/${cursoId}`)
    ])
    const curso = cursoRes?.curso || cursoRes
    usaAsistencia.value = !!curso?.usa_asistencia
    pesoAsistencia.value = Number(curso?.peso_asistencia || 0)

    // Calcular estadísticas globales
    const stats = {}
    asistenciasGlobal.forEach(a => {
      if (!stats[a.id_estudiante]) {
         stats[a.id_estudiante] = { total: 0, presentes: 0 }
      }
      stats[a.id_estudiante].total++
      if (a.estado === 'presente') stats[a.id_estudiante].presentes++
    })

    // Mapear asistencias de hoy
    const hoyMap = {}
    asistenciasHoy.forEach(a => {
      hoyMap[a.id_estudiante] = a.estado === 'presente' ? 'P' : 'F'
    })

    // Mapear al modelo de vista
    estudiantes.value = estudiantesRes.map(e => {
      const st = stats[e.id] || { total: 0, presentes: 0 }
      const porcentajeReal = st.total === 0 ? 100 : Math.round((st.presentes / st.total) * 100)
      
      return {
        id: e.id,
        nombre: e.nombre,
        estado: hoyMap[e.id] || 'P',
        porcentaje: porcentajeReal
      }
    })
  } catch (e) {
    error.value = 'Error al cargar estudiantes o asistencias: ' + e.message
  } finally {
    loading.value = false
  }
}

function getRowClass(estado) {
  if (estado === 'F') return 'bg-danger-subtle'
  return ''
}

async function guardarConfigAsistencia() {
  savingConfig.value = true
  try {
    await apiRequest(`/api/cursos/${cursoId}/asistencia-config`, 'POST', {
      usa_asistencia: usaAsistencia.value,
      peso_asistencia: usaAsistencia.value ? Number(pesoAsistencia.value || 0) : null,
    })
    alert('Configuracion de asistencia actualizada.')
  } catch (e) {
    alert('Error guardando configuracion: ' + e.message)
  } finally {
    savingConfig.value = false
  }
}

async function guardarAsistencia() {
  saving.value = true
  try {
    const asistenciasData = estudiantes.value.map(e => ({
      id_estudiante: e.id,
      estado: e.estado
    }))
    await apiRequest(`/api/asistencias/curso/${cursoId}`, 'POST', {
      fecha: fechaActual.value,
      asistencias: asistenciasData
    })
    alert("Asistencia guardada correctamente.")
    await loadAsistencia() // Recargar para actualizar porcentajes
  } catch (e) {
    alert("Error guardando asistencia: " + e.message)
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
.btn-primary-custom {
  background-color: #1e40af;
  color: white;
  border: none;
  border-radius: 8px;
}

.custom-table th {
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.attendance-radio {
  width: 20px;
  height: 20px;
  cursor: pointer;
}

.radio-success:checked { background-color: #10b981; border-color: #10b981; }
.radio-danger:checked { background-color: #ef4444; border-color: #ef4444; }
.radio-warning:checked { background-color: #f59e0b; border-color: #f59e0b; }

.bg-danger-subtle { background-color: #fef2f2 !important; }
.bg-warning-subtle { background-color: #fffbeb !important; }

.transition { transition: background-color 0.2s ease; }
</style>
