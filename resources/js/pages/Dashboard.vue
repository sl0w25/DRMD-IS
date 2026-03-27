<script setup lang="ts">
import Barchart from '@/components/Barchart.vue';
import StatCard from '@/components/StatCard.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import {
    AlertTriangle,
    FileText,
    PhilippinePeso,
    Users,
} from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="grid auto-rows-min gap-4 md:grid-cols-4">
                <StatCard
                    title="Total Beneficiaries"
                    :value="1250"
                    description="+12% from last month"
                    :icon="Users"
                    trend="up"
                />

                <StatCard
                    title="SITREP Reports"
                    :value="87"
                    description="+5 new today"
                    :icon="FileText"
                    trend="up"
                />

                <StatCard
                    title="Active Incidents"
                    :value="14"
                    description="-2 from yesterday"
                    :icon="AlertTriangle"
                    trend="down"
                />

                <StatCard
                    title="Funds Released"
                    value="₱2,450,000"
                    description="Updated today"
                    :icon="PhilippinePeso"
                    trend="neutral"
                />
            </div>
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
            >
                <Barchart />
            </div>
        </div>
    </AppLayout>
</template>
