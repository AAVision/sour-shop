<template>
    <div>
        <div class="bg-white px-3 text-slate-600 flex items-center gap-1 pt-2">
            <HomeIcon class="w-5 h-5 md:w-6 md:h-6" />
            <router-link
                to="/order-history"
                class="leading-normal hover:text-primary"
            >
                {{ $t("Order History") }}
            </router-link>
            <span class="leading-normal">/ {{ $t("Order Details") }}</span>
        </div>

        <!-- Header -->
        <OrderDetailsPageHeader :title="$t('Order Details')">
            <RouterLink :to="'/return-product/' + order.id">
                <button
                    v-if="order.is_returnable"
                    class="h-10 px-5 py-1 rounded outline outline-1 outline-offset-[-1px] outline-primary inline-flex justify-start items-center gap-2 text-primary text-base font-medium leading-normal transition-all duration-200 hover:bg-primary hover:text-white"
                >
                    {{ $t('Return Product') }}
                </button>
            </RouterLink>
        </OrderDetailsPageHeader>

        <!-- Order details -->
        <div class="px-2 pt-2 md:px-4 md:pt-4 lg:px-6 lg:pt-8">
            <OrderDetailsOrderStatus :order="order" />

            <div
                class="grid grid-cols-3 gap-4 md:gap-6 p-3 md:p-4 xl:p-8 bg-white rounded-lg md:rounded-2xl"
            >
                <!-- column 1 -->
                <div class="col-span-3 lg:col-span-2 bg-white">
                    <div
                        class="rounded-xl md:rounded-2xl outline outline-1 outline-offset-[-1px] outline-slate-100 p-3 md:p-4 xl:p-6"
                    >
                        <div
                            class="text-slate-500 text-xs md:text-sm font-normal leading-tight md:leading-none"
                        >
                            {{ $t("Purchased from") }}
                        </div>

                        <div class="flex items-center gap-4 mt-2">
                            <img
                                class="w-9 h-9 md:w-12 md:h-12"
                                :src="order.shop?.logo"
                            />
                            <div
                                class="flex flex-wrap gap-2 justify-between items-start grow"
                            >
                                <div>
                                    <div
                                        class="text-slate-950 text-sm md:text-base font-medium  leading-tight md:leading-normal"
                                    >
                                        {{ order.shop?.name }}
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <StarIcon
                                            class="w-5 h-5 text-yellow-400"
                                        />
                                        <span
                                            class="text-slate-800 text-xs md:text-sm font-medium leading-tight"
                                            >{{ order.shop?.rating }}</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="w-full h-[0px] border-t border-slate-200 my-4"
                        ></div>

                        <div
                            class="text-slate-600 text-sm font-normal leading-tight"
                        >
                            {{ $t("Products") }} ({{ order.products?.length }})
                        </div>

                        <OrderProducts
                            :order="order"
                            @refresh="fetchOrderDetails"
                        />
                    </div>
                </div>

                <!-- column 2 -->
                <div class="col-span-3 lg:col-span-1">
                    <OrderDetailsSummery
                        :order="order"
                        @update:paymentSuccess="fetchOrderDetails"
                    />
                </div>
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
const authStore = useAuth();
const route = useRoute();
const router = useRouter();

const order = ref({});



watch(
    () => authStore.orderCancel,
    () => {
        if (authStore.orderCancel == true) {
            fetchOrderDetails();
        }
        authStore.orderCancel = false;
    }
);

onMounted(() => {
    fetchOrderDetails();
    window.scrollTo(0, 0, { behavior: "smooth" });
});

const fetchOrderDetails = async () => {
    axios
        .get("/order-details", {
            params: { order_id: route.params.id },
            headers: {
                Authorization: authStore.token,
            },
        })
        .then((response) => {
            order.value = response.data.data.order;
        })
        .catch((error) => {
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
