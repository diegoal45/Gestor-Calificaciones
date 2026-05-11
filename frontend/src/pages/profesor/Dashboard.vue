<template>
  <div>
    <!-- Header del Dashboard -->
    <div class="d-flex justify-content-between align-items-center mb-5 mt-2">
      <div>
        <h2 class="fw-bold m-0 text-dark">Hola, {{ userName }} <span class="wave-emoji">👋</span></h2>
        <p class="text-muted mt-1 fs-5">Semestre 2026-1</p>
      </div>
      <button
        v-if="activeTab === 'cursos'"
        class="btn btn-primary-custom d-flex align-items-center gap-2 fw-bold px-4 py-2"
        @click="crearCurso"
      >
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Crear curso
      </button>
    </div>

    <!-- Error state -->
    <div v-if="error" class="alert alert-danger shadow-sm border-0 rounded-4">
      {{ error }}
    </div>

    <!-- Tabs -->
    <ul class="nav nav-pills mb-4">
      <li class="nav-item">
        <button class="nav-link" :class="{ active: activeTab === 'inicio' }" @click="activeTab = 'inicio'">
          Inicio
        </button>
      </li>
      <li class="nav-item">
        <button class="nav-link" :class="{ active: activeTab === 'cursos' }" @click="activeTab = 'cursos'">
          Cursos
        </button>
      </li>
    </ul>

    <!-- INICIO: Panel de información -->
    <div v-if="activeTab === 'inicio'">
      <div class="row g-4 mb-4">
        <div class="col-12 col-md-4">
          <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <div class="text-muted fw-medium small">Cursos</div>
            <div class="d-flex align-items-end justify-content-between mt-2">
              <div class="display-6 fw-bold">{{ totalCursos }}</div>
              <button class="btn btn-sm btn-light border fw-medium" @click="activeTab = 'cursos'">Ver</button>
            </div>
            <div class="small text-muted mt-2">Total de cursos cargados en esta vista.</div>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <div class="text-muted fw-medium small">Reclamos pendientes</div>
            <div class="d-flex align-items-end justify-content-between mt-2">
              <div class="display-6 fw-bold" :class="reclamosPendientesCount > 0 ? 'text-danger' : ''">
                {{ reclamosPendientesCount }}
              </div>
              <button class="btn btn-sm btn-light border fw-medium" @click="activeTab = 'cursos'">Abrir cursos</button>
            </div>
            <div class="small text-muted mt-2">Suma de reclamos con estado “pendiente” (en tus cursos).</div>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <div class="text-muted fw-medium small">Últimas calificaciones</div>
            <div class="d-flex align-items-end justify-content-between mt-2">
              <div class="display-6 fw-bold">{{ ultimasCalificaciones.length }}</div>
              <button class="btn btn-sm btn-light border fw-medium" @click="refrescarPaneles" :disabled="loadingPaneles">
                {{ loadingPaneles ? 'Actualizando...' : 'Actualizar' }}
              </button>
            </div>
            <div class="small text-muted mt-2">Registros recientes detectados por fecha de actualización.</div>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-12 col-lg-7">
          <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
              <div>
                <div class="fw-bold">Últimas tareas calificadas</div>
                <div class="text-muted small">Basado en notas (`updated_at`) de tus cursos.</div>
              </div>
              <button class="btn btn-sm btn-light border fw-medium" @click="refrescarPaneles" :disabled="loadingPaneles">
                {{ loadingPaneles ? 'Cargando...' : 'Recargar' }}
              </button>
            </div>
            <div class="card-body p-0">
              <div v-if="loadingPaneles" class="p-4 text-muted">Cargando actividad...</div>
              <div v-else-if="ultimasCalificaciones.length === 0" class="p-4 text-muted">
                Aún no hay calificaciones registradas.
              </div>
              <ul v-else class="list-group list-group-flush">
                <li v-for="(it, idx) in ultimasCalificaciones" :key="idx" class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                  <div class="me-3">
                    <div class="fw-bold text-dark text-truncate" :title="`${it.curso} · ${it.tarea}`">{{ it.curso }} · {{ it.tarea }}</div>
                    <div class="small text-muted">{{ formatDateTime(it.updated_at) }}</div>
                  </div>
                  <div class="fw-bold" :class="getNotaClass(it.nota)">{{ Number(it.nota).toFixed(1) }}</div>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-5">
          <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom p-4">
              <div class="d-flex justify-content-between align-items-start gap-3">
                <div>
                  <div class="fw-bold">Reclamos pendientes</div>
                  <div class="text-muted small">Los más recientes (estado “pendiente”).</div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary fw-medium" @click="irAReclamos" :disabled="loadingPaneles || !cursoDestinoReclamos">
                  Ir a Reclamos
                </button>
              </div>
            </div>
            <div class="card-body p-0">
              <div v-if="loadingPaneles" class="p-4 text-muted">Cargando reclamos...</div>
              <div v-else-if="reclamosPendientes.length === 0" class="p-4 text-muted">
                No tienes reclamos pendientes.
              </div>
              <ul v-else class="list-group list-group-flush">
                <li v-for="r in reclamosPendientes" :key="r.id" class="list-group-item py-3 px-4">
                  <div class="d-flex justify-content-between gap-3">
                    <div class="text-truncate">
                      <div class="fw-bold text-dark text-truncate" :title="r.titulo">{{ r.titulo }}</div>
                      <div class="small text-muted text-truncate" :title="r.mensaje">{{ r.mensaje }}</div>
                    </div>
                    <div class="small text-muted text-nowrap">{{ formatDateTime(r.created_at) }}</div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- CURSOS: lo que estaba antes -->
    <div v-else>
      <div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
        <div class="row g-2 align-items-center">
          <div class="col-md-8">
            <input
              v-model="searchQuery"
              @keyup.enter="applyFilter"
              type="text"
              class="form-control custom-input"
              placeholder="Filtrar cursos por nombre o descripcion..."
            />
          </div>
          <div class="col-md-4 d-flex gap-2 justify-content-md-end">
            <button class="btn btn-light border fw-medium" @click="clearFilter" :disabled="!searchQuery">Limpiar</button>
            <button class="btn btn-primary-custom fw-bold" @click="applyFilter">Filtrar</button>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="row">
        <div class="col-md-4 mb-4" v-for="i in 3" :key="i">
          <div class="card border-0 rounded-4 shadow-sm p-4 h-100 placeholder-glow">
            <div class="placeholder col-8 mb-3 py-3 rounded"></div>
            <div class="placeholder col-4 mb-2"></div>
            <div class="placeholder col-6"></div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="cursos.length === 0" class="text-center py-5">
        <div class="opacity-50 mb-3">
          <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
        </div>
        <h4 class="text-muted fw-bold">Aún no tienes cursos</h4>
        <p class="text-muted">Crea tu primer curso para empezar a gestionar calificaciones.</p>
      </div>

      <!-- Grid de Cursos (API Real) -->
      <div v-else class="row g-4">
        <div class="col-12 col-md-6 col-xl-4" v-for="(curso, index) in cursos" :key="curso.id">
          <div class="card course-card border-0 shadow-sm h-100 overflow-hidden">
            
            <!-- Banner Superior del Card -->
            <div class="course-banner text-white p-4 position-relative" :class="`banner-color-${index % 4}`">
              <h4 class="fw-bold mb-1 position-relative z-1 text-truncate" :title="curso.nombre">{{ curso.nombre }}</h4>
              <p class="opacity-75 m-0 position-relative z-1 text-truncate">{{ curso.descripcion || 'Sin descripción' }}</p>
              
              <!-- Patrón de fondo abstracto -->
              <div class="abstract-pattern"></div>
            </div>
            
            <!-- Info interna -->
            <div class="card-body p-4 d-flex flex-column">
              
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text-muted d-flex align-items-center gap-2">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5.5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                  <span class="fw-medium">Estudiantes: {{ curso.estudiantes_count !== undefined ? curso.estudiantes_count : '...' }}</span>
                </div>
                <div class="badge bg-light text-dark border px-2 py-1 fs-6">
                  Promedio: {{ curso.promedio_general !== undefined ? curso.promedio_general : 'N/A' }}
                </div>
              </div>
              
              <div class="mt-auto d-flex gap-2">
                <button class="btn btn-light flex-grow-1 border fw-medium action-btn" @click="abrirCurso(curso.id)">
                  Abrir curso
                </button>
                <button class="btn btn-light border px-3 action-btn text-muted" title="Planilla Rápida">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="3" y1="15" x2="21" y2="15"></line><line x1="9" y1="9" x2="9" y2="21"></line><line x1="15" y1="9" x2="15" y2="21"></line></svg>
                </button>
              </div>
            </div>
            
          </div>
        </div>
      </div>
      <div v-if="!loading && cursos.length > 0" class="d-flex justify-content-between align-items-center mt-4">
        <small class="text-muted">Mostrando {{ cursos.length }} de {{ totalCursos }} cursos</small>
        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-sm btn-light border" @click="goToPage(currentPage - 1)" :disabled="currentPage <= 1">Anterior</button>
          <span class="small text-muted">Pagina {{ currentPage }} de {{ lastPage }}</span>
          <button class="btn btn-sm btn-light border" @click="goToPage(currentPage + 1)" :disabled="currentPage >= lastPage">Siguiente</button>
        </div>
      </div>
    </div>
    <!-- Formulario Modal para Crear Curso (Overlay) -->
    <div v-if="showCreateModal" class="modal-overlay d-flex align-items-center justify-content-center z-3">
      <div class="card border-0 shadow-lg rounded-4 modal-content-custom">
        <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center rounded-top-4">
          <h5 class="fw-bold m-0">Crear Nuevo Curso</h5>
          <button class="btn-close shadow-none" @click="showCreateModal = false"></button>
        </div>
        <div class="card-body p-4">
          <form @submit.prevent="guardarNuevoCurso">
            <div class="mb-3">
              <label class="form-label fw-medium text-muted small">Nombre del Curso</label>
              <input v-model="newCurso.nombre" type="text" class="form-control form-control-lg custom-input" placeholder="Ej: Matemáticas Avanzadas" required>
            </div>
            
            <div class="mb-4">
              <label class="form-label fw-medium text-muted small">Descripción (Opcional)</label>
              <textarea v-model="newCurso.descripcion" class="form-control custom-input" rows="2" placeholder="Breve descripción del curso..."></textarea>
            </div>
            
            <div class="row g-3 mb-4">
              <div class="col-6">
                <label class="form-label fw-medium text-muted small">Nota Mínima Aprobatoria</label>
                <input v-model="newCurso.nota_minima" type="number" step="0.1" min="1" max="100" class="form-control custom-input text-end" required>
              </div>
              <div class="col-6">
                <label class="form-label fw-medium text-muted small">Nota Máxima</label>
                <input v-model="newCurso.nota_maxima" type="number" step="0.1" min="1" max="100" class="form-control custom-input text-end" required>
              </div>
            </div>

            <div class="mb-4">
              <label class="form-label fw-medium text-muted small">Método de calificación</label>
              <select v-model="newCurso.metodo_calificacion" class="form-select custom-input">
                <option value="ponderacion">Ponderación (porcentajes por tarea)</option>
                <option value="promedio">Promedio simple (sin porcentajes)</option>
              </select>
              <div class="small text-muted mt-1">
                Este método define cómo se calcula el promedio/definitiva del curso para planilla, resumen y análisis.
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
              <button type="button" class="btn btn-light fw-medium px-4" @click="showCreateModal = false">Cancelar</button>
              <button type="submit" class="btn btn-primary-custom fw-bold px-4" :disabled="savingCurso">
                <span v-if="savingCurso" class="spinner-border spinner-border-sm me-2"></span>
                Crear Curso
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { apiRequest } from '../../api.js'

const router = useRouter()
const route = useRoute()
const userName = ref('')
const cursos = ref([])
const loading = ref(true)
const error = ref('')
const searchQuery = ref('')
const currentPage = ref(1)
const lastPage = ref(1)
const totalCursos = ref(0)

const activeTab = ref('inicio')

// Panel "Inicio"
const loadingPaneles = ref(false)
const ultimasCalificaciones = ref([])
const reclamosPendientes = ref([])
const reclamosPendientesCount = ref(0)
const cursoDestinoReclamos = ref(null)

// Form state
const showCreateModal = ref(false)
const savingCurso = ref(false)
const newCurso = ref({
  nombre: '',
  descripcion: '',
  nota_minima: 3.0,
  nota_maxima: 5.0,
  metodo_calificacion: 'ponderacion',
})

onMounted(async () => {
  // Obtener nombre desde localstorage
  try {
    const user = JSON.parse(localStorage.getItem('user'))
    if (user && user.nombre) userName.value = user.nombre
  } catch (e) {
    userName.value = 'Profesor'
  }

  await loadCursos()
  // Soporta navegación directa desde sidebar: /dashboard?tab=cursos
  if (route.query?.tab === 'cursos') {
    activeTab.value = 'cursos'
  }
  await refrescarPaneles()
})

watch(
  () => route.query?.tab,
  (tab) => {
    if (tab === 'cursos') activeTab.value = 'cursos'
    if (tab === 'inicio') activeTab.value = 'inicio'
  }
)

async function loadCursos(page = 1) {
  loading.value = true
  error.value = ''
  try {
    const params = new URLSearchParams({
      page: String(page),
      per_page: '6',
    })
    if (searchQuery.value.trim()) {
      params.set('q', searchQuery.value.trim())
    }
    const res = await apiRequest(`/api/cursos?${params.toString()}`)
    cursos.value = res.data || []
    currentPage.value = res.meta?.current_page || 1
    lastPage.value = res.meta?.last_page || 1
    totalCursos.value = res.meta?.total || cursos.value.length
  } catch (e) {
    error.value = 'Error al cargar los cursos: ' + e.message
  } finally {
    loading.value = false
  }
}

async function refrescarPaneles() {
  loadingPaneles.value = true
  try {
    const ids = (cursos.value || []).map(c => c.id).filter(Boolean)
    if (ids.length === 0) {
      ultimasCalificaciones.value = []
      reclamosPendientes.value = []
      reclamosPendientesCount.value = 0
      return
    }

    // Últimas calificaciones: se infiere desde /tareas/curso/{id} (incluye notas con updated_at)
    const tareasPorCurso = await Promise.all(ids.map(id => apiRequest(`/api/tareas/curso/${id}`)))
    const flat = []
    tareasPorCurso.forEach((tareas, i) => {
      const cursoNombre = cursos.value[i]?.nombre || `Curso ${ids[i]}`
      ;(tareas || []).forEach(t => {
        ;(t.notas || []).forEach(n => {
          flat.push({
            curso_id: ids[i],
            curso: cursoNombre,
            tarea: t.nombre || `Tarea ${t.id}`,
            nota: Number(n.nota),
            updated_at: n.updated_at,
          })
        })
      })
    })
    flat.sort((a, b) => new Date(b.updated_at).getTime() - new Date(a.updated_at).getTime())
    ultimasCalificaciones.value = flat.slice(0, 8)

    // Reclamos pendientes: suma por curso
    const reclamosPorCurso = await Promise.all(ids.map(id => apiRequest(`/api/reclamos/curso/${id}`)))
    const reclamosFlat = reclamosPorCurso.flatMap((recs, idx) => {
      const cursoNombre = cursos.value[idx]?.nombre || `Curso ${ids[idx]}`
      return (recs || []).map(r => ({
        id: r.id,
        curso_id: ids[idx],
        estado: r.estado,
        created_at: r.created_at,
        mensaje: r.mensaje,
        titulo: `${cursoNombre} · ${(r?.nota?.tarea?.nombre) || 'Tarea'} · ${(r?.estudiante?.nombre) || 'Estudiante'}`,
      }))
    })
    const pendientes = reclamosFlat.filter(r => String(r.estado || '').toLowerCase() === 'pendiente')
    pendientes.sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime())
    reclamosPendientesCount.value = pendientes.length
    reclamosPendientes.value = pendientes.slice(0, 6)
    cursoDestinoReclamos.value = (pendientes[0]?.curso_id) || ids[0] || null
  } catch (e) {
    // No bloquea la vista "Cursos"
    console.warn('No se pudieron cargar paneles del dashboard', e)
  } finally {
    loadingPaneles.value = false
  }
}

function irAReclamos() {
  const cid = cursoDestinoReclamos.value
  if (!cid) {
    activeTab.value = 'cursos'
    return
  }
  router.push(`/curso/${cid}/reclamos`)
}

function crearCurso() {
  newCurso.value = { nombre: '', descripcion: '', nota_minima: 3.0, nota_maxima: 5.0, metodo_calificacion: 'ponderacion' }
  showCreateModal.value = true
}

async function guardarNuevoCurso() {
  savingCurso.value = true
  try {
    await apiRequest('/api/cursos', 'POST', newCurso.value)
    await loadCursos(1)
    await refrescarPaneles()
    showCreateModal.value = false
  } catch (e) {
    alert('Error al crear el curso: ' + e.message)
  } finally {
    savingCurso.value = false
  }
}

function abrirCurso(id) {
  router.push(`/curso/${id}/resumen`)
}

function applyFilter() {
  loadCursos(1)
}

function clearFilter() {
  searchQuery.value = ''
  loadCursos(1)
}

function goToPage(page) {
  if (page < 1 || page > lastPage.value) return
  loadCursos(page)
}

function formatDateTime(value) {
  if (!value) return ''
  const d = new Date(value)
  if (Number.isNaN(d.getTime())) return String(value)
  return d.toLocaleString('es-CO', { year: 'numeric', month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit' })
}

function getNotaClass(nota) {
  const v = Number(nota)
  if (Number.isNaN(v)) return 'text-muted'
  if (v >= 4.0) return 'text-success'
  if (v >= 3.0) return 'text-warning'
  return 'text-danger'
}
</script>

<style scoped>
.wave-emoji {
  display: inline-block;
  animation: wave-animation 2.5s infinite;
  transform-origin: 70% 70%;
}

@keyframes wave-animation {
  0% { transform: rotate( 0.0deg) }
  10% { transform: rotate(14.0deg) }
  20% { transform: rotate(-8.0deg) }
  30% { transform: rotate(14.0deg) }
  40% { transform: rotate(-4.0deg) }
  50% { transform: rotate(10.0deg) }
  60% { transform: rotate( 0.0deg) }
  100% { transform: rotate( 0.0deg) }
}

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
  max-width: 500px;
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

.btn-primary-custom {
  background-color: #1e40af;
  color: white;
  border: none;
  border-radius: 10px;
  transition: all 0.3s ease;
}

.btn-primary-custom:hover {
  background-color: #1e3a8a;
  transform: translateY(-2px);
  box-shadow: 0 8px 15px rgba(30, 64, 175, 0.2);
}

.course-card {
  border-radius: 16px;
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  cursor: pointer;
}

.course-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1) !important;
}

.course-banner {
  height: 120px;
  overflow: hidden;
}

.banner-color-0 { background: linear-gradient(135deg, #2563eb, #1e40af); }
.banner-color-1 { background: linear-gradient(135deg, #059669, #047857); }
.banner-color-2 { background: linear-gradient(135deg, #9333ea, #7e22ce); }
.banner-color-3 { background: linear-gradient(135deg, #e11d48, #be123c); }

.abstract-pattern {
  position: absolute;
  top: 0;
  right: -20%;
  width: 150px;
  height: 150px;
  background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 70%);
  border-radius: 50%;
  transform: translateY(-20%);
  z-index: 0;
}

.custom-progress-bar {
  background-color: #3b82f6;
  border-radius: 6px;
}

.action-btn {
  border-radius: 10px;
  transition: all 0.2s ease;
}

.action-btn:hover {
  background-color: #f1f5f9;
  border-color: #cbd5e1 !important;
}
</style>
