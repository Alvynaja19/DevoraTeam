<template>
  <AdminLayout title="Pengaturan Sistem">
    <div class="max-w-3xl space-y-6">
      <div v-for="(items, groupName) in settings" :key="groupName" class="card">
        <div class="card-header">
          <div class="font-semibold text-slate-800 capitalize">{{ groupLabel(groupName) }}</div>
        </div>
        <div class="card-body space-y-4">
          <div v-for="setting in items" :key="setting.key" class="flex items-center justify-between gap-4 py-2 border-b border-slate-50 last:border-0">
            <div>
              <div class="text-sm font-medium text-slate-700">{{ setting.description || setting.key }}</div>
              <div class="text-xs text-slate-400 mt-0.5 font-mono">{{ setting.key }}</div>
            </div>
            <div class="w-44 flex-shrink-0">
              <input
                v-if="setting.type !== 'boolean'"
                v-model="form[setting.key]"
                :type="setting.type === 'integer' ? 'number' : 'text'"
                class="form-input text-right"
              />
              <label v-else class="flex items-center justify-end gap-2 cursor-pointer">
                <input v-model="form[setting.key]" type="checkbox" class="w-4 h-4 rounded" />
                <span class="text-sm text-slate-600">{{ form[setting.key] ? 'Aktif' : 'Nonaktif' }}</span>
              </label>
            </div>
          </div>
        </div>
      </div>

      <div class="flex justify-end">
        <button @click="save" :disabled="saving" class="btn btn-primary px-8">
          <span v-if="saving" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
          {{ saving ? 'Menyimpan...' : '💾 Simpan Pengaturan' }}
        </button>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/TailAdminLayout.vue'

const props = defineProps({ settings: Object })
const saving = ref(false)

// Build reactive form dari semua setting
const form = reactive(
  Object.values(props.settings).flat()
    .reduce((acc, s) => {
      acc[s.key] = s.type === 'boolean' ? s.value === 'true' : s.type === 'integer' ? Number(s.value) : s.value
      return acc
    }, {})
)

function save() {
  saving.value = true
  const payload = Object.entries(form).map(([key, value]) => ({
    key,
    value: typeof value === 'boolean' ? (value ? 'true' : 'false') : String(value),
  }))
  router.post(route('settings.update'), { settings: payload }, {
    onFinish: () => { saving.value = false }
  })
}

const groupLabels = { denda: '💰 Denda', peminjaman: '📚 Peminjaman', reservasi: '🔖 Reservasi', general: '⚙️ Umum' }
function groupLabel(g) { return groupLabels[g] ?? g }
</script>
