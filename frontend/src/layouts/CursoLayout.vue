<template>
  <div class="curso-layout d-flex flex-column h-100">
    <!-- Header del Curso (Banner) -->
    <div class="course-header text-white p-4 position-relative" :class="bannerColor">
      <div class="d-flex justify-content-between align-items-end position-relative z-1">
        <div>
          <button @click="volverDashboard" class="btn btn-sm btn-light text-primary-custom fw-bold mb-3 d-flex align-items-center gap-2 border-0 opacity-75 hover-opacity-100 transition">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Volver
          </button>
          
          <h2 class="fw-bold mb-1">{{ curso?.nombre || 'Cargando curso...' }}</h2>
          <p class="opacity-75 m-0">{{ curso?.descripcion || '---' }}</p>
        </div>
        
        <div class="d-flex gap-4 text-end">
          <div>
            <div class="fs-4 fw-bold">{{ curso?.estudiantes_count || 0 }}</div>
            <div class="small opacity-75">Estudiantes</div>
          </div>
          <div>
            <div class="fs-4 fw-bold">{{ curso?.promedio_general || 'N/A' }}</div>
            <div class="small opacity-75">Promedio general</div>
          </div>
        </div>
      </div>
      <div class="abstract-pattern"></div>
    </div>

    <!-- Tabs de Navegación -->
    <div class="bg-white border-bottom px-4 sticky-top z-2">
      <ul class="nav nav-tabs custom-tabs border-0 gap-3">
        <li class="nav-item">
          <router-link :to="`/curso/${cursoId}/resumen`" class="nav-link py-3 text-muted fw-medium" active-class="active">Resumen</router-link>
        </li>
        <li class="nav-item">
          <router-link :to="`/curso/${cursoId}/planilla`" class="nav-link py-3 text-muted fw-medium" active-class="active">Planilla</router-link>
        </li>
        <li class="nav-item">
          <router-link :to="`/curso/${cursoId}/tareas`" class="nav-link py-3 text-muted fw-medium" active-class="active">Tareas</router-link>
        </li>
        <li class="nav-item">
          <router-link :to="`/curso/${cursoId}/estudiantes`" class="nav-link py-3 text-muted fw-medium" active-class="active">Estudiantes</router-link>
        </li>
        <li class="nav-item">
          <router-link :to="`/curso/${cursoId}/rubricas`" class="nav-link py-3 text-muted fw-medium" active-class="active">Rúbricas</router-link>
        </li>
        <li class="nav-item">
          <router-link :to="`/curso/${cursoId}/asistencia`" class="nav-link py-3 text-muted fw-medium" active-class="active">Asistencia</router-link>
        </li>
        <li class="nav-item">
          <router-link :to="`/curso/${cursoId}/analisis`" class="nav-link py-3 text-muted fw-medium" active-class="active">Análisis</router-link>
        </li>
        <li class="nav-item ms-auto">
          <router-link :to="`/curso/${cursoId}/exportaciones`" class="nav-link py-3 text-muted fw-medium d-flex align-items-center gap-1" active-class="active">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            Exportar
          </router-link>
        </li>
        <li class="nav-item">
          <router-link :to="`/curso/${cursoId}/reclamos`" class="nav-link py-3 text-muted fw-medium position-relative d-flex align-items-center gap-1" active-class="active">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            Reclamos
            <span class="position-absolute top-25 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="width: 8px; height: 8px;"></span>
          </router-link>
        </li>
      </ul>
    </div>

    <!-- Contenido del Tab -->
    <div class="flex-grow-1 p-4 overflow-auto">
      <div v-if="loading" class="d-flex justify-content-center align-items-center h-100">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Cargando...</span>
        </div>
      </div>
      <div v-else-if="error" class="alert alert-danger">
        {{ error }}
      </div>
      <router-view v-else :curso="curso" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { apiRequest } from '../api.js'

const route = useRoute()
const router = useRouter()
const cursoId = ref(route.params.id)
const curso = ref(null)
const loading = ref(true)
const error = ref('')

// Determinamos un color basado en el ID para mantener la estética
const bannerColor = computed(() => {
  const id = parseInt(cursoId.value) || 0
  return `banner-color-${id % 4}`
})

onMounted(async () => {
  try {
    // Obtener la info general del curso conectada al backend
    const res = await apiRequest(`/api/cursos/${cursoId.value}`)
    // El backend retorna { curso, resumen }, por compatibilidad usamos curso si existe.
    curso.value = res?.curso || res
  } catch (e) {
    error.value = 'No se pudo cargar la información del curso.'
  } finally {
    loading.value = false
  }
})

function volverDashboard() {
  router.push('/dashboard')
}
</script>

<style scoped>
/* Course header responsive */
.course-header {
  border-radius: 12px 12px 0 0;
  overflow: hidden;
  margin-top: -1rem;
  margin-left: -1rem;
  margin-right: -1rem;
}

@media (max-width: 768px) {
  .course-header {
    margin-top: -0.5rem;
    margin-left: -0.5rem;
    margin-right: -0.5rem;
  }
  
  .course-header .d-flex {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .course-header .d-flex .text-end {
    text-align: left !important;
  }
  
  .course-header .d-flex.gap-4 {
    flex-direction: row;
    justify-content: space-between;
    width: 100%;
  }
}

@media (max-width: 576px) {
  .course-header h2 {
    font-size: 1.5rem;
  }
  
  .course-header .fs-4 {
    font-size: 1.25rem;
  }
}

.banner-color-0 { background: linear-gradient(135deg, #2563eb, #1e40af); }
.banner-color-1 { background: linear-gradient(135deg, #059669, #047857); }
.banner-color-2 { background: linear-gradient(135deg, #9333ea, #7e22ce); }
.banner-color-3 { background: linear-gradient(135deg, #e11d48, #be123c); }

/* Abstract pattern responsive */
.abstract-pattern {
  position: absolute;
  top: 0;
  right: 0;
  width: 300px;
  height: 300px;
  background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
  border-radius: 50%;
  transform: translate(30%, -30%);
  z-index: 0;
}

@media (max-width: 768px) {
  .abstract-pattern {
    width: 200px;
    height: 200px;
    transform: translate(20%, -20%);
  }
}

@media (max-width: 576px) {
  .abstract-pattern {
    width: 150px;
    height: 150px;
    transform: translate(10%, -10%);
  }
}

.hover-opacity-100:hover {
  opacity: 1 !important;
}
.transition {
  transition: all 0.2s ease;
}

/* Tabs responsive */
.custom-tabs .nav-link {
  border: none;
  border-bottom: 3px solid transparent;
  color: #64748b;
  padding: 1rem 0.5rem;
  transition: all 0.2s;
  white-space: nowrap;
  font-size: 0.875rem;
}

@media (max-width: 992px) {
  .custom-tabs .nav-link {
    padding: 0.75rem 0.25rem;
    font-size: 0.8rem;
  }
}

@media (max-width: 768px) {
  .custom-tabs {
    overflow-x: auto;
    flex-wrap: nowrap;
    gap: 0.5rem;
  }
  
  .custom-tabs .nav-link {
    padding: 0.5rem 0.25rem;
    font-size: 0.75rem;
    min-width: fit-content;
  }
}

@media (max-width: 576px) {
  .custom-tabs {
    gap: 0.25rem;
  }
  
  .custom-tabs .nav-link {
    padding: 0.5rem 0.125rem;
    font-size: 0.7rem;
  }
  
  .custom-tabs .nav-link svg {
    width: 12px;
    height: 12px;
  }
}

.custom-tabs .nav-link:hover {
  border-color: #cbd5e1;
  color: #334155;
}

.custom-tabs .nav-link.active {
  color: #1e40af;
  border-color: #1e40af;
  font-weight: 600;
  background: transparent;
}
</style>
