<template>
  <span class="badge" :class="badgeClass">{{ label }}</span>
</template>

<script setup>
const props = defineProps({ status: String })

const map = {
  aktif:       { cls: 'badge-blue',   label: 'Aktif' },
  diperpanjang:{ cls: 'badge-purple', label: 'Diperpanjang' },
  terlambat:   { cls: 'badge-red',    label: 'Terlambat' },
  selesai:     { cls: 'badge-green',  label: 'Selesai' },
  hilang:      { cls: 'badge-gray',   label: 'Hilang' },
}

const badgeClass = map[props.status]?.cls ?? 'badge-gray'
const label      = map[props.status]?.label ?? props.status
</script>
