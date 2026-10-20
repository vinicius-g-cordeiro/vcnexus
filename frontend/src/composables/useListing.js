import { ref, reactive } from 'vue'
import api from '@/services/apiService'

export function useListing(endpoint, perPage = 15) {
  const items = ref([])
  const loading = ref(false)
  const error = ref(null)
  const meta = reactive({ page: 1, perPage, total: 0 })

  let controller = null

  async function load(filters = {}, page = 1) {
    controller?.abort()
    controller = new AbortController()
    loading.value = true
    error.value = null

    const params = new URLSearchParams({ page, per_page: meta.perPage })
    for (const [k, v] of Object.entries(filters)) {
      if (v !== '' && v !== null && v !== undefined) params.append(k, v)
    }

    try {
      let res = await api.query(`${endpoint}?${params}`, { signal: controller.signal })
      res = res.data
      items.value = res.data.list
      meta.page = res.data.meta.page
      meta.total = res.data.meta.total
    } catch (e) {
      if (e.name !== 'AbortError') error.value = e.message
    } finally {
      loading.value = false
    }
  }

  async function clear() {
    items.value = []
    meta.page = 1
    meta.total = 0
  }

  return { items, loading, error, meta, load, clear }
}