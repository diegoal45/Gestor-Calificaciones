<template>
  <div class="export-container">
    <div class="d-flex justify-content-between align-items-center mb-5">
      <div>
        <h4 class="fw-bold mb-1">Exportar Datos</h4>
        <p class="text-muted small m-0">Genera reportes en formato Excel o CSV</p>
      </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
      <div class="card-body p-0 d-flex">
        
        <!-- Sidebar Stepper -->
        <div class="bg-light-custom p-4 border-end" style="width: 250px;">
          <div class="stepper-item mb-4" :class="{ 'active': step === 1, 'completed': step > 1 }">
            <div class="d-flex align-items-center gap-3">
              <div class="step-circle">{{ step > 1 ? '✓' : '1' }}</div>
              <div class="fw-medium">Tipo de Reporte</div>
            </div>
          </div>
          <div class="stepper-item mb-4" :class="{ 'active': step === 2, 'completed': step > 2 }">
            <div class="d-flex align-items-center gap-3">
              <div class="step-circle">{{ step > 2 ? '✓' : '2' }}</div>
              <div class="fw-medium">Configurar Columnas</div>
            </div>
          </div>
          <div class="stepper-item mb-4" :class="{ 'active': step === 3, 'completed': step > 3 }">
            <div class="d-flex align-items-center gap-3">
              <div class="step-circle">{{ step > 3 ? '✓' : '3' }}</div>
              <div class="fw-medium">Filtros</div>
            </div>
          </div>
          <div class="stepper-item" :class="{ 'active': step === 4 }">
            <div class="d-flex align-items-center gap-3">
              <div class="step-circle">4</div>
              <div class="fw-medium">Vista Previa</div>
            </div>
          </div>
        </div>

        <!-- Contenido del Step -->
        <div class="p-5 flex-grow-1 position-relative">
          
          <!-- Step 1 -->
          <div v-if="step === 1" class="step-content animation-fade">
            <h5 class="fw-bold mb-4">¿Qué deseas exportar?</h5>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="d-block cursor-pointer">
                  <input type="radio" v-model="exportConfig.tipo" value="planilla" class="d-none">
                  <div class="card border rounded-3 p-4 transition text-center" :class="exportConfig.tipo === 'planilla' ? 'border-primary bg-primary-light shadow-sm' : ''">
                    <svg class="mb-3 mx-auto text-primary" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="3" y1="15" x2="21" y2="15"></line><line x1="9" y1="9" x2="9" y2="21"></line><line x1="15" y1="9" x2="15" y2="21"></line></svg>
                    <h6 class="fw-bold">Planilla Completa</h6>
                    <p class="small text-muted mb-0">Todas las notas y promedio final.</p>
                  </div>
                </label>
              </div>
              <div class="col-md-6">
                <label class="d-block cursor-pointer">
                  <input type="radio" v-model="exportConfig.tipo" value="asistencia" class="d-none">
                  <div class="card border rounded-3 p-4 transition text-center" :class="exportConfig.tipo === 'asistencia' ? 'border-primary bg-primary-light shadow-sm' : ''">
                    <svg class="mb-3 mx-auto text-primary" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    <h6 class="fw-bold">Reporte de Asistencia</h6>
                    <p class="small text-muted mb-0">Historial y % de asistencia global.</p>
                  </div>
                </label>
              </div>
            </div>
          </div>

          <!-- Step 2 -->
          <div v-if="step === 2" class="step-content animation-fade">
            <h5 class="fw-bold mb-4">Configurar Columnas</h5>
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" v-model="exportConfig.columnas.correo" id="colCorreo">
              <label class="form-check-label fw-medium" for="colCorreo">Incluir Correo Institucional</label>
            </div>
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" v-model="exportConfig.columnas.grupo" id="colGrupo">
              <label class="form-check-label fw-medium" for="colGrupo">Incluir Grupo / Sección</label>
            </div>
            <div class="form-check mb-3" v-if="exportConfig.tipo === 'planilla'">
              <input class="form-check-input" type="checkbox" v-model="exportConfig.columnas.retroalimentacion" id="colRetro">
              <label class="form-check-label fw-medium" for="colRetro">Incluir Retroalimentación de Tareas</label>
            </div>
          </div>

          <!-- Step 3 -->
          <div v-if="step === 3" class="step-content animation-fade">
            <h5 class="fw-bold mb-4">Aplicar Filtros</h5>
            <div class="mb-4">
              <label class="form-label fw-medium text-muted small">Estado de Riesgo</label>
              <select class="form-select custom-input" v-model="exportConfig.filtros.riesgo">
                <option value="Todos">Todos los estudiantes</option>
                <option value="Riesgo">Solo estudiantes en riesgo (< 3.0)</option>
              </select>
            </div>
            <div class="mb-4">
              <label class="form-label fw-medium text-muted small">Grupo / Sección</label>
              <select class="form-select custom-input" v-model="exportConfig.filtros.grupo">
                <option value="Todos">Todos</option>
                <option value="A1">A1</option>
                <option value="A2">A2</option>
              </select>
            </div>
          </div>

          <!-- Step 4 -->
          <div v-if="step === 4" class="step-content animation-fade">
            <h5 class="fw-bold mb-4">Vista Previa & Descarga</h5>
            <div class="table-responsive border rounded mb-4">
              <table class="table table-sm mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Nombre</th>
                    <th v-if="exportConfig.columnas.correo">Correo</th>
                    <th v-if="exportConfig.columnas.grupo">Grupo</th>
                    <th v-if="exportConfig.tipo==='planilla'">Nota Final</th>
                    <th v-if="exportConfig.tipo==='asistencia'">Asist. %</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Ana García</td>
                    <td v-if="exportConfig.columnas.correo">ana.g@univ.edu</td>
                    <td v-if="exportConfig.columnas.grupo">A1</td>
                    <td v-if="exportConfig.tipo==='planilla'">4.5</td>
                    <td v-if="exportConfig.tipo==='asistencia'">95%</td>
                  </tr>
                  <tr>
                    <td colspan="5" class="text-center text-muted small py-2">... (Vista previa de las primeras filas) ...</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="d-flex gap-3 mt-5">
              <button class="btn btn-outline-success flex-grow-1 fw-bold py-3 d-flex justify-content-center align-items-center gap-2" @click="descargar('excel')">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                Descargar Excel (.xlsx)
              </button>
              <button class="btn btn-outline-secondary flex-grow-1 fw-bold py-3 d-flex justify-content-center align-items-center gap-2" @click="descargar('csv')">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                Descargar CSV
              </button>
            </div>
          </div>

          <!-- Navegación Stepper -->
          <div class="d-flex justify-content-between mt-5 pt-4 border-top" v-if="step < 4">
            <button class="btn btn-light fw-medium px-4" :disabled="step === 1" @click="step--">Atrás</button>
            <button class="btn btn-primary-custom fw-bold px-4" @click="step++">Siguiente</button>
          </div>
          <div class="d-flex justify-content-start mt-4" v-else>
            <button class="btn btn-light fw-medium px-4" @click="step--">Volver a Filtros</button>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const step = ref(1)

const exportConfig = ref({
  tipo: 'planilla',
  columnas: {
    correo: true,
    grupo: false,
    retroalimentacion: false
  },
  filtros: {
    riesgo: 'Todos',
    grupo: 'Todos'
  }
})

function descargar(format) {
  alert(`Iniciando descarga de ${format.toUpperCase()}...\nEsto llamaría a /api/exportar?format=${format} con la configuración seleccionada.`)
}
</script>

<style scoped>
.bg-light-custom { background-color: #f8fafc; }

.stepper-item {
  opacity: 0.5;
  transition: opacity 0.3s;
}
.stepper-item.active {
  opacity: 1;
  color: #1e40af;
}
.stepper-item.completed {
  opacity: 1;
}

.step-circle {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background-color: #cbd5e1;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  transition: all 0.3s;
}

.stepper-item.active .step-circle {
  background-color: #1e40af;
  box-shadow: 0 0 0 4px rgba(30, 64, 175, 0.2);
}

.stepper-item.completed .step-circle {
  background-color: #10b981;
}

.btn-primary-custom {
  background-color: #1e40af;
  color: white;
  border: none;
  border-radius: 8px;
}
.bg-primary-light { background-color: #eff6ff !important; }

.custom-input {
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 0.6rem 1rem;
}

.animation-fade {
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
