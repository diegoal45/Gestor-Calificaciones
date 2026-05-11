<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-5 mt-2">
      <div>
        <h2 class="fw-bold m-0 text-dark">Hola, {{ userName }} <span class="wave-emoji">👋</span></h2>
        <p class="text-muted mt-1 fs-5">Tus cursos inscritos</p>
      </div>
      <div class="d-flex gap-2">
        <button v-if="activeTab === 'cursos'" class="btn btn-primary-custom fw-bold px-4 py-2" @click="showJoinModal = true">Unirme a curso</button>
        <button class="btn btn-light border fw-medium px-4 py-2 action-btn" @click="router.push('/estudiante/perfil')">Perfil</button>
      </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-pills mb-4">
      <li class="nav-item">
        <button type="button" class="nav-link" :class="{ active: activeTab === 'inicio' }" @click="irATab('inicio')">
          Inicio
        </button>
      </li>
      <li class="nav-item">
        <button type="button" class="nav-link" :class="{ active: activeTab === 'cursos' }" @click="irATab('cursos')">
          Cursos
        </button>
      </li>
    </ul>

    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <!-- INICIO: Panel -->
    <div v-if="activeTab === 'inicio'">
      <div class="row g-4 mb-4">
        <div class="col-12 col-md-4">
          <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <div class="text-muted fw-medium small">Cursos</div>
            <div class="d-flex align-items-end justify-content-between mt-2">
              <div class="display-6 fw-bold">{{ totalCursos }}</div>
              <button type="button" class="btn btn-sm btn-light border fw-medium" @click="irATab('cursos')">Ver</button>
            </div>
            <div class="small text-muted mt-2">Cursos en los que estás inscrito.</div>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <div class="text-muted fw-medium small">Reclamos pendientes</div>
            <div class="d-flex align-items-end justify-content-between mt-2">
              <div class="display-6 fw-bold" :class="reclamosPendientesCount > 0 ? 'text-danger' : ''">
                {{ reclamosPendientesCount }}
              </div>
              <button type="button" class="btn btn-sm btn-light border fw-medium" @click="irAReclamos" :disabled="loadingPaneles || !cursoDestinoReclamos">
                Ir
              </button>
            </div>
            <div class="small text-muted mt-2">Reclamos tuyos con estado “pendiente”.</div>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <div class="text-muted fw-medium small">Últimas calificaciones</div>
            <div class="d-flex align-items-end justify-content-between mt-2">
              <div class="display-6 fw-bold">{{ ultimasCalificaciones.length }}</div>
              <button type="button" class="btn btn-sm btn-light border fw-medium" @click="refrescarPaneles" :disabled="loadingPaneles">
                {{ loadingPaneles ? 'Actualizando...' : 'Actualizar' }}
              </button>
            </div>
            <div class="small text-muted mt-2">Tus notas más recientes (por fecha de actualización).</div>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-12 col-lg-7">
          <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
              <div>
                <div class="fw-bold">Últimas tareas calificadas</div>
                <div class="text-muted small">En todos tus cursos.</div>
              </div>
              <button type="button" class="btn btn-sm btn-light border fw-medium" @click="refrescarPaneles" :disabled="loadingPaneles">
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
              <div class="fw-bold">Reclamos pendientes</div>
              <div class="text-muted small">Los más recientes.</div>
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

    <!-- CURSOS: vista actual -->
    <div v-else>
      <div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
        <div class="row g-2 align-items-center">
          <div class="col-md-8">
            <input
              v-model="searchQuery"
              @keyup.enter="applyFilter"
              type="text"
              class="form-control custom-input"
              placeholder="Filtrar tus cursos..."
            />
          </div>
          <div class="col-md-4 d-flex gap-2 justify-content-md-end">
            <button class="btn btn-light border fw-medium action-btn" @click="clearFilter" :disabled="!searchQuery">Limpiar</button>
            <button class="btn btn-primary-custom fw-bold" @click="applyFilter">Filtrar</button>
          </div>
        </div>
      </div>

      <div v-if="loading" class="row g-4">
        <div class="col-md-4" v-for="i in 3" :key="i">
          <div class="card border-0 rounded-4 shadow-sm p-4 h-100 placeholder-glow">
            <div class="placeholder col-8 mb-3 py-3 rounded"></div>
            <div class="placeholder col-5 mb-2"></div>
            <div class="placeholder col-6"></div>
          </div>
        </div>
      </div>

      <div v-else-if="cursos.length === 0" class="text-center py-5">
        <div class="opacity-50 mb-3">
          <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
        </div>
        <h4 class="text-muted fw-bold">Aun no tienes cursos</h4>
        <p class="text-muted">Usa un codigo de invitacion para unirte a tu primer curso.</p>
      </div>

      <div v-else class="row g-4">
        <div class="col-12 col-md-6 col-xl-4" v-for="(curso, index) in cursos" :key="curso.id">
          <div class="card border-0 shadow-sm h-100 overflow-hidden course-card">
            <div class="course-banner text-white p-4 position-relative" :class="`banner-color-${index % 4}`">
              <h4 class="fw-bold mb-1 position-relative z-1 text-truncate">{{ curso.nombre }}</h4>
              <p class="opacity-75 m-0 position-relative z-1 text-truncate">{{ curso.descripcion || 'Sin descripcion' }}</p>
              <div class="abstract-pattern"></div>
            </div>
            <div class="card-body p-4">
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted fw-medium">Promedio</span>
                <strong>{{ curso.promedio_general ?? 'N/A' }}</strong>
              </div>
              <div class="d-flex justify-content-between mb-3">
                <span class="text-muted fw-medium">Estado</span>
                <span class="badge" :class="getEstadoClass(curso.promedio_general)"> {{ getEstado(curso.promedio_general) }} </span>
              </div>
              <button type="button" class="btn btn-light border w-100 fw-medium action-btn" @click="abrirCurso(curso.id)">Abrir curso</button>
            </div>
          </div>
        </div>
      </div>
      <div v-if="!loading && cursos.length > 0" class="d-flex justify-content-between align-items-center mt-4">
        <small class="text-muted">Mostrando {{ cursos.length }} de {{ totalCursos }} cursos</small>
        <div class="d-flex align-items-center gap-2">
          <button type="button" class="btn btn-sm btn-light border action-btn" @click="goToPage(currentPage - 1)" :disabled="currentPage <= 1">Anterior</button>
          <span class="small text-muted">Pagina {{ currentPage }} de {{ lastPage }}</span>
          <button type="button" class="btn btn-sm btn-light border action-btn" @click="goToPage(currentPage + 1)" :disabled="currentPage >= lastPage">Siguiente</button>
        </div>
      </div>
    </div>

    <div v-if="showJoinModal" class="modal-overlay d-flex align-items-center justify-content-center">
      <div class="card border-0 shadow-lg rounded-4 p-4 modal-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="fw-bold m-0">Unirme a un curso</h5>
          <button class="btn-close" @click="closeJoinModal"></button>
        </div>
        <p class="text-muted small">Ingresa el codigo de invitacion que te compartio el profesor.</p>
        <input
          v-model.trim="codigoInvitacion"
          type="text"
          class="form-control custom-input mb-3"
          placeholder="Ej: AB12CD34"
          maxlength="20"
        />
        <div class="d-flex justify-content-end gap-2">
          <button class="btn btn-light border action-btn" @click="closeJoinModal">Cancelar</button>
          <button class="btn btn-primary-custom" @click="unirmeCurso" :disabled="joining || !codigoInvitacion">
            <span v-if="joining" class="spinner-border spinner-border-sm me-2"></span>
            Unirme
          </button>
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
const userName = ref('Estudiante')
const loading = ref(true)
const error = ref('')
const cursos = ref([])
const searchQuery = ref('')
const currentPage = ref(1)
const lastPage = ref(1)
const totalCursos = ref(0)
const showJoinModal = ref(false)
const codigoInvitacion = ref('')
const joining = ref(false)

const activeTab = ref('inicio')
const loadingPaneles = ref(false)
const ultimasCalificaciones = ref([])
const reclamosPendientes = ref([])
const reclamosPendientesCount = ref(0)
const cursoDestinoReclamos = ref(null)

onMounted(async () => {
  try {
    const user = JSON.parse(localStorage.getItem('user'))
    if (user?.nombre) userName.value = user.nombre
  } catch (_) {}

  await loadCursos()
  sincronizarTabDesdeRuta()
  await refrescarPaneles()
})

watch(
  () => route.query.tab,
  () => {
    sincronizarTabDesdeRuta()
  }
)

function sincronizarTabDesdeRuta() {
  activeTab.value = route.query.tab === 'cursos' ? 'cursos' : 'inicio'
}

function irATab(tab) {
  activeTab.value = tab
  if (tab === 'cursos') {
    router.replace({ name: 'DashboardEstudiante', query: { tab: 'cursos' } })
  } else {
    router.replace({ name: 'DashboardEstudiante' })
  }
}

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
    error.value = 'No se pudieron cargar tus cursos: ' + e.message
  } finally {
    loading.value = false
  }
}

function abrirCurso(id) {
  router.push(`/estudiante/curso/${id}`)
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

function closeJoinModal() {
  showJoinModal.value = false
  codigoInvitacion.value = ''
}

async function unirmeCurso() {
  joining.value = true
  error.value = ''
  try {
    const user = JSON.parse(localStorage.getItem('user') || 'null')
    if (!user?.id) throw new Error('Sesion invalida. Inicia sesion de nuevo.')
    await apiRequest('/api/cursos/unirse', 'POST', {
      codigo_invitacion: codigoInvitacion.value,
      id_estudiante: user.id,
    })
    closeJoinModal()
    await loadCursos(1)
  } catch (e) {
    error.value = 'No fue posible unirte al curso: ' + e.message
  } finally {
    joining.value = false
  }
}

async function refrescarPaneles() {
  loadingPaneles.value = true
  try {
    const user = JSON.parse(localStorage.getItem('user') || 'null')
    const userId = user?.id
    if (!userId) return

    const ids = (cursos.value || []).map(c => c.id).filter(Boolean)
    if (!ids.length) {
      ultimasCalificaciones.value = []
      reclamosPendientes.value = []
      reclamosPendientesCount.value = 0
      cursoDestinoReclamos.value = null
      return
    }

    const tareasPorCurso = await Promise.all(ids.map(id => apiRequest(`/api/tareas/curso/${id}`)))
    const flat = []
    tareasPorCurso.forEach((tareas, i) => {
      const cursoNombre = cursos.value[i]?.nombre || `Curso ${ids[i]}`
      ;(tareas || []).forEach(t => {
        const nota = (t.notas || []).find(n => Number(n.id_estudiante) === Number(userId))
        if (!nota) return
        flat.push({
          curso_id: ids[i],
          curso: cursoNombre,
          tarea: t.nombre || `Tarea ${t.id}`,
          nota: Number(nota.nota),
          updated_at: nota.updated_at,
        })
      })
    })
    flat.sort((a, b) => new Date(b.updated_at).getTime() - new Date(a.updated_at).getTime())
    ultimasCalificaciones.value = flat.slice(0, 8)

    const recs = await apiRequest(`/api/reclamos/estudiante/${userId}`)
    const pendientes = (recs || [])
      .filter(r => String(r.estado || '').toLowerCase() === 'pendiente')
      .map(r => ({
        id: r.id,
        created_at: r.created_at,
        mensaje: r.mensaje,
        curso_id: r?.nota?.tarea?.id_curso,
        titulo: `${r?.nota?.tarea?.nombre || 'Tarea'} · Reclamo #${r.id}`,
      }))
      .sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime())

    reclamosPendientesCount.value = pendientes.length
    reclamosPendientes.value = pendientes.slice(0, 6)
    cursoDestinoReclamos.value = pendientes[0]?.curso_id || ids[0] || null
  } catch (e) {
    console.warn('No se pudieron cargar paneles del estudiante', e)
  } finally {
    loadingPaneles.value = false
  }
}

function irAReclamos() {
  const cid = cursoDestinoReclamos.value
  if (!cid) {
    irATab('cursos')
    return
  }
  router.push({ path: `/estudiante/curso/${cid}`, query: { tab: 'reclamos' } })
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

function getEstado(promedio) {
  const val = parseFloat(promedio)
  if (isNaN(val)) return 'Sin notas'
  if (val >= 4.0) return 'Aprobando'
  if (val >= 3.0) return 'En riesgo'
  return 'Critico'
}

function getEstadoClass(promedio) {
  const val = parseFloat(promedio)
  if (isNaN(val)) return 'bg-light text-muted border'
  if (val >= 4.0) return 'bg-success text-white'
  if (val >= 3.0) return 'bg-warning text-dark'
  return 'bg-danger text-white'
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

.course-card {
  border-radius: 16px;
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}
.course-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1) !important;
}
.course-banner {
  min-height: 120px;
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
  background: #1e40af;
  color: white;
  border: none;
  border-radius: 10px;
  transition: all 0.3s ease;
}
.btn-primary-custom:hover:not(:disabled) {
  background: #1e3a8a;
  transform: translateY(-2px);
  box-shadow: 0 8px 15px rgba(30, 64, 175, 0.2);
  color: white;
}
.action-btn {
  border-radius: 10px;
  transition: all 0.2s ease;
}
.action-btn:hover {
  background-color: #f1f5f9;
  border-color: #cbd5e1 !important;
}
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.55);
  backdrop-filter: blur(2px);
  z-index: 1050;
}
.modal-card {
  width: 100%;
  max-width: 460px;
}
</style>
