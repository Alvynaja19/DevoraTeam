<template>
  <span class="badge" :class="badgeClass">{{ label }}</span>
</template>

<script setup>
const props = defineProps({ status: String })
const map = {
  pending:   { cls: 'badge-yellow', label: 'Pending' },
  aktif:     { cls: 'badge-green',  label: 'Aktif' },
  nonaktif:  { cls: 'badge-gray',   label: 'Nonaktif' },
  suspended: { cls: 'badge-red',    label: 'Suspended' },
  ditolak:   { cls: 'badge-red',    label: 'Ditolak' },
}
const badgeClass = map[props.status]?.cls ?? 'badge-gray'
const label      = map[props.status]?.label ?? props.status
</script>
