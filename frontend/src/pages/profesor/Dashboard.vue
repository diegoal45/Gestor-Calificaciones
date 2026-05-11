<template>
  <div>
    <!-- Header del Dashboard -->
    <div class="d-flex justify-content-between align-items-center mb-5 mt-2">
      <div>
        <h2 class="fw-bold m-0 text-dark">Hola, {{ userName }} <span class="wave-emoji">👋</span></h2>
        <p class="text-muted mt-1 fs-5">Semestre 2026-1</p>
      </div>
      <button class="btn btn-primary-custom d-flex align-items-center gap-2 fw-bold px-4 py-2" @click="crearCurso">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Crear curso
      </button>
    </div>

    <!-- Error state -->
    <div v-if="error" class="alert alert-danger shadow-sm border-0 rounded-4">
      {{ error }}
    </div>

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

            <!-- Eliminado progreso estático -->

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
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { apiRequest } from '../../api.js'

const router = useRouter()
const userName = ref('')
const cursos = ref([])
const loading = ref(true)
const error = ref('')
const searchQuery = ref('')
const currentPage = ref(1)
const lastPage = ref(1)
const totalCursos = ref(0)

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
})

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

function crearCurso() {
  newCurso.value = { nombre: '', descripcion: '', nota_minima: 3.0, nota_maxima: 5.0, metodo_calificacion: 'ponderacion' }
  showCreateModal.value = true
}

async function guardarNuevoCurso() {
  savingCurso.value = true
  try {
    await apiRequest('/api/cursos', 'POST', newCurso.value)
    await loadCursos(1)
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
