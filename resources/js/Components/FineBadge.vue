<template>
  <span class="badge" :class="cls">{{ label }}</span>
</template>
<script setup>
const p = defineProps({ status: String })
const map = { belum_lunas: ['badge-red', 'Belum Lunas'], lunas: ['badge-green', 'Lunas'], dibebaskan: ['badge-gray', 'Dibebaskan'] }
const [cls, label] = map[p.status] ?? ['badge-gray', p.status]
</script>
