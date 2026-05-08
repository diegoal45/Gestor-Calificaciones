<template>
  <div class="reclamos-container d-flex h-100 overflow-hidden">
    <!-- Lista de Reclamos -->
    <div class="w-50 border-end bg-white d-flex flex-column h-100">
      <div class="p-4 border-bottom">
        <h4 class="fw-bold mb-1">Centro de Reclamos</h4>
        <p class="text-muted small m-0">Gestiona dudas y solicitudes de notas</p>
      </div>

      <div class="p-3 bg-light border-bottom d-flex gap-2">
        <select class="form-select form-select-sm shadow-none border-0 bg-white" v-model="filtroEstado">
          <option value="Todos">Todos los estados</option>
          <option value="Pendiente">Pendientes</option>
          <option value="Respondido">Respondidos</option>
          <option value="Cerrado">Cerrados</option>
        </select>
      </div>
      <div v-if="error" class="alert alert-danger rounded-0 m-0 py-2 px-3">
        {{ error }}
      </div>

      <div class="flex-grow-1 overflow-auto">
        <div v-for="reclamo in reclamosFiltrados" :key="reclamo.id" 
             class="p-4 border-bottom cursor-pointer transition ticket-item"
             :class="{'bg-primary-light': selectedReclamo?.id === reclamo.id}"
             @click="seleccionarReclamo(reclamo)">
          
          <div class="d-flex justify-content-between align-items-start mb-2">
            <span class="badge rounded-pill" :class="getBadgeClass(reclamo.estado)">{{ reclamo.estado }}</span>
            <span class="small text-muted">{{ reclamo.fecha }}</span>
          </div>
          
          <h6 class="fw-bold mb-1 text-truncate">{{ reclamo.asunto }}</h6>
          <p class="small text-muted mb-2 text-truncate">{{ reclamo.mensaje }}</p>
          
          <div class="d-flex align-items-center gap-2 mt-2">
            <div class="avatar-xs bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:20px; height:20px; font-size:10px;">
              {{ reclamo.estudiante.charAt(0) }}
            </div>
            <span class="small fw-medium">{{ reclamo.estudiante }}</span>
            <span class="small text-muted ms-auto">{{ reclamo.tarea }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Chat / Detalle del Reclamo -->
    <div class="w-50 bg-light-custom d-flex flex-column h-100 position-relative">
      <div v-if="!selectedReclamo" class="m-auto text-center text-muted opacity-50">
        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="mb-3"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
        <h5>Selecciona un reclamo</h5>
        <p class="small">Para ver los detalles y responder</p>
      </div>

      <template v-else>
        <!-- Chat Header -->
        <div class="p-4 bg-white border-bottom shadow-sm z-1">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <h5 class="fw-bold m-0">{{ selectedReclamo.asunto }}</h5>
            <span class="badge" :class="getBadgeClass(selectedReclamo.estado)">{{ selectedReclamo.estado }}</span>
          </div>
          <div class="d-flex align-items-center gap-3 mt-3 text-muted small">
            <div><strong class="text-dark">Estudiante:</strong> {{ selectedReclamo.estudiante }}</div>
            <div><strong class="text-dark">Evaluación:</strong> {{ selectedReclamo.tarea }}</div>
          </div>
        </div>

        <!-- Chat History -->
        <div class="flex-grow-1 p-4 overflow-auto chat-bg d-flex flex-column gap-3">
          <!-- Mensaje Estudiante -->
          <div class="align-self-start max-w-80">
            <div class="small text-muted mb-1">{{ selectedReclamo.estudiante }} • {{ selectedReclamo.fecha }}</div>
            <div class="bg-white p-3 rounded-4 shadow-sm border rounded-top-0">
              {{ selectedReclamo.mensaje }}
            </div>
          </div>

          <!-- Respuestas (Mock) -->
          <div v-if="selectedReclamo.respuesta" class="align-self-end max-w-80">
            <div class="small text-muted mb-1 text-end">Tú • Respondió recientemente</div>
            <div class="bg-primary text-white p-3 rounded-4 shadow-sm rounded-top-0">
              {{ selectedReclamo.respuesta }}
            </div>
          </div>
        </div>

        <!-- Chat Input -->
        <div class="p-3 bg-white border-top">
          <div v-if="selectedReclamo.estado === 'Cerrado'" class="alert alert-secondary m-0 text-center py-2 border-0">
            Este reclamo ha sido cerrado.
          </div>
          <div v-else>
            <textarea class="form-control bg-light border-0 shadow-none mb-2" rows="3" placeholder="Escribe tu respuesta al estudiante..." v-model="nuevaRespuesta"></textarea>
            <div class="d-flex justify-content-between align-items-center">
              <button class="btn btn-sm btn-outline-danger" @click="cerrarReclamo">Cerrar Reclamo</button>
              <button class="btn btn-primary-custom fw-bold px-4" @click="enviarRespuesta" :disabled="!nuevaRespuesta">
                Responder
              </button>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { apiRequest } from '../../../api.js'

const filtroEstado = ref('Todos')
const reclamos = ref([])
const selectedReclamo = ref(null)
const nuevaRespuesta = ref('')
const error = ref('')
const route = useRoute()
const cursoId = route.params.id

onMounted(async () => {
  await cargarReclamos()
})

async function cargarReclamos() {
  error.value = ''
  try {
    const res = await apiRequest(`/api/reclamos/curso/${cursoId}`)
    reclamos.value = (res || []).map((r) => ({
      id: r.id,
      asunto: `Reclamo #${r.id}`,
      mensaje: r.mensaje,
      estudiante: r.estudiante?.nombre || 'Estudiante',
      tarea: r.nota?.tarea?.nombre || 'Sin tarea',
      estado: normalizarEstado(r.estado),
      fecha: new Date(r.created_at).toLocaleString('es-ES'),
      respuesta: r.respuesta,
    }))
    if (selectedReclamo.value) {
      const updated = reclamos.value.find(r => r.id === selectedReclamo.value.id)
      selectedReclamo.value = updated || null
    }
  } catch (e) {
    error.value = 'No se pudieron cargar los reclamos: ' + e.message
  }
}

const reclamosFiltrados = computed(() => {
  if (filtroEstado.value === 'Todos') return reclamos.value
  return reclamos.value.filter(r => r.estado === filtroEstado.value)
})

function seleccionarReclamo(r) {
  selectedReclamo.value = r
  nuevaRespuesta.value = ''
}

async function enviarRespuesta() {
  if(!nuevaRespuesta.value) return
  
  error.value = ''
  try {
    await apiRequest(`/api/reclamos/${selectedReclamo.value.id}/responder`, 'POST', {
      respuesta: nuevaRespuesta.value
    })
    nuevaRespuesta.value = ''
    await cargarReclamos()
  } catch (e) {
    error.value = 'No se pudo responder el reclamo: ' + e.message
  }
}

async function cerrarReclamo() {
  if (!selectedReclamo.value) return
  error.value = ''
  try {
    await apiRequest(`/api/reclamos/${selectedReclamo.value.id}/cerrar`, 'POST')
    await cargarReclamos()
  } catch (e) {
    error.value = 'No se pudo cerrar el reclamo: ' + e.message
  }
}

function getBadgeClass(estado) {
  switch(estado) {
    case 'Pendiente': return 'bg-warning text-dark'
    case 'Respondido': return 'bg-info text-dark'
    case 'Cerrado': return 'bg-success text-white'
    default: return 'bg-secondary'
  }
}

function normalizarEstado(estado) {
  if (!estado) return 'Pendiente'
  const val = estado.toLowerCase()
  if (val === 'respondido') return 'Respondido'
  if (val === 'cerrado') return 'Cerrado'
  return 'Pendiente'
}
</script>

<style scoped>
.bg-light-custom { background-color: #f8fafc; }
.bg-primary-light { background-color: #eff6ff; }
.cursor-pointer { cursor: pointer; }

.ticket-item:hover {
  background-color: #f1f5f9;
}

.max-w-80 {
  max-width: 80%;
}

.chat-bg {
  background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
  background-size: 20px 20px;
  background-color: #f8fafc;
}

.btn-primary-custom {
  background-color: #1e40af;
  color: white;
  border: none;
  border-radius: 6px;
}
</style>
