<script setup lang="ts">
import { ref, watch } from 'vue'
import axios from 'axios'
import { Upload } from 'lucide-vue-next';
import { Button } from '@/components/ui/button'
import {
  Dialog,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog_sitrep_view'


interface Sitrep {
  id: number
  incident_type: string
  barangay: string
  municipal: string
  province: string
  submitted_by?: number | null
  reviewed_by?: number | null
  sitrep_file?: string | null
}

const props = defineProps<{
  show: boolean
  sitrep: Sitrep | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'uploaded', sitrep: Sitrep): void
}>()

const internalShow = ref(props.show)
const file = ref<File | null>(null)
const comments = ref('')
const loading = ref(false)

// Sync prop show with internal state
watch(() => props.show, val => {
  internalShow.value = val
  if (!val) {
    file.value = null
    comments.value = ''
  }
})

const closeModal = () => {
  internalShow.value = false
  emit('close')
}

const uploadApprovedSitrep = async () => {
  if (!props.sitrep) return
  if (!file.value) {
    alert('Please select a file to upload')
    return
  }

  loading.value = true

  try {
    // Get CSRF cookie first
    await axios.get('/sanctum/csrf-cookie', { withCredentials: true })

    const formData = new FormData()
    formData.append('file', file.value)
    formData.append('comments', comments.value)

    const res = await axios.post(
      `/sitreps/${props.sitrep.id}/upload-approved`,
      formData,
      {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
        withCredentials: true,
      }
    )

    alert(res.data.message)
    emit('uploaded', props.sitrep)
    closeModal()

  } catch (error: any) {
    console.error('Upload failed:', error)
    alert(error.response?.data?.message || 'Failed to upload approved Sitrep')
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <Dialog v-model:open="internalShow">
    <DialogContent class="sm:max-w-lg">
      <DialogHeader>
        <DialogTitle class="text-lg font-semibold text-center">
          {{
            props.sitrep
              ? `Upload Approved Sitrep`
              : "Upload Approved Sitrep"
          }}
        </DialogTitle>

        <p v-if="props.sitrep" class="text-sm text-muted-foreground text-center">
          {{ props.sitrep.incident_type }} in
          {{ props.sitrep.barangay }},
          {{ props.sitrep.municipality }},
          {{ props.sitrep.province }}
        </p>
      </DialogHeader>

      <!-- Upload Box (only show when no file selected) -->
      <div
        v-if="!file"
        class="border-2 border-dashed border-muted rounded-lg p-8 text-center hover:border-primary transition cursor-pointer"
      >
        <label class="flex flex-col items-center justify-center space-y-3 cursor-pointer">

          <Upload class="w-10 h-10 text-muted-foreground" />

          <span class="text-sm font-medium">
            Click to upload a PDF
          </span>

          <span class="text-xs text-muted-foreground">
            Only PDF files allowed
          </span>

          <input
            type="file"
            accept="application/pdf"
            class="hidden"
            @change="e => {
              const selected = e.target.files?.[0] || null

              if (selected && selected.type !== 'application/pdf') {
                alert('Only PDF files are allowed!')
                file = null
                e.target.value = ''
                return
              }

              file = selected
            }"
          />
        </label>
      </div>

      <!-- Selected File Preview -->
      <div
        v-if="file"
        class="mt-4 p-4 rounded-lg border bg-muted flex justify-between items-center"
      >
        <div>
          <p class="font-medium text-sm">{{ file.name }}</p>
          <p class="text-xs text-muted-foreground">
            {{ (file.size / 1024).toFixed(2) }} KB
          </p>
        </div>

        <Button
          size="sm"
          variant="outline"
          @click="file = null"
        >
          Change
        </Button>
      </div>

      <DialogFooter class="mt-6 flex justify-end gap-2">
        <Button variant="outline" @click="closeModal">
          Close
        </Button>

        <Button
          :disabled="!file"
          :loading="loading"
          @click="uploadApprovedSitrep"
        >
          Upload
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
