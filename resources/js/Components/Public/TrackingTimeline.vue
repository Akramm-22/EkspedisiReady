<script setup>
defineProps({
    timeline: { type: Array, required: true },
});
</script>

<template>
    <ol class="relative ml-3 border-l-2 border-slate-100">
        <li v-for="(step, i) in timeline" :key="step.status" class="mb-8 ml-6 last:mb-0">
            <span class="absolute -left-[9px] grid h-4 w-4 place-items-center rounded-full ring-4 ring-white"
                  :class="step.is_completed ? 'bg-brand-gradient' : 'bg-slate-200'" />

            <div class="flex flex-wrap items-baseline justify-between gap-x-3">
                <h4 class="font-semibold" :class="step.is_completed ? 'text-slate-800' : 'text-slate-400'">
                    {{ step.label }}
                </h4>
                <time v-if="step.timestamp" class="text-xs text-slate-400">
                    {{ new Date(step.timestamp).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' }) }}
                </time>
            </div>
            <p v-if="step.description" class="mt-1 text-sm text-slate-500">{{ step.description }}</p>
            <p v-if="step.location" class="mt-0.5 text-xs font-medium text-transit-600">{{ step.location }}</p>
        </li>
    </ol>
</template>
