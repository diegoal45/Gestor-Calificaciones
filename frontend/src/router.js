import { createRouter, createWebHistory } from 'vue-router'
import Login from './pages/Login.vue'
import Register from './pages/Register.vue'
import MainLayout from './layouts/MainLayout.vue'
import Dashboard from './pages/profesor/Dashboard.vue'
import DashboardEstudiante from './pages/estudiante/Dashboard.vue'
import CursoEstudiante from './pages/estudiante/Curso.vue'
import PerfilEstudiante from './pages/estudiante/Perfil.vue'
import CursoLayout from './layouts/CursoLayout.vue'
import Resumen from './pages/profesor/curso/Resumen.vue'
import Planilla from './pages/profesor/curso/Planilla.vue'
import Tareas from './pages/profesor/curso/Tareas.vue'
import Estudiantes from './pages/profesor/curso/Estudiantes.vue'
import Rubricas from './pages/profesor/curso/Rubricas.vue'
import Asistencia from './pages/profesor/curso/Asistencia.vue'
import Analisis from './pages/profesor/curso/Analisis.vue'
import Exportaciones from './pages/profesor/curso/Exportaciones.vue'
import Reclamos from './pages/profesor/curso/Reclamos.vue'
import Calificar from './pages/profesor/curso/Calificar.vue'
const routes = [
  { path: '/', name: 'Login', component: Login },
  { path: '/register', name: 'Register', component: Register },
  { 
    path: '/dashboard', 
    component: MainLayout,
    children: [
      { path: '', name: 'Dashboard', component: Dashboard }
    ]
  },
  {
    path: '/estudiante/dashboard',
    component: MainLayout,
    children: [
      { path: '', name: 'DashboardEstudiante', component: DashboardEstudiante }
    ]
  },
  {
    path: '/estudiante/curso/:id',
    component: MainLayout,
    children: [
      { path: '', name: 'CursoEstudiante', component: CursoEstudiante }
    ]
  },
  {
    path: '/estudiante/perfil',
    component: MainLayout,
    children: [
      { path: '', name: 'PerfilEstudiante', component: PerfilEstudiante }
    ]
  },
  {
    path: '/curso/:id',
    component: MainLayout,
    children: [
      {
        path: '',
        component: CursoLayout,
        children: [
          { path: '', redirect: { name: 'CursoResumen' } },
          { path: 'resumen', name: 'CursoResumen', component: Resumen },
          { path: 'planilla', name: 'CursoPlanilla', component: Planilla },
          { path: 'tareas', name: 'CursoTareas', component: Tareas },
          { path: 'estudiantes', name: 'CursoEstudiantes', component: Estudiantes },
          { path: 'rubricas', name: 'CursoRubricas', component: Rubricas },
          { path: 'asistencia', name: 'CursoAsistencia', component: Asistencia },
          { path: 'analisis', name: 'CursoAnalisis', component: Analisis },
          { path: 'exportaciones', name: 'CursoExportaciones', component: Exportaciones },
          { path: 'reclamos', name: 'CursoReclamos', component: Reclamos },
          { path: 'calificar', name: 'CursoCalificar', component: Calificar },
        ]
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// Protección de rutas simple
router.beforeEach((to, from, next) => {
  const isAuthenticated = !!localStorage.getItem('token')
  const user = JSON.parse(localStorage.getItem('user') || 'null')
  const rol = user?.rol

  if (to.path !== '/' && to.path !== '/register' && !isAuthenticated) {
    next({ name: 'Login' })
  } else if ((to.path === '/' || to.path === '/register') && isAuthenticated) {
    next({ name: rol === 'estudiante' ? 'DashboardEstudiante' : 'Dashboard' })
  } else if (rol === 'estudiante' && (to.path.startsWith('/dashboard') || to.path.startsWith('/curso/'))) {
    next({ name: 'DashboardEstudiante' })
  } else if (rol === 'profesor' && to.path.startsWith('/estudiante/')) {
    next({ name: 'Dashboard' })
  } else {
    next()
  }
})

export default router
