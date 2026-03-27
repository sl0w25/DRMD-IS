<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

defineProps<{
    title: string;
    value: string | number;
    description?: string;
    icon?: any;
    trend?: 'up' | 'down' | 'neutral';
}>();
</script>

<template>
    <Card class="rounded-2xl border shadow-sm">
        <CardHeader
            class="flex flex-row items-center justify-between space-y-0 pb-2"
        >
            <CardTitle class="text-sm font-medium text-muted-foreground">
                {{ title }}
            </CardTitle>

            <component
                v-if="icon"
                :is="icon"
                class="h-5 w-5 text-muted-foreground"
            />
        </CardHeader>

        <CardContent>
            <div class="text-3xl font-bold tracking-tight">
                {{ value }}
            </div>

            <p
                v-if="description"
                class="mt-2 text-xs"
                :class="{
                    'text-green-600': trend === 'up',
                    'text-red-600': trend === 'down',
                    'text-muted-foreground': trend === 'neutral' || !trend,
                }"
            >
                {{ description }}
            </p>
        </CardContent>
    </Card>
</template>
