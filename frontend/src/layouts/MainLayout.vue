<template>
  <div class="d-flex min-vh-100 bg-light-custom">
    <!-- Sidebar -->
    <aside class="sidebar d-flex flex-column bg-white shadow-sm">
      <div class="p-4 border-bottom d-flex align-items-center gap-3">
        <div class="brand-icon rounded text-white d-flex align-items-center justify-content-center fw-bold fs-4">
          G
        </div>
        <h4 class="fw-bold m-0 text-primary-custom d-none d-md-block">Gestor</h4>
      </div>
      
      <nav class="flex-grow-1 p-3">
        <div class="text-muted small fw-bold mb-3 px-3 text-capitalize">{{ userRoleLabel }}</div>
        
        <router-link
          :to="dashboardInicioPath"
          class="nav-item-custom mb-2 text-decoration-none"
          :class="{ active: sidebarInicioActivo }"
        >
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
          <span class="d-none d-md-inline ms-3 fw-medium">Dashboard</span>
        </router-link>
        
        <router-link
          :to="cursosPath"
          class="nav-item-custom mb-2 text-decoration-none"
          :class="{ active: sidebarCursosActivo }"
        >
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
          <span class="d-none d-md-inline ms-3 fw-medium">Cursos</span>
        </router-link>
      </nav>
      
      <div class="p-3 border-top mt-auto">
        <button @click="logout" class="nav-item-custom text-danger text-decoration-none border-0 bg-transparent w-100 text-start">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
          <span class="d-none d-md-inline ms-3 fw-medium">Cerrar Sesión</span>
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-grow-1 d-flex flex-column overflow-hidden">
      <!-- Top Navbar -->
      <header class="top-navbar bg-white border-bottom px-4 py-3 d-flex align-items-center justify-content-between sticky-top">
        <div class="d-flex align-items-center gap-3">
          <!-- Breadcrumb base -->
          <div class="text-muted fw-medium d-none d-sm-block">
            Académico <span class="mx-2">/</span> <span class="text-dark">Mis Cursos</span>
          </div>
        </div>

        <div class="d-flex align-items-center gap-4">
          <!-- Search -->
          <div class="position-relative d-none d-lg-block">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="position-absolute ms-3" style="top: 50%; transform: translateY(-50%)"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input type="text" class="form-control search-input" placeholder="Buscar curso...">
          </div>
          
          <!-- Perfil User (Leído dinámicamente) -->
          <div class="d-flex align-items-center gap-3 border-start ps-4">
            <div class="text-end d-none d-sm-block">
              <div class="fw-bold text-dark lh-sm">{{ userName }}</div>
              <small class="text-muted text-capitalize">{{ userRole }}</small>
            </div>
            <div class="avatar rounded-circle text-white d-flex align-items-center justify-content-center fw-bold fs-5">
              {{ userName.charAt(0).toUpperCase() }}
            </div>
          </div>
        </div>
      </header>

      <!-- Content Area -->
      <div class="p-4 overflow-auto bg-light-custom flex-grow-1">
        <router-view />
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { apiRequest } from '../api.js'

const router = useRouter()
const route = useRoute()
const userName = ref('')
const userRole = ref('')
const userRoleLabel = computed(() => {
  const r = String(userRole.value || '').toLowerCase()
  if (r === 'estudiante') return 'Estudiante'
  if (r === 'profesor') return 'Profesor'
  return 'Usuario'
})
/** Rutas con nombre para que el query `tab` dispare navegación real al hacer clic en Cursos. */
const dashboardInicioPath = ref({ name: 'Dashboard' })
const cursosPath = ref({ name: 'Dashboard', query: { tab: 'cursos' } })

const sidebarInicioActivo = computed(() => {
  if (route.name !== 'Dashboard' && route.name !== 'DashboardEstudiante') return false
  return route.query.tab !== 'cursos'
})

const sidebarCursosActivo = computed(() => {
  if (route.name !== 'Dashboard' && route.name !== 'DashboardEstudiante') return false
  return route.query.tab === 'cursos'
})

onMounted(() => {
  try {
    const user = JSON.parse(localStorage.getItem('user'))
    if (user) {
      userName.value = user.nombre || 'Usuario'
      userRole.value = user.rol || 'Rol'
      if (user.rol === 'estudiante') {
        dashboardInicioPath.value = { name: 'DashboardEstudiante' }
        cursosPath.value = { name: 'DashboardEstudiante', query: { tab: 'cursos' } }
      } else {
        dashboardInicioPath.value = { name: 'Dashboard' }
        cursosPath.value = { name: 'Dashboard', query: { tab: 'cursos' } }
      }
    }
  } catch (e) {
    userName.value = 'Usuario'
  }
})

async function logout() {
  try {
    // Intentar logout en el servidor para invalidar el token
    await apiRequest('/api/logout', 'POST')
  } catch (e) {
    console.warn('Error logueando en servidor', e)
  }
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  router.push('/')
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.bg-light-custom {
  background-color: #f3f4f6;
  font-family: 'Inter', sans-serif;
}

.text-primary-custom {
  color: #1e40af;
}

/* Sidebar */
.sidebar {
  width: 280px;
  min-width: 280px;
  transition: width 0.3s ease;
}

@media (max-width: 768px) {
  .sidebar {
    width: 80px;
    min-width: 80px;
  }
}

.brand-icon {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #1e40af 0%, #6b21a8 100%);
}

.nav-item-custom {
  display: flex;
  align-items: center;
  padding: 0.8rem 1.2rem;
  border-radius: 12px;
  color: #64748b;
  transition: all 0.2s ease;
  cursor: pointer;
}

.nav-item-custom:hover {
  background-color: #f8fafc;
  color: #1e40af;
}

.nav-item-custom.active {
  background-color: #eff6ff;
  color: #1e40af;
  font-weight: 600;
}

/* Navbar */
.search-input {
  padding-left: 2.5rem;
  border-radius: 20px;
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  width: 300px;
}

.search-input:focus {
  background: #ffffff;
  border-color: #cbd5e1;
  box-shadow: none;
}

.avatar {
  width: 42px;
  height: 42px;
  background: linear-gradient(135deg, #1e40af 0%, #6b21a8 100%);
}
</style>
