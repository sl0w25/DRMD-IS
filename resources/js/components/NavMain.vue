<script setup lang="ts">
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuBadge,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { urlIsActive } from '@/lib/utils';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronRight } from 'lucide-vue-next';
import { reactive } from 'vue';

defineProps<{
    items: NavItem[];
    collapsed?: boolean; // 👈 add this
}>();

const page = usePage();
const openGroups = reactive<{ [key: string]: boolean }>({});
const toggleGroup = (title: string) => {
    openGroups[title] = !openGroups[title];
};
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <!-- Hide label if collapsed -->
        <SidebarGroupLabel v-if="!collapsed">Platform</SidebarGroupLabel>
        <SidebarMenu>
            <template v-for="item in items" :key="item.title">
                <Collapsible
                    v-if="item.item && item.item.length > 0"
                    :open="
                        openGroups[item.title] ||
                        urlIsActive(item.href, page.url) ||
                        item.item.some((child) =>
                            urlIsActive(child.href, page.url),
                        )
                    "
                    @update:open="() => toggleGroup(item.title)"
                    class="group/collapsible"
                >
                    <SidebarMenuItem>
                        <CollapsibleTrigger as-child>
                            <SidebarMenuButton
                                :is-active="urlIsActive(item.href, page.url)"
                                :tooltip="collapsed ? item.title : ''"
                            >
                                <div
                                    class="flex w-full items-center justify-between"
                                >
                                    <div class="flex items-center gap-2">
                                        <component
                                            v-if="item.icon"
                                            :is="item.icon"
                                            class="h-4 w-4"
                                        />
                                        <!-- Hide text when collapsed -->
                                        <span v-if="!collapsed">{{
                                            item.title
                                        }}</span>
                                    </div>
                                    <div
                                        v-if="!collapsed"
                                        class="flex items-center gap-1"
                                    >
                                        <SidebarMenuBadge v-if="item.badge">{{
                                            item.badge
                                        }}</SidebarMenuBadge>
                                        <ChevronRight
                                            class="h-4 w-4 transition-transform"
                                            :class="
                                                openGroups[item.title]
                                                    ? 'rotate-90'
                                                    : ''
                                            "
                                        />
                                    </div>
                                </div>
                            </SidebarMenuButton>
                        </CollapsibleTrigger>
                    </SidebarMenuItem>

                    <!-- Hide submenus if sidebar collapsed -->
                    <CollapsibleContent
                        v-if="!collapsed"
                        class="flex flex-col gap-1 pl-8"
                    >
                        <SidebarMenuItem
                            v-for="child in item.item"
                            :key="child.title"
                        >
                            <SidebarMenuButton
                                as-child
                                :is-active="urlIsActive(child.href, page.url)"
                                :tooltip="child.title"
                            >
                                <Link
                                    :href="child.href"
                                    class="flex items-center gap-2"
                                >
                                    <component
                                        v-if="child.icon"
                                        :is="child.icon"
                                        class="h-4 w-4"
                                    />
                                    <span>{{ child.title }}</span>
                                    <SidebarMenuBadge v-if="child.badge">{{
                                        child.badge
                                    }}</SidebarMenuBadge>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </CollapsibleContent>
                </Collapsible>

                <!-- Normal item -->
                <SidebarMenuItem v-else>
                    <SidebarMenuButton
                        as-child
                        :is-active="urlIsActive(item.href, page.url)"
                        :tooltip="collapsed ? item.title : ''"
                    >
                        <Link :href="item.href" class="flex items-center gap-2">
                            <component
                                v-if="item.icon"
                                :is="item.icon"
                                class="h-4 w-4"
                            />
                            <span v-if="!collapsed">{{ item.title }}</span>
                            <SidebarMenuBadge v-if="!collapsed && item.badge">{{
                                item.badge
                            }}</SidebarMenuBadge>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </template>
        </SidebarMenu>
    </SidebarGroup>
</template>
