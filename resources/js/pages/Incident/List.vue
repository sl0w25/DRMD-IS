<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { minor_incident } from '@/routes'
import { type BreadcrumbItem } from '@/types'
import { Head } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import { computed } from 'vue'
import { ref } from 'vue'
import View_Sitrep from './View_Sitrep.vue'
import { Button } from '@/components/ui/button'


import CreateSitrepModal from './Create.vue'

// ShadCN UI imports
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



const handleAction = (sitrep) => {
  if (sitrep.status === 'Approved Sitrep') {
    createRecommendation(sitrep)
  } else {
    openModal(sitrep)
  }
}

const createRecommendation = (sitrep) => {
  // your logic here
  console.log('Creating recommendation for:', sitrep)
}

const showModal = ref(false);
const selectedSitrep = ref<Sitrep | null>(null)

const openModal = (sitrep: Sitrep) => {
  selectedSitrep.value = sitrep
  showModal.value = true
}

const printPdf = (id: number) => {
  window.open(`/minor_incident/${id}/print`, "_blank")
}

const closeModal = () => {
  showModal.value = false
  selectedSitrep.value = null
}

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Minor Incidents', href: minor_incident()?.url ?? '#' },
]

const refreshData = () => {
  router.reload({ only: ['sitreps'] })
}


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

</script>

<template>
  <Head title="Minor Incidents" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex items-start justify-center mt-10">
      <Card class="w-full md:w-auto">

        <!-- Header -->
        <CardHeader>
          <CardTitle>List of Minor Incident</CardTitle>
          <CardDescription />
        </CardHeader>

        <!-- Content -->
        <CardContent>

          <!-- Create Button -->
          <div class="flex justify-end mr-5 mb-3">
            <CreateSitrepModal @saved="refreshData"/>
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
                  <TableHead class="w-[30px]">Action</TableHead>
                </TableRow>
              </TableHeader>

                <TableBody>
                    <TableRow
                        v-for="(sitrep, index) in props.sitreps?.data || []"
                        :key="sitrep.id">
                        <TableCell class="font-medium">
                        {{ startIndex + index + 1 }}
                        </TableCell>
                        <TableCell class="font-medium">{{ sitrep.province }}</TableCell>
                        <TableCell class="font-medium">{{ sitrep.municipality }}</TableCell>
                        <TableCell class="font-medium">{{ sitrep.barangay }}</TableCell>
                        <TableCell class="font-medium">{{ sitrep.incident_type }}</TableCell>
                        <TableCell class="font-medium">{{ sitrep.status }}</TableCell>
                        <TableCell class="font-medium">
                            <Button @click="handleAction(sitrep)" variant="outline">
                           {{ sitrep.status === 'Approved Sitrep' ? 'Create Recommendation' : 'Review' }}
                            </Button>
                        </TableCell>
                        <!-- <TableCell class="font-medium text-blue-600 cursor-pointer"
                        @click="printPdf(sitrep.id)">Print PDF</TableCell> -->
                    </TableRow>
                </TableBody>
            </Table>

            <View_Sitrep
            :show="showModal"
            :sitrep="selectedSitrep"
            @close="closeModal"
            @edit="openEditModal"  />


            <div class="flex justify-end mt-4 space-x-2">
                <button
                    v-for="link in props.sitreps.links"
                    :key="link.label"
                    v-html="link.label"
                    :disabled="!link.url"
                    :class="['px-3 py-1 rounded', link.active ? 'bg-blue-500 text-white' : 'bg-gray-200', !link.url && 'opacity-50 cursor-not-allowed']"
                    @click.prevent="link.url && $inertia.get(link.url)"
                ></button>
            </div>
          </div>

        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
