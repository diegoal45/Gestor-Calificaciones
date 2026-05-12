<template>
  <div>
    <div v-if="inline" class="card border-0 shadow-sm rounded-4 overflow-hidden">
      <div class="card-body p-4 p-lg-5">
        <ExportPlanillaFormBody
          :loading="loading"
          :error="error"
          :exporting="exporting"
          :tareas="tareas"
          :usa-asistencia="usaAsistencia"
          :incluir-definitiva="incluirDefinitiva"
          :incluir-email="incluirEmail"
          :incluir-grupo="incluirGrupo"
          :incluir-asistencia-pct="incluirAsistenciaPct"
          :incluir-feedback="incluirFeedback"
          :filtro-riesgo="filtroRiesgo"
          :tareas-checks="tareasChecks"
          :can-export="canExport"
          @solo-definitiva="soloDefinitiva"
          @seleccionar-todas="seleccionarTodasLasTareas"
          @update:incluir-definitiva="incluirDefinitiva = $event"
          @update:incluir-email="incluirEmail = $event"
          @update:incluir-grupo="incluirGrupo = $event"
          @update:incluir-asistencia-pct="incluirAsistenciaPct = $event"
          @update:incluir-feedback="incluirFeedback = $event"
          @update:filtro-riesgo="filtroRiesgo = $event"
          @update:tarea="onToggleTarea"
          @download-csv="downloadCsv"
          @download-xls="downloadXls"
          @export-pdf="exportPdfPrint"
        />
      </div>
    </div>

    <div
      v-else-if="visible"
      class="modal fade show d-block"
      tabindex="-1"
      style="background-color: rgba(15, 23, 42, 0.45)"
      @click.self="close"
    >
      <div class="modal-dialog modal-lg modal-dialog-scrollable" @click.stop>
        <div class="modal-content border-0 shadow rounded-4">
          <div class="modal-header border-0 pb-0">
            <div>
              <h5 class="modal-title fw-bold">Exportar planilla</h5>
              <p class="text-muted small mb-0">
                Siempre se exporta el nombre. Elige la nota definitiva y qué tareas incluir.
              </p>
            </div>
            <button type="button" class="btn-close" aria-label="Cerrar" @click="close"></button>
          </div>
          <div class="modal-body pt-3">
            <ExportPlanillaFormBody
              :loading="loading"
              :error="error"
              :exporting="exporting"
              :tareas="tareas"
              :usa-asistencia="usaAsistencia"
              :incluir-definitiva="incluirDefinitiva"
              :incluir-email="incluirEmail"
              :incluir-grupo="incluirGrupo"
              :incluir-asistencia-pct="incluirAsistenciaPct"
              :incluir-feedback="incluirFeedback"
              :filtro-riesgo="filtroRiesgo"
              :tareas-checks="tareasChecks"
              :can-export="canExport"
              @solo-definitiva="soloDefinitiva"
              @seleccionar-todas="seleccionarTodasLasTareas"
              @update:incluir-definitiva="incluirDefinitiva = $event"
              @update:incluir-email="incluirEmail = $event"
              @update:incluir-grupo="incluirGrupo = $event"
              @update:incluir-asistencia-pct="incluirAsistenciaPct = $event"
              @update:incluir-feedback="incluirFeedback = $event"
              @update:filtro-riesgo="filtroRiesgo = $event"
              @update:tarea="onToggleTarea"
              @download-csv="downloadCsv"
              @download-xls="downloadXls"
              @export-pdf="exportPdfPrint"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue'
import ExportPlanillaFormBody from './ExportPlanillaFormBody.vue'
import { apiRequest, apiPostJson, apiDownloadPost } from '../api.js'

const props = defineProps({
  cursoId: { type: [String, Number], required: true },
  inline: { type: Boolean, default: false },
})

const visible = ref(false)
const loading = ref(false)
const error = ref('')
const exporting = ref('')
const tareas = ref([])
const usaAsistencia = ref(false)

const incluirDefinitiva = ref(true)
const incluirEmail = ref(false)
const incluirGrupo = ref(false)
const incluirAsistenciaPct = ref(false)
const incluirFeedback = ref(false)
const filtroRiesgo = ref('todos')
const tareasChecks = reactive({})

const selectedTareaIds = computed(() =>
  tareas.value.filter((t) => tareasChecks[t.id]).map((t) => t.id)
)

const canExport = computed(() => incluirDefinitiva.value || selectedTareaIds.value.length > 0)

function syncChecks() {
  Object.keys(tareasChecks).forEach((k) => delete tareasChecks[k])
  tareas.value.forEach((t) => {
    tareasChecks[t.id] = false
  })
}

function onToggleTarea({ id, checked }) {
  tareasChecks[id] = checked
}

async function loadPlanilla() {
  loading.value = true
  error.value = ''
  try {
    const res = await apiRequest(`/api/cursos/${props.cursoId}/planilla`)
    tareas.value = res.tareas || []
    usaAsistencia.value = !!(res.curso && res.curso.usa_asistencia)
    if (!usaAsistencia.value) incluirAsistenciaPct.value = false
    syncChecks()
  } catch (e) {
    error.value = e.message || 'No se pudo cargar la planilla'
  } finally {
    loading.value = false
  }
}

function buildBody(formato) {
  return {
    formato,
    incluir_email: incluirEmail.value,
    incluir_grupo: incluirGrupo.value,
    incluir_definitiva: incluirDefinitiva.value,
    incluir_asistencia_pct: usaAsistencia.value && incluirAsistenciaPct.value,
    incluir_feedback: incluirFeedback.value,
    tarea_ids: selectedTareaIds.value,
    filtro_riesgo: filtroRiesgo.value,
  }
}

async function downloadCsv() {
  if (!canExport.value) return
  exporting.value = 'csv'
  try {
    await apiDownloadPost(`/api/cursos/${props.cursoId}/exportar-planilla`, buildBody('csv'), 'planilla.csv')
  } catch (e) {
    alert(e.message)
  } finally {
    exporting.value = ''
  }
}

async function downloadXls() {
  if (!canExport.value) return
  exporting.value = 'xls'
  try {
    await apiDownloadPost(`/api/cursos/${props.cursoId}/exportar-planilla`, buildBody('xls'), 'planilla.xls')
  } catch (e) {
    alert(e.message)
  } finally {
    exporting.value = ''
  }
}

function escapeHtml(s) {
  return String(s)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
}

async function exportPdfPrint() {
  if (!canExport.value) return
  exporting.value = 'pdf'
  try {
    const payload = await apiPostJson(`/api/cursos/${props.cursoId}/exportar-planilla`, buildBody('json'))
    const title = escapeHtml(payload.curso_nombre || 'Planilla')
    const thead =
      '<tr>' + payload.headers.map((h) => `<th>${escapeHtml(h)}</th>`).join('') + '</tr>'
    const tbody = payload.rows
      .map((row) => '<tr>' + row.map((cell) => `<td>${escapeHtml(cell)}</td>`).join('') + '</tr>')
      .join('')
    const html = `<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"><title>${title}</title>
<style>
body{font-family:system-ui,sans-serif;margin:24px;color:#0f172a}
h1{font-size:1.1rem;margin:0 0 16px;font-weight:700}
table{border-collapse:collapse;width:100%;font-size:12px}
th,td{border:1px solid #334155;padding:8px 10px;text-align:left}
th{background:#f1f5f9;font-weight:600}
@media print{body{margin:12px}}
</style></head><body onload="window.print()">
<h1>${title}</h1>
<table><thead>${thead}</thead><tbody>${tbody}</tbody></table>
</body></html>`
    const w = window.open('', '_blank')
    if (!w) {
      alert('Permite ventanas emergentes para imprimir o guardar como PDF.')
      return
    }
    w.document.open()
    w.document.write(html)
    w.document.close()
  } catch (e) {
    alert(e.message)
  } finally {
    exporting.value = ''
  }
}

function soloDefinitiva() {
  incluirDefinitiva.value = true
  tareas.value.forEach((t) => {
    tareasChecks[t.id] = false
  })
}

function seleccionarTodasLasTareas() {
  tareas.value.forEach((t) => {
    tareasChecks[t.id] = true
  })
}

function open() {
  visible.value = true
  loadPlanilla()
}

function close() {
  visible.value = false
}

watch(
  () => props.cursoId,
  () => {
    if (props.inline) loadPlanilla()
  }
)

onMounted(() => {
  if (props.inline) loadPlanilla()
})

defineExpose({ open, close })
</script>
