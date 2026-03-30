<script setup lang="ts">
import { ref, watch } from 'vue'
import { Button } from '@/components/ui/button'
import axios from 'axios'
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog_sitrep_view'

interface Sitrep {
  id: number
  incident_type: string
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
  (e: 'saved', sitrep: Sitrep): void
  (e: 'edit', sitrep: Sitrep): void
}>()

const loading = ref(false)
const internalShow = ref(props.show)
const comments = ref('')
const email = ref('')
const message = ref('')

// Sync local show state with prop
watch(() => props.show, (val) => {
  internalShow.value = val
  if (!val) {
    comments.value = ''
    email.value = ''
    message.value = ''
  }
})

// Close modal
const closeModal = () => {
  internalShow.value = false
  emit('close')
}

const submitSitrep = async () => {
  if (!props.sitrep) return

  if (!email.value) {
    alert('Please enter recipient email')
    return
  }

  loading.value = true

  try {

    await axios.get('/sanctum/csrf-cookie', { withCredentials: true })


    const res = await axios.post(`/sitreps/${props.sitrep.id}/submit`,
      {
        email: email.value,
        message: message.value,
      },
      {
        withCredentials: true,
        headers: {
          Accept: 'application/json',
        },
      }
    )

    alert(res.data.message)
    emit('saved', props.sitrep)
    closeModal()

  } catch (error: any) {
    console.error('Error:', error)
    alert(error.response?.data?.message || 'Failed to submit sitrep')
  } finally {
    loading.value = false
  }
}

// Review / Create Recommendation
const reviewSitrep = () => {
  if (!props.sitrep) return
  emit('edit', props.sitrep)
  closeModal()
}

// Dynamic action label
const actionLabel = () => {
  if (!props.sitrep) return 'Action'
  if (props.sitrep.reviewed_by && props.sitrep.sitrep_file) return 'Create Recommendation'
  if (!props.sitrep.submitted_by && props.sitrep.reviewed_by) return 'Submit for Approval'
  return 'Review'
}
</script>

<template>
  <Dialog v-model:open="internalShow">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>
          {{ props.sitrep ? `Sitrep: ${props.sitrep.incident_type}` : 'Sitrep Details' }}
        </DialogTitle>
      </DialogHeader>

      <!-- Email input -->
      <div v-if="!props.sitrep?.submitted_by" class="my-4">
        <label class="block mb-1 font-medium">Recipient Email</label>
        <input
          type="email"
          v-model="email"
          class="w-full border rounded p-2"
          placeholder="Enter email address"
        />
      </div>

      <!-- Email message -->
      <div v-if="!props.sitrep?.submitted_by" class="my-4">
        <label class="block mb-1 font-medium">Email Message</label>
        <textarea
          v-model="message"
          class="w-full border rounded p-2"
          placeholder="Type your message here"
        ></textarea>
      </div>

      <DialogFooter class="flex justify-end space-x-2 mt-4">
        <Button variant="outline" @click="closeModal">Close</Button>
        <Button
          v-if="props.sitrep && props.sitrep.reviewed_by && props.sitrep.sitrep_file"
          @click="reviewSitrep"
        >
          {{ actionLabel() }}
        </Button>
        <Button
          v-else
          :loading="loading"
          @click="submitSitrep"
        >
          {{ actionLabel() }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
