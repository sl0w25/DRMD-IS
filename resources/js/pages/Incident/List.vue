<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { minor_incident } from '@/routes'
import { type BreadcrumbItem } from '@/types'
import { Head } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import View_Sitrep from './View_Sitrep.vue'
import Submit_Sitrep from './Submit_Sitrep.vue'
import CreateSitrepModal from './Create.vue'
import UploadApprovedSitrep from './UploadApprovedSitrep.vue'
import { Button } from '@/components/ui/button'

import {
  Card,
  CardHeader,
  CardTitle,
  CardDescription,
  CardContent
} from '@/components/ui/card'

import {
  Table,
  TableHeader,
  TableRow,
  TableHead,
  TableBody,
  TableCell,
  TableCaption
} from '@/components/ui/table'

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Minor Incidents', href: minor_incident()?.url ?? '#' },
]

const props = defineProps<{
  sitreps?: {
    data: Sitrep[]
    meta?: { current_page: number; last_page: number; per_page: number; total: number }
    links?: { url: string | null; label: string; active: boolean }[]
  }
}>()

const startIndex = computed(() => {
  const meta = props.sitreps?.meta
  if (!meta) return 0
  return (meta.current_page - 1) * meta.per_page
})

// Modal management
const currentModal = ref<null | 'view' | 'submit' | 'upload'>(null)
const currentSitrep = ref<Sitrep | null>(null)

const openModal = (sitrep: Sitrep, type: 'view' | 'submit' | 'upload') => {
  console.log('Opening modal:', type, sitrep.id)
  currentSitrep.value = sitrep
  currentModal.value = type
}

const closeModal = () => {
  currentModal.value = null
  currentSitrep.value = null
}

const refreshData = () => {
  router.reload({ only: ['sitreps'] })
}

// Status and action labels
const getStatusLabel = (sitrep: Sitrep) => {
  if (!sitrep.submitted_by && !sitrep.reviewed_by && !sitrep.sitrep_file) return 'Sitrep For Review'
  if (!sitrep.submitted_by && sitrep.reviewed_by) return 'Reviewed Sitrep'
  if (sitrep.reviewed_by && sitrep.sitrep_file) return 'Approved Sitrep'
  if (sitrep.submitted_by && sitrep.reviewed_by && !sitrep.sitrep_file) return 'Waiting For Approval'
  return 'Review'
}

const getActionButtonLabel = (sitrep: Sitrep) => {
  if (!sitrep.submitted_by && !sitrep.reviewed_by && !sitrep.sitrep_file) return 'Review'
  if (!sitrep.submitted_by && sitrep.reviewed_by) return 'Submit For Approval'
  if (sitrep.reviewed_by && sitrep.sitrep_file) return 'Create Recommendation'
  if (sitrep.submitted_by && sitrep.reviewed_by && !sitrep.sitrep_file) return 'Upload Approved Sitrep'
  return 'Review'
}

// Handle button actions
const handleAction = (sitrep: Sitrep) => {
  const label = getActionButtonLabel(sitrep)
  console.log('Action label:', label)

  switch (label) {
    case 'Review':
      openModal(sitrep, 'view') // opens View_Sitrep.vue
      break
    case 'Upload Approved Sitrep':
        openModal(sitrep, 'upload')
      break
    case 'Submit For Approval':
      openModal(sitrep, 'submit') // opens Submit_Sitrep.vue
      break
    case 'Create Recommendation':
      window.location.href = `/#`
      break
    default:
      openModal(sitrep, 'view')
      break
  }
}
</script>

<template>
  <Head title="Minor Incidents" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex items-start justify-center mt-10">
      <Card class="w-full md:w-auto">
        <CardHeader>
          <CardTitle>List of Minor Incidents</CardTitle>
          <CardDescription />
        </CardHeader>

        <CardContent>
          <!-- Create Button -->
          <div class="flex justify-end mr-5 mb-3">
            <CreateSitrepModal @saved="refreshData" />
          </div>

          <!-- Table -->
          <div class="overflow-x-auto">
            <Table class="table-auto w-full mb-10">
              <TableCaption>List of Minor Incidents in Region III</TableCaption>

              <TableHeader>
                <TableRow>
                  <TableHead class="w-[70px]">No.</TableHead>
                  <TableHead class="w-[300px]">Province</TableHead>
                  <TableHead class="w-[300px]">Municipality</TableHead>
                  <TableHead class="w-[300px]">Barangay</TableHead>
                  <TableHead class="w-[300px]">Type of Incident</TableHead>
                  <TableHead class="w-[200px]">Status</TableHead>
                  <TableHead class="w-[150px]">Action</TableHead>
                </TableRow>
              </TableHeader>

              <TableBody>
                <TableRow
                  v-for="(sitrep, index) in props.sitreps?.data || []"
                  :key="sitrep.id"
                >
                  <TableCell class="font-medium">
                    {{ startIndex + index + 1 }}
                  </TableCell>
                  <TableCell class="font-medium">{{ sitrep.province }}</TableCell>
                  <TableCell class="font-medium">{{ sitrep.municipality }}</TableCell>
                  <TableCell class="font-medium">{{ sitrep.barangay }}</TableCell>
                  <TableCell class="font-medium">{{ sitrep.incident_type }}</TableCell>
                  <TableCell class="font-medium">{{ getStatusLabel(sitrep) }}</TableCell>
                  <TableCell class="font-medium">
                    <Button @click="handleAction(sitrep)" variant="outline">
                      {{ getActionButtonLabel(sitrep) }}
                    </Button>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>

            <!-- Modals -->
            <View_Sitrep
              v-if="currentModal === 'view'"
              :sitrep="currentSitrep"
              :show="currentModal === 'view'"
              @close="closeModal"
            />
            <Submit_Sitrep
              v-if="currentModal === 'submit'"
              :sitrep="currentSitrep"
              :show="currentModal === 'submit'"
              @close="closeModal"
              @saved="refreshData"
            />

            <UploadApprovedSitrep
            v-if="currentModal === 'upload'"
            :sitrep="currentSitrep"
            :show="currentModal === 'upload'"
            @close="closeModal"
            @uploaded="refreshData"
            />

            <!-- Pagination -->
            <div class="flex justify-end mt-4 space-x-2">
              <button
                v-for="link in props.sitreps?.links || []"
                :key="link.label"
                v-html="link.label"
                :disabled="!link.url"
                :class="[
                  'px-3 py-1 rounded',
                  link.active ? 'bg-blue-500 text-white' : 'bg-gray-200',
                  !link.url && 'opacity-50 cursor-not-allowed'
                ]"
                @click.prevent="link.url && $inertia.get(link.url)"
              ></button>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
