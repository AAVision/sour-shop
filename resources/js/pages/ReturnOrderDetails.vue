<template>
    <div>
        <div class="bg-white px-3 text-slate-600 flex items-center gap-1 pt-2">
            <HomeIcon class="w-5 h-5 md:w-6 md:h-6" />
            <router-link
                to="/returned-order-history"
                class="leading-normal hover:text-primary"
            >
                {{ $t("Return Order History") }}
            </router-link>
            <span class="leading-normal"
                >/ {{ $t("Return Order Details") }}</span
            >
        </div>

        <!-- Header -->
        <OrderDetailsPageHeader :title="$t('Order Details')" />

        <!-- Order details -->
        <div class="px-2 pt-2 md:px-4 md:pt-4 lg:px-6 lg:pt-8">
            <div
                v-if="!isLoading && order"
                class="grid grid-cols-8 gap-2 lg:gap-4 xl:gap-8 p-3 md:p-4 xl:p-8 bg-white rounded-lg md:rounded-2xl"
            >
                <div class="col-span-8 lg:col-span-4 xl:col-span-5">
                    <div
                        class="p-6 bg-white rounded-xl outline outline-1 outline-offset-[-1px] outline-slate-100"
                    >
                        <p
                            class="self-stretch justify-start text-slate-600 text-sm font-normal font-['Roboto'] leading-tight"
                        >
                            Products ({{
                                order?.return_order_products?.length
                            }})
                        </p>

                        <div class="mt-3 space-y-4">
                            <div
                                v-for="(
                                    product, index
                                ) in order?.return_order_products"
                                :key="product.id"
                                class="flex justify-start items-center gap-4 py-4"
                                :class="
                                    index !=
                                    order?.return_order_products?.length - 1
                                        ? ' border-b border-slate-100'
                                        : ''
                                "
                            >
                                <div
                                    class="w-16 h-16 relative rounded-lg overflow-hidden"
                                >
                                    <img
                                        :src="product.thumbnail"
                                        alt=""
                                        class="w-full h-full"
                                    />
                                </div>
                                <div class="space-y-1">
                                    <p
                                        class="self-stretch justify-start text-slate-950 text-base font-normal leading-normal"
                                    >
                                        {{ truncate(product.product_name, 50) }}
                                    </p>
                                    <div
                                        class="flex justify-start items-center gap-1"
                                    >
                                        <p
                                            class="justify-center text-rose-600 text-sm font-bold font-['Roboto'] leading-tight"
                                        >
                                            {{ product.quantity }}x
                                        </p>
                                        <p
                                            class="text-rose-600 text-sm font-semibold leading-tight"
                                        >
                                            {{
                                                master.showCurrency(
                                                    product.price
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-8 lg:col-span-4 xl:col-span-3">
                    <div
                        class="p-6 outline outline-1 outline-offset-[-1px] outline-slate-100 bg-slate-50 rounded-xl"
                    >
                        <div class="flex justify-between items-center py-[6px]">
                            <p
                                class="self-stretch justify-start text-slate-600 text-base font-normal leading-tight"
                            >
                                Return Status
                            </p>
                            <div
                                class="px-2 py-1 rounded-2xl inline-flex justify-start items-center gap-2"
                                :class="
                                    order?.status === 'Pending'
                                        ? 'bg-gray-500'
                                        : order?.status === 'Mismatch'
                                        ? 'bg-orange-500'
                                        : order?.status === 'Approved'
                                        ? 'bg-blue-500'
                                        : order?.status === 'Damaged'
                                        ? 'bg-yellow-500'
                                        : order?.status === 'Refunded'
                                        ? 'bg-green-500'
                                        : order?.status === 'Cancelled'
                                        ? 'bg-red-500'
                                        : ''
                                "
                            >
                                <p
                                    class="text-right justify-center text-white text-xs font-normal leading-none"
                                >
                                    {{ order?.status }}
                                </p>
                            </div>
                        </div>

                        <div class="flex justify-between items-center py-[6px]">
                            <p
                                class="self-stretch justify-start text-slate-600 text-base font-normal font-['Roboto'] leading-tight"
                            >
                                Order ID
                            </p>
                            <div
                                class="self-stretch text-right justify-start text-zinc-800 text-base font-normal font-['Roboto'] leading-tight"
                            >
                                {{ order?.order_id }}
                            </div>
                        </div>

                        <div class="flex justify-between items-center py-[6px]">
                            <p
                                class="self-stretch justify-start text-slate-600 text-base font-normal font-['Roboto'] leading-tight"
                            >
                                Return Date
                            </p>
                            <div
                                class="self-stretch text-right justify-start text-zinc-800 text-base font-normal font-['Roboto'] leading-tight"
                            >
                                {{ order?.return_date }}
                            </div>
                        </div>

                        <div class="flex justify-between items-center py-[6px]">
                            <p
                                class="self-stretch justify-start text-slate-600 text-base font-normal font-['Roboto'] leading-tight"
                            >
                                Total Amount
                            </p>
                            <div
                                class="self-stretch text-right justify-start text-zinc-800 text-base font-normal font-['Roboto'] leading-tight"
                            >
                                {{ master.showCurrency(order?.amount) }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <!-- shop info -->
                            <div
                                class="p-2 flex justify-start items-start gap-4 bg-white rounded-xl"
                            >
                                <div class="w-12 h-12">
                                    <img
                                        :src="order?.shop_logo"
                                        alt=""
                                        class="object-cover"
                                    />
                                </div>
                                <div>
                                    <p
                                        class="text-slate-950 text-base font-medium leading-normal"
                                    >
                                        {{ order?.shop_name }}
                                    </p>

                                    <div
                                        class="flex justify-start items-center gap-1"
                                    >
                                        <StarIcon
                                            class="w-4 h-4 text-yellow-400"
                                        />
                                        <p
                                            class="text-slate-800 text-sm font-medium leading-tight"
                                        >
                                            {{ order?.shop_rating }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- shop address  -->
                            <div class="p-3 space-y-3 bg-white rounded-xl">
                                <div
                                    class="flex justify-start items-center gap-2"
                                >
                                    <img
                                        src="../../../public/assets/icons/map-pin.png"
                                        alt=""
                                        class="w-5 h-5"
                                    />

                                    <div
                                        class="px-1 py-0.5 bg-slate-900 rounded inline-flex justify-start items-center gap-2"
                                    >
                                        <div
                                            class="text-center justify-center text-white text-sm font-normal leading-none"
                                        >
                                            Collection Address
                                        </div>
                                    </div>
                                </div>
                                <p
                                    class="text-slate-500 text-sm font-normal font-['Roboto'] leading-none"
                                >
                                    {{ order?.return_address }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    v-if="order?.reason"
                    class="col-span-8 p-4 bg-slate-50 rounded-lg space-y-2"
                >
                    <p
                        class="text-slate-500 text-base font-normal leading-none"
                    >
                        Reason for Return
                    </p>

                    <p
                        class="text-gray-900 text-lg font-normal leading-relaxed"
                    >
                        {{ order?.reason }}
                    </p>
                </div>

                <div
                    v-if="order?.reject_note"
                    class="col-span-8 p-4 bg-slate-50 rounded-lg space-y-2"
                >
                    <p
                        class="text-slate-500 text-base font-normal leading-none"
                    >
                        Reason for Cancellation
                    </p>

                    <p
                        class="text-gray-900 text-lg font-normal leading-relaxed"
                    >
                        {{ order?.reject_note ? order?.reject_note : "None" }}
                    </p>
                </div>
            </div>
            <div
                v-else
                class="grid grid-cols-8 gap-2 lg:gap-4 xl:gap-8 p-3 md:p-4 xl:p-8 bg-white rounded-lg md:rounded-2xl"
            >
                No Data Found
            </div>
        </div>
    </div>
</template>

<script setup>
import { HomeIcon } from "@heroicons/vue/24/outline";
import { StarIcon } from "@heroicons/vue/24/solid";
import { onMounted, ref, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import AuthPageHeader from "../components/AuthPageHeader.vue";
import OrderDetailsOrderStatus from "../components/OrderDetailsOrderStatus.vue";
import OrderDetailsSummery from "../components/OrderDetailsSummery.vue";
import OrderProducts from "../components/OrderProducts.vue";

import { useAuth } from "../stores/AuthStore";
import OrderDetailsPageHeader from "../components/OrderDetailsPageHeader.vue";
import { useMaster } from "../stores/MasterStore";
import { useTruncateText } from "../composables/useTruncateText";

// apis
const returnOrderDetailsAPi = "/return-order-details/";

const master = useMaster();
const authStore = useAuth();
const route = useRoute();
const router = useRouter();
const { truncate } = useTruncateText();

const order = ref(null);
const isLoading = ref(true);

watch(
    () => authStore.orderCancel,
    () => {
        if (authStore.orderCancel == true) {
            fetchReturnOrderDetails();
        }
        authStore.orderCancel = false;
    }
);

onMounted(() => {
    fetchReturnOrderDetails();
    window.scrollTo(0, 0, { behavior: "smooth" });
});

const fetchReturnOrderDetails = async () => {
    isLoading.value = true;
    axios
        .get(returnOrderDetailsAPi + route.params.id, {
            headers: {
                Authorization: authStore.token,
            },
        })
        .then((response) => {
            isLoading.value = false;
            order.value = response.data.data.returnOrders;
        })
        .catch((error) => {
            isLoading.value = false;
            if (error.response.status === 401) {
                authStore.token = null;
                authStore.user = null;
                authStore.addresses = [];
                authStore.favoriteProducts = 0;
                router.push("/");
            }
        });
};
</script>
