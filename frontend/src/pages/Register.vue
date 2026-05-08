<template>
  <div class="auth-wrapper d-flex align-items-center justify-content-center min-vh-100">
    <div class="auth-card shadow-lg d-flex overflow-hidden">
      <!-- Left Branding Side -->
      <div class="auth-brand d-none d-md-flex flex-column justify-content-center align-items-center p-5 text-white">
        <div class="brand-content text-center">
          <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-4 opacity-75"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
          <h2 class="fw-bold mb-3">Únete Hoy</h2>
          <p class="opacity-75 fs-5">Empieza a gestionar tus calificaciones de forma inteligente y rápida.</p>
        </div>
      </div>
      
      <!-- Right Form Side -->
      <div class="auth-form-container p-4 p-md-5 d-flex flex-column justify-content-center">
        <div class="text-center mb-4">
          <h3 class="fw-bold text-primary-custom mb-2">Crear Cuenta</h3>
          <p class="text-muted">Completa tus datos para registrarte</p>
        </div>

        <!-- Alertas de estado -->
        <div v-if="errorMessage" class="alert alert-danger custom-alert" role="alert">
          {{ errorMessage }}
        </div>
        <div v-if="successMessage" class="alert alert-success custom-alert" role="alert">
          {{ successMessage }}
        </div>

        <form @submit.prevent="onRegister" class="auth-form">
          <div class="form-floating mb-3">
            <input type="text" class="form-control custom-input" id="name" placeholder="Tu nombre" v-model="name" required :disabled="loading" />
            <label for="name">Nombre completo</label>
          </div>
          
          <div class="form-floating mb-3">
            <input type="email" class="form-control custom-input" id="email" placeholder="nombre@ejemplo.com" v-model="email" required :disabled="loading" />
            <label for="email">Correo electrónico</label>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <div class="form-floating">
                <input type="password" class="form-control custom-input" id="password" placeholder="Contraseña" v-model="password" required :disabled="loading" />
                <label for="password">Contraseña</label>
              </div>
            </div>
            <div class="col-6">
              <div class="form-floating">
                <input type="password" class="form-control custom-input" id="confirmPassword" placeholder="Confirmar" v-model="confirmPassword" required :disabled="loading" />
                <label for="confirmPassword">Confirmar</label>
              </div>
            </div>
          </div>

          <div class="form-floating mb-4 pb-2">
            <select id="role" class="form-select custom-input" v-model="role" required :disabled="loading">
              <option value="" disabled>Selecciona tu rol</option>
              <option value="profesor">👨‍🏫 Profesor</option>
              <option value="estudiante">🎓 Estudiante</option>
            </select>
            <label for="role">Tipo de usuario</label>
          </div>

          <button type="submit" class="btn btn-primary-custom w-100 py-3 fw-bold mb-4 d-flex justify-content-center align-items-center gap-2" :disabled="loading">
            <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span v-else>Registrarse</span>
            <svg v-if="!loading" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5.5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
          </button>
        </form>

        <div class="text-center mt-2">
          <span class="text-muted">¿Ya tienes cuenta?</span>
          <router-link to="/" class="text-secondary-custom fw-bold ms-1 text-decoration-none">Inicia sesión aquí</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { apiRequest } from '../api.js'
import { useRouter } from 'vue-router'

const name = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const role = ref('')
const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const router = useRouter()

async function onRegister() {
  errorMessage.value = ''
  successMessage.value = ''
  
  if (password.value !== confirmPassword.value) {
    errorMessage.value = 'Las contraseñas no coinciden'
    return
  }
  if (!role.value) {
    errorMessage.value = 'Selecciona un rol'
    return
  }
  
  loading.value = true
  try {
    // IMPORTANTE: El backend espera 'nombre' y 'rol'
    const res = await apiRequest('/api/register', 'POST', {
      nombre: name.value,
      email: email.value,
      password: password.value,
      rol: role.value
    })
    
    // Guardar token real emitido por Sanctum
    localStorage.setItem('token', res.token)
    localStorage.setItem('user', JSON.stringify(res.usuario))
    
    successMessage.value = '¡Registro exitoso! Redirigiendo...'
    setTimeout(() => {
      router.push('/dashboard')
    }, 1500)
    
  } catch (e) {
    try {
      const errorObj = JSON.parse(e.message)
      // Si el servidor envía errores de validación, solemos tener un objeto 'errors'
      if (errorObj.errors) {
        const firstError = Object.values(errorObj.errors)[0][0]
        errorMessage.value = firstError
      } else {
        errorMessage.value = errorObj.message || 'Error en el registro.'
      }
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
