<template>
    <div class="py-3 px-2 text-slate-800 text-lg md:text-2xl font-medium font-['Roboto'] tracking-tight md:leading-loose flex justify-between items-center bg-white">
        {{ title }}

        <div>
            <slot />
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    title: String
});

</script>
