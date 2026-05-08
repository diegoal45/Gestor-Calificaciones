<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h4 class="fw-bold mb-1">Rúbricas del curso</h4>
        <p class="text-muted small m-0">Ahora se guardan en base de datos y puedes editarlas.</p>
      </div>
      <button type="button" class="btn btn-primary" @click="openCreate">Crear rúbrica</button>
    </div>

    <div v-if="error" class="alert alert-danger">{{ error }}</div>
    <div v-if="loading" class="text-center py-4"><div class="spinner-border text-primary"></div></div>

    <div v-else class="row g-3">
      <div class="col-md-6" v-for="rubrica in rubricas" :key="rubrica.id">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <h5 class="fw-bold mb-1">{{ rubrica.nombre }}</h5>
                <div class="small text-muted">Tarea: {{ rubrica.tarea_nombre }}</div>
                <div class="small text-muted">{{ rubrica.criterios.length }} criterios</div>
              </div>
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-light border" @click="openEdit(rubrica)">Editar</button>
                <button type="button" class="btn btn-sm btn-light border text-danger" @click="removeRubrica(rubrica.id)">Eliminar</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-if="rubricas.length === 0" class="col-12 text-center text-muted py-5">
        No hay rúbricas registradas.
      </div>
    </div>

    <div v-if="showModal" class="modal-overlay d-flex justify-content-center align-items-center">
      <div class="card w-100 modal-card">
        <div class="card-header fw-bold sticky-header">{{ form.id ? 'Editar rúbrica' : 'Nueva rúbrica' }}</div>
        <div class="card-body modal-body-scroll">
          <div class="mb-2" v-if="!form.id">
            <label class="form-label">Tarea</label>
            <select v-model="form.id_tarea" class="form-select">
              <option :value="null" disabled>Selecciona una tarea</option>
              <option v-for="t in tareas" :key="t.id" :value="t.id">{{ t.nombre }}</option>
            </select>
          </div>
          <div class="mb-2">
            <label class="form-label">Rúbrica</label>
            <div class="form-control bg-light">
              Se usará el nombre de la tarea (no necesitas poner nombre).
            </div>
          </div>
          <div class="mb-2">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <label class="form-label mb-0">Criterios</label>
              <div class="small text-muted">
                Disponible: <strong :class="pesoRestante < 0 ? 'text-danger' : 'text-success'">{{ pesoRestante }}%</strong>
              </div>
              <button type="button" class="btn btn-sm btn-light border" @click="addCriterio">Agregar criterio</button>
            </div>
            <div v-for="(c, idx) in form.criterios" :key="c._key" class="border rounded p-2 mb-2">
              <div class="row g-2">
                <div class="col-md-8"><input v-model="c.nombre" class="form-control" placeholder="Nombre criterio" /></div>
                <div class="col-md-4">
                  <input
                    v-model.number="c.peso"
                    type="number"
                    min="0"
                    :max="maxPesoParaCriterio(idx)"
                    class="form-control"
                    placeholder="Peso %"
                  />
                  <div class="small text-muted mt-1">Máx: {{ maxPesoParaCriterio(idx) }}%</div>
                </div>
              </div>
              <div class="mt-2">
                <div class="small text-muted fw-semibold mb-1">Niveles (Mala / Buena / Excelente)</div>
                <div class="row g-2" v-for="(n, nIdx) in (c.niveles || [])" :key="nIdx">
                  <div class="col-md-3">
                    <input v-model="n.nombre" class="form-control form-control-sm" />
                  </div>
                  <div class="col-md-9">
                    <input v-model="n.descripcion" class="form-control form-control-sm" placeholder="Descripción del nivel (qué se evalúa)" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-light" @click="showModal = false">Cancelar</button>
            <button type="button" class="btn btn-primary" :disabled="saving || !puedeGuardar" @click="saveRubrica">Guardar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { apiRequest } from '../../../api.js'

const route = useRoute()
const cursoId = route.params.id

const loading = ref(true)
const saving = ref(false)
const error = ref('')
const tareas = ref([])
const rubricas = ref([])
const showModal = ref(false)
const form = ref({ id: null, id_tarea: null, nombre: '', criterios: [] })
let nextKey = 1

const pesoTotal = computed(() => form.value.criterios.reduce((acc, c) => acc + (Number(c.peso) || 0), 0))
const pesoRestante = computed(() => 100 - pesoTotal.value)

const criteriosConNombre = computed(() => (form.value.criterios || []).filter(c => String(c?.nombre || '').trim() !== ''))
const pesoTotalValidos = computed(() => criteriosConNombre.value.reduce((acc, c) => acc + (Number(c.peso) || 0), 0))

const puedeGuardar = computed(() => {
  // No pedimos nombre de rúbrica; se deriva de la tarea
  // Permitir crear rúbrica solo con nombre (sin criterios todavía)
  if (criteriosConNombre.value.length === 0) return true
  // Si ya hay criterios con nombre, exigir suma 100
  return pesoTotalValidos.value === 100
})

function maxPesoParaCriterio(index) {
  const current = Number(form.value.criterios?.[index]?.peso || 0)
  const otherTotal = pesoTotal.value - current
  const max = 100 - otherTotal
  return max < 0 ? 0 : max
}

onMounted(loadData)

async function loadData() {
  loading.value = true
  error.value = ''
  try {
    const tareasRes = await apiRequest(`/api/tareas/curso/${cursoId}`)
    // Solo tareas tipo rúbrica
    tareas.value = (tareasRes || []).filter(t => (t.tipo || '').toLowerCase() === 'rubrica')

    const all = []
    for (const tarea of tareas.value) {
      const rs = await apiRequest(`/api/rubricas/tarea/${tarea.id}`)
      ;(rs || []).forEach(r => {
        all.push({
          ...r,
          tarea_nombre: tarea.nombre,
          criterios: r.criterios || [],
        })
      })
    }
    rubricas.value = all
  } catch (e) {
    error.value = `No se pudo cargar rúbricas: ${e.message}`
  } finally {
    loading.value = false
  }
}

function openCreate() {
  form.value = { id: null, id_tarea: tareas.value[0]?.id ?? null, nombre: '', criterios: [] }
  showModal.value = true
}

function openEdit(rubrica) {
  form.value = {
    id: rubrica.id,
    id_tarea: rubrica.id_tarea,
    nombre: rubrica.nombre,
    criterios: (rubrica.criterios || []).map(c => ({
      _key: nextKey++,
      id: c.id,
      nombre: c.nombre,
      peso: Number(c.peso),
      niveles: (c.niveles || []).map(n => ({
        id: n.id,
        nombre: n.nombre,
        valor: Number(n.valor),
        descripcion: n.descripcion || '',
      })),
    })),
  }
  showModal.value = true
}

function addCriterio() {
  form.value.criterios.push({
    _key: nextKey++,
    nombre: '',
    peso: 0,
    niveles: [
      { nombre: 'Mala', descripcion: '' },
      { nombre: 'Buena', descripcion: '' },
      { nombre: 'Excelente', descripcion: '' },
    ],
  })
}

async function saveRubrica() {
  saving.value = true
  error.value = ''
  try {
    let rubricaId = form.value.id

    const criteriosValidos = (form.value.criterios || []).filter(c => String(c?.nombre || '').trim() !== '')
    // Solo validar suma 100 si ya hay criterios con nombre
    if (criteriosValidos.length > 0) {
      const totalPeso = criteriosValidos.reduce((acc, c) => acc + (Number(c.peso) || 0), 0)
      if (totalPeso !== 100) {
        throw new Error(`La suma de pesos de los criterios debe ser 100%. Actualmente suma ${totalPeso}%.`)
      }
    }
    if (!rubricaId) {
      const nueva = await apiRequest(`/api/rubricas/tarea/${form.value.id_tarea}`, 'POST', {})
      rubricaId = nueva.id
    } else {
      // Mantener nombre actual o permitir actualizar si ya existiera
      await apiRequest(`/api/rubricas/${rubricaId}`, 'PUT', {})
    }

    for (const criterio of criteriosValidos) {
      if (!criterio.niveles || criterio.niveles.length !== 3) {
        throw new Error('Cada criterio debe tener exactamente 3 niveles (Mala/Buena/Excelente).')
      }
      for (const n of criterio.niveles) {
        if (!String(n.descripcion || '').trim()) {
          throw new Error(`Falta la descripción del nivel "${n.nombre}" en el criterio "${criterio.nombre}".`)
        }
      }

      if (criterio.id) {
        await apiRequest(`/api/criterios/${criterio.id}`, 'PUT', { nombre: criterio.nombre, peso: criterio.peso || 0 })

        // Actualizar niveles existentes
        for (const n of criterio.niveles) {
          if (!n.id) continue
          await apiRequest(`/api/niveles-criterio/${n.id}`, 'PUT', {
            nombre: n.nombre,
            valor: 0,
            descripcion: n.descripcion,
          })
        }
      } else {
        const created = await apiRequest(`/api/criterios/rubrica/${rubricaId}`, 'POST', { nombre: criterio.nombre, peso: criterio.peso || 0 })
        for (const n of criterio.niveles) {
          await apiRequest(`/api/niveles-criterio/criterio/${created.id}`, 'POST', {
            nombre: n.nombre,
            valor: 0,
            descripcion: n.descripcion,
          })
        }
      }
    }
    showModal.value = false
    await loadData()
  } catch (e) {
    error.value = `No se pudo guardar la rúbrica: ${e.message}`
  } finally {
    saving.value = false
  }
}

async function removeRubrica(id) {
  if (!confirm('¿Eliminar esta rúbrica?')) return
  error.value = ''
  try {
    await apiRequest(`/api/rubricas/${id}`, 'DELETE')
    await loadData()
  } catch (e) {
    error.value = `No se pudo eliminar: ${e.message}`
  }
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.45);
  z-index: 1000;
  padding: 1rem;
  overflow: auto;
}

.modal-card {
  max-width: 740px;
  max-height: calc(100vh - 2rem);
  overflow: hidden;
}

.sticky-header {
  position: sticky;
  top: 0;
  z-index: 2;
  background: #fff;
}

.modal-body-scroll {
  overflow: auto;
  max-height: calc(100vh - 2rem - 56px);
}
</style>
