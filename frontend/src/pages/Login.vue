<template>
  <div class="auth-wrapper d-flex align-items-center justify-content-center min-vh-100">
    <div class="auth-card shadow-lg d-flex overflow-hidden">
      <!-- Left Branding Side -->
      <div class="auth-brand d-none d-md-flex flex-column justify-content-center align-items-center p-5 text-white">
        <div class="brand-content text-center">
          <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-4 opacity-75"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
          <h2 class="fw-bold mb-3">Gestor Académico</h2>
          <p class="opacity-75 fs-5">Tu plataforma moderna para la gestión de calificaciones y progreso estudiantil.</p>
        </div>
      </div>
      
      <!-- Right Form Side -->
      <div class="auth-form-container p-4 p-md-5 d-flex flex-column justify-content-center">
        <div class="text-center mb-4 pb-2">
          <h3 class="fw-bold text-primary-custom mb-2">¡Bienvenido de vuelta!</h3>
          <p class="text-muted">Ingresa tus credenciales para continuar</p>
        </div>

        <!-- Alertas de estado -->
        <div v-if="errorMessage" class="alert alert-danger custom-alert" role="alert">
          {{ errorMessage }}
        </div>
        <div v-if="successMessage" class="alert alert-success custom-alert" role="alert">
          {{ successMessage }}
        </div>

        <form @submit.prevent="onLogin" class="auth-form">
          <div class="form-floating mb-3">
            <input type="email" class="form-control custom-input" id="email" placeholder="nombre@ejemplo.com" v-model="email" required :disabled="loading" />
            <label for="email">Correo electrónico</label>
          </div>
          
          <div class="form-floating mb-3">
            <input type="password" class="form-control custom-input" id="password" placeholder="Contraseña" v-model="password" required :disabled="loading" />
            <label for="password">Contraseña</label>
          </div>

          <button type="submit" class="btn btn-primary-custom w-100 py-3 fw-bold mb-4 d-flex justify-content-center align-items-center gap-2" :disabled="loading">
            <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span v-else>Iniciar Sesión</span>
            <svg v-if="!loading" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
          </button>
        </form>

        <div class="text-center mt-2">
          <span class="text-muted">¿No tienes cuenta?</span>
          <router-link to="/register" class="text-secondary-custom fw-bold ms-1 text-decoration-none">Regístrate aquí</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { apiRequest } from '../api.js'
import { useRouter } from 'vue-router'

const email = ref('')
const password = ref('')
const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const router = useRouter()

async function onLogin() {
  loading.value = true
  errorMessage.value = ''
  successMessage.value = ''
  
  try {
    const res = await apiRequest('/api/login', 'POST', {
      email: email.value,
      password: password.value
    })
    
    // Si la API responde con un error manual (por si acaso no tira excepción HTTP)
    if (res.error) {
      errorMessage.value = res.error
      return
    }
    
    // Guardar token real emitido por Sanctum
    localStorage.setItem('token', res.token)
    localStorage.setItem('user', JSON.stringify(res.usuario))
    
    successMessage.value = '¡Login exitoso! Redirigiendo...'
    setTimeout(() => {
      const nextPath = res.usuario?.rol === 'estudiante' ? '/estudiante/dashboard' : '/dashboard'
      router.push(nextPath)
    }, 1000)
    
  } catch (e) {
    try {
      const errorObj = JSON.parse(e.message)
      errorMessage.value = errorObj.error || errorObj.message || 'Credenciales inválidas.'
    } catch {
      errorMessage.value = 'Error al conectar con el servidor.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.auth-wrapper {
  background-color: #f8f9fa;
  font-family: 'Inter', sans-serif;
}

.auth-card {
  background: #ffffff;
  border-radius: 24px;
  max-width: 1000px;
  width: 100%;
  margin: 1.5rem;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08) !important;
  border: 1px solid rgba(0,0,0,0.05);
}

.auth-brand {
  background: linear-gradient(135deg, #1e40af 0%, #6b21a8 100%);
  width: 45%;
  position: relative;
  overflow: hidden;
}

.auth-brand::before {
  content: "";
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: url('data:image/svg+xml;utf8,<svg width="20" height="20" xmlns="http://www.w3.org/2000/svg"><circle cx="2" cy="2" r="2" fill="rgba(255,255,255,0.05)"/></svg>') repeat;
  z-index: 1;
}

.brand-content {
  position: relative;
  z-index: 2;
}

.auth-form-container {
  width: 55%;
}

@media (max-width: 768px) {
  .auth-form-container {
    width: 100%;
  }
}

.text-primary-custom {
  color: #1e40af;
}

.text-secondary-custom {
  color: #6b21a8;
  transition: color 0.3s ease;
}

.text-secondary-custom:hover {
  color: #1e40af;
}

.custom-input {
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  box-shadow: none !important;
  transition: all 0.3s ease;
  height: 3.5rem;
}

.custom-input:focus {
  border-color: #1e40af;
  box-shadow: 0 0 0 4px rgba(30, 64, 175, 0.1) !important;
}

.form-floating > label {
  padding-left: 1rem;
}

.btn-primary-custom {
  background: #1e40af;
  color: white;
  border: none;
  border-radius: 12px;
  transition: all 0.3s ease;
}

.btn-primary-custom:hover:not(:disabled) {
  background: #1e3a8a;
  transform: translateY(-2px);
  box-shadow: 0 8px 15px rgba(30, 64, 175, 0.2);
  color: white;
}

.auth-form label {
  color: #64748b;
}

.custom-alert {
  border-radius: 12px;
  font-size: 0.95rem;
}
</style>
