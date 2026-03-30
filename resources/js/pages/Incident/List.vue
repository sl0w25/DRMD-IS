<script setup lang="ts">
<<<<<<< HEAD
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { minor_incident } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import View_Sitrep from './View_Sitrep.vue';

import CreateSitrepModal from './Create.vue';

// ShadCN UI imports
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
=======
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
>>>>>>> 1e2c656c6a583bd698a746a432ef8ce29f6c791c

import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

const handleAction = (sitrep) => {
    if (sitrep.status === 'Approved Sitrep') {
        createRecommendation(sitrep);
    } else {
        openModal(sitrep);
    }
};

const createRecommendation = (sitrep) => {
    // your logic here
    if (sitrep.status === 'Create Recommendation') {
        createRecommendation(sitrep);
    }
    // else {
    //     openModal(sitrep);
    // }
};

const showModal = ref(false);
const selectedSitrep = ref<Sitrep | null>(null);

const openModal = (sitrep: Sitrep) => {
    selectedSitrep.value = sitrep;
    showModal.value = true;
};

<<<<<<< HEAD
const printPdf = (id: number) => {
    window.open(`/minor_incident/${id}/print`, '_blank');
};
=======
// Modal management
const currentModal = ref<null | 'view' | 'submit' | 'upload'>(null)
const currentSitrep = ref<Sitrep | null>(null)

const openModal = (sitrep: Sitrep, type: 'view' | 'submit' | 'upload') => {
  console.log('Opening modal:', type, sitrep.id)
  currentSitrep.value = sitrep
  currentModal.value = type
}
>>>>>>> 1e2c656c6a583bd698a746a432ef8ce29f6c791c

const closeModal = () => {
    showModal.value = false;
    selectedSitrep.value = null;
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Minor Incidents', href: minor_incident()?.url ?? '#' },
];

const refreshData = () => {
    router.reload({ only: ['sitreps'] });
};

const props = defineProps<{
    sitreps?: {
        data: Sitrep[];
        meta?: {
            current_page: number;
            last_page: number;
            per_page: number;
            total: number;
        };
        links?: { url: string | null; label: string; active: boolean }[];
    };
}>();

<<<<<<< HEAD
const startIndex = computed(() => {
    const meta = props.sitreps?.meta;
    if (!meta) return 0;
    return (meta.current_page - 1) * meta.per_page;
});
=======
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
>>>>>>> 1e2c656c6a583bd698a746a432ef8ce29f6c791c
</script>

<template>
    <Head title="Minor Incidents" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mt-10 flex items-start justify-center">
            <Card class="w-full md:w-auto">
                <!-- Header -->
                <CardHeader>
                    <CardTitle>List of Minor Incident</CardTitle>
                    <CardDescription />
                </CardHeader>

                <!-- Content -->
                <CardContent>
                    <!-- Create Button -->
                    <div class="mr-5 mb-3 flex justify-end">
                        <CreateSitrepModal @saved="refreshData" />
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <Table class="mb-10 w-full table-auto">
                            <TableCaption
                                >List of Minor Incidents in Region
                                III</TableCaption
                            >

                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-[70px]">No.</TableHead>
                                    <TableHead class="w-[300px]"
                                        >Province</TableHead
                                    >
                                    <TableHead class="w-[300px]"
                                        >Municipality</TableHead
                                    >
                                    <TableHead class="w-[300px]"
                                        >Barangay</TableHead
                                    >
                                    <TableHead class="w-[300px]"
                                        >Type of Incident</TableHead
                                    >
                                    <TableHead class="w-[200px]"
                                        >Status</TableHead
                                    >
                                    <TableHead class="w-[30px]"
                                        >Action</TableHead
                                    >
                                </TableRow>
                            </TableHeader>

                            <TableBody>
                                <TableRow
                                    v-for="(sitrep, index) in props.sitreps
                                        ?.data || []"
                                    :key="sitrep.id"
                                >
                                    <TableCell class="font-medium">
                                        {{ startIndex + index + 1 }}
                                    </TableCell>
                                    <TableCell class="font-medium">{{
                                        sitrep.province
                                    }}</TableCell>
                                    <TableCell class="font-medium">{{
                                        sitrep.municipality
                                    }}</TableCell>
                                    <TableCell class="font-medium">{{
                                        sitrep.barangay
                                    }}</TableCell>
                                    <TableCell class="font-medium">{{
                                        sitrep.incident_type
                                    }}</TableCell>
                                    <TableCell class="font-medium">{{
                                        sitrep.status
                                    }}</TableCell>
                                    <TableCell class="font-medium">
                                        <Button
                                            @click="handleAction(sitrep)"
                                            variant="outline"
                                        >
                                            {{
                                                sitrep.status ===
                                                'Approved Sitrep'
                                                    ? 'Create Recommendation'
                                                    : 'Review'
                                            }}
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
                            @edit="openEditModal"
                        />

<<<<<<< HEAD
                        <div class="mt-4 flex justify-end space-x-2">
                            <button
                                v-for="link in props.sitreps.links"
                                :key="link.label"
                                v-html="link.label"
                                :disabled="!link.url"
                                :class="[
                                    'rounded px-3 py-1',
                                    link.active
                                        ? 'bg-blue-500 text-white'
                                        : 'bg-gray-200',
                                    !link.url &&
                                        'cursor-not-allowed opacity-50',
                                ]"
                                @click.prevent="
                                    link.url && $inertia.get(link.url)
                                "
                            ></button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
=======
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
>>>>>>> 1e2c656c6a583bd698a746a432ef8ce29f6c791c
</template>
