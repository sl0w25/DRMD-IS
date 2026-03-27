<script setup lang="ts">
import { Button } from '@/components/ui/button'
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog_sitrep_view'
import { Separator } from '@/components/ui/separator'

interface Sitrep {
  id: number
  province: string
  municipality: string
  barangay: string
  incident_type: string
  status: string
  overview: string
  affected_families: string
  affected_individuals: string
  totally_damaged: string
  partially_damaged: string
  displaced_family: string
  displaced_individual: string
  dswd_cost: string
  lgu_cost: string
  ngo_cost: string
  partners_cost: string
  reviewed_by: string | null
}

const props = defineProps<{
  show: boolean
  sitrep: Sitrep | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
}>()

const close = () => {
  emit('close')
}

const printPdf = (id: number) => {
  window.open(`/minor_incident/${id}/print`, "_blank")
}
</script>

<template>
  <Dialog v-model:open="props.show">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>Incident Details</DialogTitle>
        <DialogDescription>
          View and edit the details of this minor incident.
        </DialogDescription>
      </DialogHeader>

      <div class="space-y-6">
        <!-- Incident Info -->
        <div class="grid gap-2">
          <div class="flex items-start gap-2">
            <span class="w-40 font-medium">Province:</span>
            <span class="break-words">{{ props.sitrep?.province }}</span>
          </div>
          <div class="flex items-start gap-2">
            <span class="w-40 font-medium">Municipality:</span>
            <span class="break-words">{{ props.sitrep?.municipality }}</span>
          </div>
          <div class="flex items-start gap-2">
            <span class="w-40 font-medium">Barangay:</span>
            <span class="break-words">{{ props.sitrep?.barangay }}</span>
          </div>
          <div class="flex items-start gap-2">
            <span class="w-40 font-medium">Type of Incident:</span>
            <span class="break-words">{{ props.sitrep?.incident_type }}</span>
          </div>
          <div class="flex items-start gap-2">
            <span class="w-60 font-medium">Situationer:</span>
            <span class="break-words">{{ props.sitrep?.overview }}</span>
          </div>
        </div>

        <Separator class="my-4" />

        <!-- Cost & Affected -->
        <div class="text-center py-2 font-medium">Status of Affected, Displaced, and Damaged Houses</div>
        <div class="flex h-5 items-center space-x-20 text-m">
          <div class="font-medium">Affected Families: {{ props.sitrep?.affected_families }}</div>
          <Separator orientation="vertical" />
          <div class="font-medium">Affected Individual: {{ props.sitrep?.affected_individuals }}</div>
          <Separator orientation="vertical" />
          <div class="font-medium">Totally Damaged Houses: {{ props.sitrep?.totally_damaged }}</div>
          <Separator orientation="vertical" />
          <div class="font-medium">Partially Damaged Houses: {{ props.sitrep?.partially_damaged }}</div>
        </div>
      </div>

      <DialogFooter class="space-x-2">
        <DialogClose as-child>
          <Button variant="outline" @click="close">Close</Button>
        </DialogClose>
        <Button @click="props.sitrep && printPdf(props.sitrep.id)" class="bg-blue-600 hover:bg-blue-700 text-white">
          View Sitrep
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
