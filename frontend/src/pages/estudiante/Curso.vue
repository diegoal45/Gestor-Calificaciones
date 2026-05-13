<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-5 mt-2">
      <div>
        <h3 class="fw-bold mb-1 text-dark">{{ cursoNombre }}</h3>
        <p class="text-muted m-0">Vista del estudiante</p>
      </div>
      <button class="btn btn-light border fw-medium action-btn" @click="volver">Volver</button>
    </div>

    <ul class="nav nav-tabs custom-tabs border-0 gap-2 mb-4">
      <li class="nav-item" v-for="tab in tabs" :key="tab">
        <button class="nav-link py-2 px-3 fw-medium" :class="{ active: activeTab === tab }" @click="activeTab = tab">{{ tab }}</button>
      </li>
    </ul>

    <div v-if="error" class="alert alert-danger">{{ error }}</div>
    <div v-if="loading" class="text-center py-5"><div class="spinner-border text-primary"></div></div>

    <div v-else>
      <div v-if="activeTab === 'Notas'">
        <div class="table-responsive bg-white rounded-3 border shadow-sm">
          <table class="table mb-0">
            <thead class="table-light">
              <tr>
                <th>Tarea</th>
                <th class="text-center">Nota</th>
                <th class="text-center">Peso</th>
                <th class="text-center">Estado</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="t in tareas" :key="t.id">
                <td>{{ t.nombre }}</td>
                <td class="text-center fw-bold">{{ t.nota ?? '-' }}</td>
                <td class="text-center">{{ t.porcentaje }}%</td>
                <td class="text-center"><span class="badge" :class="getEstadoClass(t.nota)">{{ getEstado(t.nota) }}</span></td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="row g-3 mt-2">
          <div class="col-md-4"><div class="card p-3 border-0 shadow-sm"><small class="text-muted">Promedio</small><strong>{{ estudiante.promedio }}</strong></div></div>
          <div class="col-md-4"><div class="card p-3 border-0 shadow-sm"><small class="text-muted">Nota minima</small><strong>{{ cursoNotaMinima }}</strong></div></div>
          <div class="col-md-4"><div class="card p-3 border-0 shadow-sm"><small class="text-muted">Progreso</small><strong>{{ progresoNotas }}%</strong></div></div>
        </div>
      </div>

      <div v-else-if="activeTab === 'Feedback'" class="bg-white rounded-4 border-0 shadow-sm p-3">
        <div v-if="feedbackItems.length === 0" class="text-muted">No hay feedback registrado.</div>
        <div v-for="item in feedbackItems" :key="item.id" class="border rounded p-3 mb-2">
          <div class="fw-bold">{{ item.nombre }}</div>
          <div class="small text-muted">{{ item.feedback }}</div>

          <div
            v-if="(item.tipo || '').toLowerCase() === 'rubrica' && (item.rubrica_evaluacion || []).length"
            class="mt-3"
          >
            <div class="small text-muted fw-semibold mb-2">Detalle rúbrica</div>
            <div
              v-for="(ev, idx) in item.rubrica_evaluacion"
              :key="idx"
              class="border rounded p-2 mb-2 bg-light"
            >
              <div class="d-flex justify-content-between align-items-start gap-2">
                <div class="fw-semibold">
                  {{ ev.criterio?.nombre || 'Criterio' }}
                  <span class="text-muted fw-normal">({{ ev.criterio?.peso ?? 0 }}%)</span>
                </div>
                <span class="badge bg-primary">
                  {{ ev.nivel?.nombre || 'Nivel' }} · {{ Number(ev.nivel?.valor ?? 0) }}% del criterio
                </span>
              </div>
              <div class="small text-muted mt-2">
                {{ ev.nivel?.descripcion || '---' }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-else-if="activeTab === 'Simulador'" class="bg-white rounded-4 border-0 shadow-sm p-4">
        <div v-if="!simulador || simulador.length === 0">
          <div class="alert alert-warning">No hay tareas disponibles para simular.</div>
        </div>
        
        <div v-else>
          <div class="row mb-4">
            <div class="col-md-6">
              <label class="form-label fw-bold">Nota definitiva deseada</label>
              <div class="input-group">
                <input type="number" min="0" :max="cursoNotaMaxima" step="0.1" class="form-control custom-input" v-model.number="notaObjetivo">
                <button class="btn btn-primary-custom" @click="calcularNotasMinimas">Calcular mínimo necesario</button>
              </div>
              <small class="text-muted">Define la nota final que quieres alcanzar (máximo: {{ cursoNotaMaxima }})</small>
            </div>
            <div class="col-md-6">
              <div class="card p-3 border-0 bg-light h-100">
                <small class="text-muted">Promedio actual</small>
                <strong class="display-6">{{ promedioActual.toFixed(1) }}</strong>
                <small class="text-muted">Promedio simulado</small>
                <strong class="display-6" :class="getNotaClass(promedioSimulado)">{{ promedioSimulado.toFixed(1) }}</strong>
              </div>
            </div>
          </div>

          <div class="table-responsive mb-4">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th>Tarea</th>
                  <th class="text-center">Peso</th>
                  <th class="text-center">Nota Actual</th>
                  <th class="text-center">Nota Simulada</th>
                  <th class="text-center">Estado</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="t in simulador" :key="t.id" :class="{ 'table-warning': t.nota_actual === null }">
                  <td class="fw-medium">{{ t.nombre }}</td>
                  <td class="text-center">{{ t.porcentaje }}%</td>
                  <td class="text-center">
                    <span v-if="t.nota_actual !== null" class="fw-bold">{{ t.nota_actual.toFixed(1) }}</span>
                    <span v-else class="text-muted">-</span>
                  </td>
                  <td class="text-center">
                    <div class="input-group input-group-sm">
                      <input 
                        type="number" 
                        min="0" 
                        :max="cursoNotaMaxima" 
                        step="0.1" 
                        class="form-control custom-input text-center" 
                        v-model.number="t.simulada"
                        @input="actualizarPromedio"
                      >
                      <div class="input-group-text">/{{ cursoNotaMaxima.toFixed(1) }}</div>
                    </div>
                  </td>
                  <td class="text-center">
                    <span v-if="t.nota_actual !== null" class="badge bg-success">Calificada</span>
                    <span v-else class="badge bg-warning">Por calificar</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div v-if="mensajeCalculo" class="alert mt-3" :class="promedioSimulado >= notaObjetivo ? 'alert-success' : 'alert-warning'">
            {{ mensajeCalculo }}
          </div>
        </div>
      </div>

      <div v-else-if="activeTab === 'Progreso'" class="bg-white rounded-4 border-0 shadow-sm p-4">
        <div class="row g-3">
          <div class="col-md-3"><div class="card p-3 border-0 bg-light"><small class="text-muted">Promedio actual</small><strong>{{ estudiante.promedio }}</strong></div></div>
          <div class="col-md-3"><div class="card p-3 border-0 bg-light"><small class="text-muted">Estado</small><strong>{{ estudiante.estado }}</strong></div></div>
          <div class="col-md-3"><div class="card p-3 border-0 bg-light"><small class="text-muted">Riesgo</small><strong>{{ estudiante.riesgo }}</strong></div></div>
          <div class="col-md-3"><div class="card p-3 border-0 bg-light"><small class="text-muted">Comparacion</small><strong>{{ comparacionCurso }}</strong></div></div>
        </div>
        <div class="row g-3 mt-1">
          <div class="col-md-3"><div class="card p-3 border-0 shadow-sm"><small class="text-muted">Percentil</small><strong>P{{ analisis.percentil }}</strong></div></div>
          <div class="col-md-3"><div class="card p-3 border-0 shadow-sm"><small class="text-muted">Posicion</small><strong>{{ analisis.posicion ? `${analisis.posicion}/${analisis.total_estudiantes}` : 'N/A' }}</strong></div></div>
          <div class="col-md-3"><div class="card p-3 border-0 shadow-sm"><small class="text-muted">Promedio curso</small><strong>{{ analisis.promedio_curso || 0 }}</strong></div></div>
          <div class="col-md-3"><div class="card p-3 border-0 shadow-sm"><small class="text-muted">Mediana curso</small><strong>{{ analisis.mediana_curso || 0 }}</strong></div></div>
        </div>
        <div class="alert alert-info mt-3 mb-0">
          {{ analisis.mensaje || 'Aun no hay suficientes notas para analisis comparativo.' }}
        </div>
      </div>

      <div v-else-if="activeTab === 'Asistencia'" class="bg-white rounded-4 border-0 shadow-sm p-4">
        <div class="row g-3 mb-3">
          <div class="col-md-3"><div class="card p-3 border-0 bg-light"><small class="text-muted">Asistencia global</small><strong>{{ asistenciaPct }}%</strong></div></div>
          <div class="col-md-3"><div class="card p-3 border-0 bg-light"><small class="text-muted">Registros</small><strong>{{ asistencias.length }}</strong></div></div>
          <div class="col-md-3"><div class="card p-3 border-0 bg-light"><small class="text-muted">Cuenta en nota</small><strong>{{ cursoConfig.usa_asistencia ? 'Si' : 'No' }}</strong></div></div>
          <div class="col-md-3"><div class="card p-3 border-0 bg-light"><small class="text-muted">Peso</small><strong>{{ cursoConfig.usa_asistencia ? `${cursoConfig.peso_asistencia || 0}%` : '0%' }}</strong></div></div>
        </div>

        <div v-if="cursoConfig.usa_asistencia" class="alert alert-primary">
          Nota final estimada incluyendo asistencia: <strong>{{ notaConAsistencia }}</strong>
        </div>

        <div class="table-responsive border rounded-3">
          <table class="table mb-0">
            <thead class="table-light">
              <tr>
                <th>Fecha</th>
                <th class="text-center">Estado</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="a in asistencias" :key="a.id">
                <td>{{ a.fecha }}</td>
                <td class="text-center">
                  <span class="badge" :class="a.estado === 'presente' ? 'bg-success' : 'bg-danger'">
                    {{ a.estado === 'presente' ? 'Presente' : 'Ausente' }}
                  </span>
                </td>
              </tr>
              <tr v-if="asistencias.length === 0">
                <td colspan="2" class="text-center text-muted">Aun no hay registros de asistencia.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div v-else-if="activeTab === 'Reclamos'" class="bg-white rounded-4 border-0 shadow-sm p-4">
        <div class="row g-3 mb-3">
          <div class="col-md-4">
            <label class="form-label">Nota</label>
            <select class="form-select custom-input" v-model="nuevoReclamo.nota_id">
              <option :value="null">Selecciona una nota</option>
              <option v-for="t in tareasConNota" :key="t.nota_id" :value="t.nota_id">
                {{ t.nombre }} ({{ t.nota }})
              </option>
            </select>
          </div>
          <div class="col-md-8">
            <label class="form-label">Mensaje</label>
            <input class="form-control custom-input" v-model="nuevoReclamo.mensaje" placeholder="Explica tu reclamo..." />
          </div>
        </div>
        <div class="d-flex justify-content-end mb-4">
          <button class="btn btn-primary-custom fw-bold" @click="crearReclamo" :disabled="!nuevoReclamo.nota_id || !nuevoReclamo.mensaje">Crear reclamo</button>
        </div>

        <div v-if="reclamos.length === 0" class="text-muted">No tienes reclamos en este curso.</div>
        <div v-for="r in reclamosCurso" :key="r.id" class="border rounded p-3 mb-2">
          <div class="d-flex justify-content-between align-items-center">
            <strong>{{ r.tarea }}</strong>
            <span class="badge" :class="badgeEstadoReclamo(r.estado)">{{ r.estado }}</span>
          </div>
          <div class="small text-muted mt-1">{{ r.mensaje }}</div>
          <div v-if="r.respuesta" class="small mt-2"><strong>Respuesta:</strong> {{ r.respuesta }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { apiRequest } from '../../api.js'

const route = useRoute()
const router = useRouter()
const cursoId = route.params.id

const tabs = ['Notas', 'Feedback', 'Simulador', 'Progreso', 'Asistencia', 'Reclamos']
const activeTab = ref('Notas')
const loading = ref(true)
const error = ref('')
const tareas = ref([])
const estudiante = ref({ promedio: '-', estado: '-', riesgo: '-' })
const cursoNombre = ref('Curso')
const cursoPromedio = ref(0)
const cursoNotaMinima = ref(3.0)
const cursoNotaMaxima = ref(5.0)
const simulador = ref([])
const reclamos = ref([])
const nuevoReclamo = ref({ nota_id: null, mensaje: '' })
const analisis = ref({
  promedio_estudiante: 0,
  promedio_curso: 0,
  mediana_curso: 0,
  percentil: 0,
  posicion: null,
  total_estudiantes: 0,
  mensaje: '',
})
const asistencias = ref([])
const cursoConfig = ref({ usa_asistencia: false, peso_asistencia: 0 })
const notaObjetivo = ref(3.0)
const promedioActual = ref(0.0)
const promedioSimulado = ref(0.0)
const mensajeCalculo = ref('')
const loadingSimulador = ref(false)

const feedbackItems = computed(() => tareas.value.filter(t => t.feedback))
const tareasConNota = computed(() => tareas.value.filter(t => t.nota_id))
const reclamosCurso = computed(() => {
  const notasCurso = new Set(tareasConNota.value.map(t => t.nota_id))
  return reclamos.value
    .filter(r => notasCurso.has(r.id_nota))
    .map((r) => ({
      id: r.id,
      estado: normalizarEstado(r.estado),
      mensaje: r.mensaje,
      respuesta: r.respuesta,
      tarea: r.nota?.tarea?.nombre || 'Sin tarea',
    }))
})
const progresoNotas = computed(() => {
  if (!tareas.value.length) return 0
  const conNota = tareas.value.filter(t => t.nota !== null && t.nota !== undefined).length
  return Math.round((conNota / tareas.value.length) * 100)
})
const comparacionCurso = computed(() => {
  const est = parseFloat(estudiante.value.promedio)
  if (isNaN(est) || !cursoPromedio.value) return 'Sin datos'
  return est >= cursoPromedio.value ? 'Sobre promedio' : 'Bajo promedio'
})
const asistenciaPct = computed(() => {
  if (!asistencias.value.length) return 0
  const presentes = asistencias.value.filter(a => a.estado === 'presente').length
  return Math.round((presentes / asistencias.value.length) * 100)
})
const notaConAsistencia = computed(() => {
  const prom = parseFloat(estudiante.value.promedio)
  if (isNaN(prom)) return '-'
  if (!cursoConfig.value.usa_asistencia || !cursoConfig.value.peso_asistencia) return prom.toFixed(1)
  const pesoAsis = Number(cursoConfig.value.peso_asistencia)
  const pesoNotas = 100 - pesoAsis
  const aporteAsis = (asistenciaPct.value / 100) * cursoNotaMaxima.value
  const final = (prom * (pesoNotas / 100)) + (aporteAsis * (pesoAsis / 100))
  return final.toFixed(1)
})

function actualizarPromedioActual() {
  let suma = 0
  let peso = 0
  simulador.value.forEach(t => {
    if (t.nota_actual !== null) {
      suma += t.nota_actual * (t.porcentaje / 100)
      peso += t.porcentaje
    }
  })
  promedioActual.value = peso > 0 ? suma / (peso / 100) : 0
}

function actualizarPromedio() {
  try {
    let suma = 0
    let peso = 0
    
    if (!Array.isArray(simulador.value)) {
      console.error('Simulador no es un array:', simulador.value)
      promedioSimulado.value = 0
      return
    }
    
    simulador.value.forEach((t, index) => {
      if (!t || typeof t !== 'object') {
        console.error(`Tarea inválida en índice ${index}:`, t)
        return
      }
      
      const simulada = parseFloat(t.simulada) || 0
      const porcentaje = parseFloat(t.porcentaje) || 0
      
      if (isNaN(simulada) || isNaN(porcentaje)) {
        console.error(`Valores inválidos en tarea ${t.id}:`, { simulada: t.simulada, porcentaje: t.porcentaje })
        return
      }
      
      suma += simulada * (porcentaje / 100)
      peso += porcentaje
    })
    
    promedioSimulado.value = peso > 0 ? suma / (peso / 100) : 0
    
    if (isNaN(promedioSimulado.value) || !isFinite(promedioSimulado.value)) {
      console.error('Promedio simulado resultó en NaN/Infinito:', { suma, peso, promedio: promedioSimulado.value })
      promedioSimulado.value = 0
    }
  } catch (error) {
    console.error('Error en actualizarPromedio:', error)
    promedioSimulado.value = 0
  }
}

function calcularNotasMinimas() {
  try {
    if (!simulador.value || simulador.value.length === 0) {
      mensajeCalculo.value = 'No hay tareas disponibles para simular.'
      return
    }
    
    const objetivo = notaObjetivo.value
    const tareasPendientes = simulador.value.filter(t => t.nota_actual === null)
    
    if (tareasPendientes.length === 0) {
      mensajeCalculo.value = 'No tienes tareas pendientes por calificar.'
      return
    }
    
    // Calcular suma y peso de tareas ya calificadas
    let sumaCalificadas = 0
    let pesoCalificadas = 0
    simulador.value.forEach(t => {
      if (t.nota_actual !== null) {
        sumaCalificadas += t.nota_actual * (t.porcentaje / 100)
        pesoCalificadas += t.porcentaje
      }
    })
    
    const pesoPendiente = 100 - pesoCalificadas
    
    if (pesoPendiente === 0) {
      mensajeCalculo.value = 'Todas las tareas ya están calificadas.'
      return
    }
    
    // Calcular nota mínima necesaria en las tareas pendientes
    const notaMinimaNecesaria = (objetivo - sumaCalificadas) / (pesoPendiente / 100)
    
    // Asignar la nota mínima calculada a las tareas pendientes
    simulador.value.forEach(t => {
      if (t.nota_actual === null) {
        t.simulada = Math.max(0, Math.min(cursoNotaMaxima.value, notaMinimaNecesaria))
      }
    })
    
    actualizarPromedio()
    
    if (notaMinimaNecesaria > cursoNotaMaxima.value) {
      mensajeCalculo.value = `⚠️ Es imposible alcanzar ${objetivo} incluso con notas perfectas en las tareas restantes. La máxima nota posible es ${promedioSimulado.value.toFixed(1)}.`
    } else if (notaMinimaNecesaria < 0) {
      mensajeCalculo.value = `✅ Ya garantizas al menos ${objetivo} con tus notas actuales. La mínima necesaria en tareas pendientes es 0.0.`
    } else {
      mensajeCalculo.value = `📊 Para alcanzar ${objetivo} necesitas mínimo ${notaMinimaNecesaria.toFixed(2)} en cada tarea pendiente (sin considerar ponderación individual).`
    }
  } catch (error) {
    console.error('Error en calcularNotasMinimas:', error)
    mensajeCalculo.value = 'Ocurrió un error al calcular las notas mínimas. Por favor intenta de nuevo.'
  }
}

// Watcher para asegurar que el simulador tenga datos cuando se active
watch(
  () => activeTab.value,
  async (newTab) => {
    if (newTab === 'Simulador') {
      // Si el simulador no tiene datos, intentar recargarlos
      if (!simulador.value || simulador.value.length === 0) {
        loadingSimulador.value = true
        try {
          console.log('Recargando simulador desde watcher...')
          const user = JSON.parse(localStorage.getItem('user'))
          if (user?.id) {
            const perfil = await apiRequest(`/api/estudiantes/${user.id}/perfil?curso_id=${cursoId}`)
            console.log('Datos recibidos en watcher:', perfil)
            
            if (perfil.tareas && Array.isArray(perfil.tareas)) {
              simulador.value = perfil.tareas.map((t, index) => {
                try {
                  const id = t.id
                  const nombre = t.nombre || 'Tarea sin nombre'
                  const porcentaje = parseFloat(t.porcentaje) || 0
                  
                  let nota_actual = null
                  let simulada = cursoNotaMaxima.value / 2
                  
                  if (t.nota !== null && t.nota !== undefined && t.nota !== '') {
                    const notaParseada = parseFloat(String(t.nota).replace(',', '.'))
                    if (!isNaN(notaParseada)) {
                      nota_actual = notaParseada
                      simulada = notaParseada
                    }
                  }
                  
                  return { id, nombre, porcentaje, nota_actual, simulada }
                } catch (error) {
                  console.error(`Error procesando tarea ${index} en watcher:`, error)
                  return {
                    id: t.id || index,
                    nombre: 'Error en tarea',
                    porcentaje: 0,
                    nota_actual: null,
                    simulada: cursoNotaMaxima.value / 2
                  }
                }
              })
              
              console.log('Simulador recargado:', simulador.value)
              actualizarPromedioActual()
              actualizarPromedio()
            }
          }
        } catch (error) {
          console.error('Error al recargar simulador:', error)
        } finally {
          loadingSimulador.value = false
        }
      }
    }
  }
)

onMounted(async () => {
  const qtab = String(route.query?.tab || '').toLowerCase()
  if (qtab === 'reclamos') activeTab.value = 'Reclamos'

  try {
    const user = JSON.parse(localStorage.getItem('user'))
    if (!user?.id) throw new Error('Usuario no encontrado en sesion')

    const perfil = await apiRequest(`/api/estudiantes/${user.id}/perfil?curso_id=${cursoId}`)
    
    tareas.value = perfil.tareas || []
    estudiante.value = perfil.estudiante || estudiante.value
    
    // Inicializar simulador con validación
    if (perfil.tareas && Array.isArray(perfil.tareas)) {
      console.log('Datos recibidos del API:', perfil.tareas)
      
      simulador.value = perfil.tareas.map((t, index) => {
        try {
          const id = t.id
          const nombre = t.nombre || 'Tarea sin nombre'
          const porcentaje = parseFloat(t.porcentaje) || 0
          
          // Manejar notas que vienen como string del backend
          let nota_actual = null
          let simulada = cursoNotaMaxima.value / 2
          
          if (t.nota !== null && t.nota !== undefined && t.nota !== '') {
            const notaParseada = parseFloat(String(t.nota).replace(',', '.'))
            if (!isNaN(notaParseada)) {
              nota_actual = notaParseada
              simulada = notaParseada
            }
          }
          
          console.log(`Tarea ${index} procesada:`, {
            id, nombre, porcentaje, nota_original: t.nota, nota_actual, simulada
          })
          
          return {
            id,
            nombre,
            porcentaje,
            nota_actual,
            simulada
          }
        } catch (error) {
          console.error(`Error procesando tarea ${index}:`, error, t)
          return {
            id: t.id || index,
            nombre: 'Error en tarea',
            porcentaje: 0,
            nota_actual: null,
            simulada: cursoNotaMaxima.value / 2
          }
        }
      })
      
      console.log('Simulador inicializado:', simulador.value)
      
      // Calcular promedio actual y simulado inicial
      actualizarPromedioActual()
      actualizarPromedio()
    } else {
      console.warn('No hay tareas válidas en el perfil:', perfil.tareas)
      simulador.value = []
    }

    const cursoRes = await apiRequest(`/api/cursos/${cursoId}`)
    const curso = cursoRes?.curso || cursoRes
    cursoNombre.value = curso?.nombre || 'Curso'
    cursoPromedio.value = parseFloat(curso?.promedio_general || 0)
    cursoNotaMinima.value = parseFloat(curso?.nota_minima_aprobatoria || 3.0)
    cursoNotaMaxima.value = parseFloat(curso?.nota_maxima || 5.0)
    cursoConfig.value = {
      usa_asistencia: !!curso?.usa_asistencia,
      peso_asistencia: Number(curso?.peso_asistencia || 0),
    }
    
    // Establecer el objetivo inicial a la nota mínima aprobatoria del curso
    if (notaObjetivo.value === 3.0) {
      notaObjetivo.value = cursoNotaMinima.value
    }

    reclamos.value = await apiRequest(`/api/reclamos/estudiante/${user.id}`)
    asistencias.value = await apiRequest(`/api/asistencias/curso/${cursoId}/estudiante/${user.id}`)
    analisis.value = await apiRequest(`/api/estudiantes/${user.id}/analisis?curso_id=${cursoId}`)
  } catch (e) {
    error.value = 'No se pudo cargar el curso del estudiante: ' + e.message
  } finally {
    loading.value = false
  }
})

function volver() {
  router.push('/estudiante/dashboard')
}
function getEstado(nota) {
  const v = parseFloat(nota)
  if (isNaN(v)) return 'Pendiente'
  if (v >= 4.0) return 'Aprobado'
  if (v >= 3.0) return 'En riesgo'
  return 'Reprobado'
}
function getEstadoClass(nota) {
  const v = parseFloat(nota)
  if (isNaN(v)) return 'bg-light text-muted border'
  if (v >= 4.0) return 'bg-success text-white'
  if (v >= 3.0) return 'bg-warning text-dark'
  return 'bg-danger text-white'
}

function getNotaClass(nota) {
  const v = parseFloat(nota)
  if (isNaN(v)) return 'text-muted'
  if (v >= 4.0) return 'text-success'
  if (v >= 3.0) return 'text-warning'
  return 'text-danger'
}

async function crearReclamo() {
  try {
    const user = JSON.parse(localStorage.getItem('user'))
    await apiRequest(`/api/reclamos/nota/${nuevoReclamo.value.nota_id}`, 'POST', {
      id_estudiante: user.id,
      mensaje: nuevoReclamo.value.mensaje,
    })
    reclamos.value = await apiRequest(`/api/reclamos/estudiante/${user.id}`)
    nuevoReclamo.value = { nota_id: null, mensaje: '' }
    activeTab.value = 'Reclamos'
  } catch (e) {
    error.value = 'No se pudo crear el reclamo: ' + e.message
  }
}

function badgeEstadoReclamo(estado) {
  if (estado === 'Respondido') return 'bg-info text-dark'
  if (estado === 'Cerrado') return 'bg-success text-white'
  return 'bg-warning text-dark'
}

function normalizarEstado(estado) {
  const v = (estado || '').toLowerCase()
  if (v === 'respondido') return 'Respondido'
  if (v === 'cerrado') return 'Cerrado'
  return 'Pendiente'
}
</script>

<style scoped>
.custom-tabs .nav-link {
  border: none;
  border-bottom: 3px solid transparent;
  color: #64748b;
  background: transparent;
  transition: all 0.2s;
}
.custom-tabs .nav-link:hover {
  border-color: #cbd5e1;
  color: #334155;
}
.custom-tabs .nav-link.active {
  color: #1e40af;
  border-color: #1e40af;
  font-weight: 600;
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
</style>
