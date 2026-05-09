<template>
  <div class="export-container">
    <div class="mb-4">
      <h4 class="fw-bold mb-1">Exportar calificaciones</h4>
      <p class="text-muted small m-0">
        Misma herramienta que en Planilla y Resumen: nombre obligatorio; elige definitiva, tareas concretas, columnas extra y formato (CSV, Excel o PDF por impresión).
      </p>
    </div>

    <ExportPlanillaModal :curso-id="cursoId" inline />
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router'
import ExportPlanillaModal from '../../../components/ExportPlanillaModal.vue'

const route = useRoute()
const cursoId = route.params.id
</script>

<style scoped>
.export-container {
  max-width: 960px;
  margin: 0 auto;
}
</style>
