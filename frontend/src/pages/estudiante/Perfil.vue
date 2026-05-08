<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-5 mt-2">
      <div>
        <h3 class="fw-bold mb-1">Perfil estudiante</h3>
        <p class="text-muted m-0">Datos personales, contraseña y preferencias</p>
      </div>
      <button class="btn btn-light border fw-medium action-btn" @click="router.push('/estudiante/dashboard')">Volver</button>
    </div>

    <div v-if="error" class="alert alert-danger">{{ error }}</div>
    <div v-if="success" class="alert alert-success">{{ success }}</div>

    <div class="row g-4">
      <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 p-4">
          <h5 class="fw-bold mb-3">Datos personales</h5>
          <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input class="form-control custom-input" v-model="perfil.nombre" />
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input class="form-control custom-input" v-model="perfil.email" />
          </div>
          <button class="btn btn-primary-custom fw-bold" @click="guardarPerfil">Guardar datos</button>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
          <h5 class="fw-bold mb-3">Cambiar contraseña</h5>
          <div class="mb-3">
            <label class="form-label">Contraseña actual</label>
            <input type="password" class="form-control custom-input" v-model="password.actual" />
          </div>
          <div class="mb-3">
            <label class="form-label">Nueva contraseña</label>
            <input type="password" class="form-control custom-input" v-model="password.nueva" />
          </div>
          <button class="btn btn-primary-custom fw-bold" @click="cambiarPassword">Actualizar contraseña</button>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4">
          <h5 class="fw-bold mb-3">Preferencias</h5>
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="darkSwitch" v-model="darkMode" @change="guardarPreferencias">
            <label class="form-check-label" for="darkSwitch">Tema oscuro</label>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { apiRequest } from '../../api.js'

const router = useRouter()
const error = ref('')
const success = ref('')
const darkMode = ref(false)
const perfil = ref({ nombre: '', email: '' })
const password = ref({ actual: '', nueva: '' })

onMounted(async () => {
  darkMode.value = localStorage.getItem('pref_dark_mode') === '1'
  applyDarkMode()
  try {
    const me = await apiRequest('/api/me')
    perfil.value.nombre = me.nombre || ''
    perfil.value.email = me.email || ''
  } catch (e) {
    error.value = 'No se pudo cargar el perfil: ' + e.message
  }
})

async function guardarPerfil() {
  error.value = ''
  success.value = ''
  try {
    const res = await apiRequest('/api/me', 'PUT', perfil.value)
    localStorage.setItem('user', JSON.stringify(res.usuario))
    success.value = 'Datos actualizados correctamente.'
  } catch (e) {
    error.value = 'No se pudieron actualizar los datos: ' + e.message
  }
}

async function cambiarPassword() {
  error.value = ''
  success.value = ''
  try {
    await apiRequest('/api/me/password', 'PUT', {
      password_actual: password.value.actual,
      password_nueva: password.value.nueva,
    })
    password.value = { actual: '', nueva: '' }
    success.value = 'Contraseña actualizada correctamente.'
  } catch (e) {
    error.value = 'No se pudo actualizar la contraseña: ' + e.message
  }
}

function guardarPreferencias() {
  localStorage.setItem('pref_dark_mode', darkMode.value ? '1' : '0')
  applyDarkMode()
}

function applyDarkMode() {
  const root = document.documentElement
  if (darkMode.value) {
    root.style.filter = 'invert(1) hue-rotate(180deg)'
  } else {
    root.style.filter = ''
  }
}
</script>

<style scoped>
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
</style>
