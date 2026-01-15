<template>
    <div
        class="grid grid-cols-7 gap-2 md:gap-4 lg:gap-8 py-3 px-4 rounded-lg outline outline-1 outline-offset-[-1px] outline-slate-100"
    >
        <div class="col-span-7 lg:col-span-2">
            <div class="flex justify-between items-center">
                <p
                    class="justify-start text-slate-500 text-base font-normal leading-normal"
                >
                    Order ID
                    <span class="text-blue-500">{{
                        props.order?.order_id
                    }}</span>
                </p>

                <router-link
                    :to="'/returned-order-history/' + props.order?.id"
                    class="block lg:hidden text-slate-500 text-xs font-normal cursor-pointer leading-normal"
                >
                    {{ $t("View Details") }}
                </router-link>
            </div>

            <div class="inline-flex justify-start items-center gap-2">
                <p
                    class="justify-start text-slate-500 text-sm font-normal font-['Roboto'] leading-tight"
                >
                    Placed on
                </p>
                <p
                    class="justify-start text-slate-950 text-sm font-normal font-['Roboto'] leading-tight"
                >
                    {{ props.order?.return_date }}
                </p>
            </div>
        </div>
        <div
            class="col-span-2 lg:col-span-1 flex justify-start md:justify-center items-center gap-1"
        >
            <p
                class="justify-start text-slate-500 text-base font-normal leading-normal"
            >
                QTY:
            </p>
            <p
                class="justify-start text-slate-950 text-base font-normal leading-normal"
            >
                {{ props.order?.quantity ? props.order?.quantity : 0 }}
            </p>
        </div>
        <div
            class="col-span-2 lg:col-span-1 flex justify-start md:justify-center items-center gap-1"
        >
            <p
                class="justify-start text-slate-500 text-base font-normal leading-normal"
            >
                Amount:
            </p>
            <p
                class="justify-start text-slate-950 text-base font-normal leading-normal"
            >
                {{ master.showCurrency(props.order?.amount) }}
            </p>
        </div>
        <div
            class="col-span-7 md:col-span-3 lg:col-span-1 flex justify-start md:justify-center items-center"
        >
            <button
                v-if="props.order?.status === 'Pending'"
                class="w-20 px-1.5 py-0.5 bg-gray-500 rounded-[10px] inline-flex justify-center items-center gap-2"
            >
                <p
                    class="justify-start text-white text-sm font-normal leading-tight"
                >
                    {{ props.order?.status }}
                </p>
            </button>

            <button
                v-if="props.order?.status === 'Mismatch'"
                class="w-20 px-1.5 py-0.5 bg-orange-500 rounded-[10px] inline-flex justify-center items-center gap-2"
            >
                <p
                    class="justify-start text-white text-sm font-normal leading-tight"
                >
                    {{ props.order?.status }}
                </p>
            </button>

            <button
                v-else-if="props.order?.status === 'Approved'"
                class="w-20 px-1.5 py-0.5 bg-blue-500 rounded-[10px] inline-flex justify-center items-center gap-2"
            >
                <p
                    class="justify-start text-white text-sm font-normal leading-tight"
                >
                    {{ props.order?.status }}
                </p>
            </button>

            <button
                v-else-if="props.order?.status === 'Cancelled'"
                class="w-20 px-1.5 py-0.5 bg-red-500 rounded-[10px] inline-flex justify-center items-center gap-2"
            >
                <p
                    class="justify-start text-white text-sm font-normal leading-tight"
                >
                    {{ props.order?.status }}
                </p>
            </button>
            <button
                v-else-if="props.order?.status === 'Damaged'"
                class="w-20 px-1.5 py-0.5 bg-yellow-500 rounded-[10px] inline-flex justify-center items-center gap-2"
            >
                <p
                    class="justify-start text-white text-sm font-normal leading-tight"
                >
                    {{ props.order?.status }}
                </p>
            </button>
            <button
                v-else-if="props.order?.status === 'Refunded'"
                class="w-20 px-1.5 py-0.5 bg-green-500 rounded-[10px] inline-flex justify-center items-center gap-2"
            >
                <p
                    class="justify-start text-white text-sm font-normal leading-tight"
                >
                    {{ props.order?.status }}
                </p>
            </button>
        </div>
        <div
            class="col-span-7 lg:col-span-2 hidden lg:flex justify-end items-center"
        >
            <router-link
                :to="'/returned-order-history/' + props.order?.id"
                class="text-slate-500 text-base font-normal cursor-pointer leading-normal"
            >
                {{ $t("View Details") }}
            </router-link>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    order: Object,
});

import { useMaster } from "../stores/MasterStore";

const master = useMaster();
</script>

<style scoped>
.Pending {
    @apply bg-yellow-500 text-white;
}

.Confirm {
    @apply bg-blue-500 text-white;
}

.Processing,
.On,
.Pickup {
    @apply bg-primary text-white;
}

.Delivered {
    @apply bg-green-500 text-white;
}

.Cancelled {
    @apply bg-red-500 text-white;
}
</style>
