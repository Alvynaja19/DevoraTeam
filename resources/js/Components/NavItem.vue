<template>
  <Link :href="href" class="sidebar-item" :class="{ active }">
    <span class="w-5 h-5 flex-shrink-0"><slot name="icon" /></span>
    <slot />
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
defineProps({ href: String, active: Boolean })
</script>
