<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar'
import { urlIsActive } from '@/lib/utils'
import { Link, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible'

// 👇 Accept items from parent (appsidebar.vue)
defineProps<{
    items: {
        title: string
        href?: string
        icon?: any
        children?: {
            title: string
            href: string
        }[]
    }[]
}>()

const page = usePage()
const openStates = ref<Record<string, boolean>>({})
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Platform</SidebarGroupLabel>

        <SidebarMenu>
            <SidebarMenuItem
                v-for="item in items"
                :key="item.title"
                class="group"
            >
                <!-- If item has children -->
                <Collapsible
                    v-if="item.children"
                    v-model:open="openStates[item.title]"
                >
                    <CollapsibleTrigger as-child>
                        <SidebarMenuButton
                            :is-active="urlIsActive(item.href, page.url)"
                            :tooltip="item.title"
                        >
                            <component :is="item.icon" />
                            <span>{{ item.title }}</span>
                        </SidebarMenuButton>
                    </CollapsibleTrigger>

                    <CollapsibleContent>
                        <div class="pl-6 mt-1 space-y-1">
                            <SidebarMenuButton
                                v-for="child in item.children"
                                :key="child.title"
                                as-child
                                :is-active="urlIsActive(child.href, page.url)"
                                :tooltip="child.title"
                            >
                                <Link :href="child.href">
                                    <span>{{ child.title }}</span>
                                </Link>
                            </SidebarMenuButton>
                        </div>
                    </CollapsibleContent>
                </Collapsible>

                <!-- If no children -->
                <template v-else>
                    <SidebarMenuButton
                        :is-active="urlIsActive(item.href, page.url)"
                        :tooltip="item.title"
                        as-child
                    >
                        <Link :href="item.href">
                            <component :is="item.icon" />
                            <span>{{ item.title }}</span>
                        </Link>
                    </SidebarMenuButton>
                </template>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
