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
        <OrderDetailsPageHeader :title="$t('Return Product')" />

        <!-- Order details -->
        <div
            class="px-2 pt-2 md:px-4 md:pt-4 lg:px-6 lg:pt-8 max-w-[1280px] mx-auto"
        >
            <div
                class="grid grid-cols-1 lg:grid-cols-2 gap-2 xl:gap-4 md:gap-6 p-3 md:p-4 xl:p-8 bg-white rounded-lg md:rounded-2xl"
            >
                <!-- column 1 -->
                <div class="bg-white">
                    <div
                        class="p-6 rounded-xl outline outline-1 outline-offset-[-1px] outline-slate-100"
                    >
                        <p
                            class="self-stretch justify-start text-slate-600 text-sm font-normal leading-tight pb-3"
                        >
                            Select Return Products ({{
                                order.products?.length
                            }})
                        </p>

                        <div class="space-y-3">
                            <div
                                @click="isProductReturnable(product)"
                                v-for="product in order.products"
                                :key="product.id"
                                class="p-3 gap-4 flex justify-start items-center rounded-lg outline outline-1 outline-offset-[-1px] outline-slate-200 cursor-pointer"
                            >
                                <!-- {{ product.id }} -->
                                <div class="w-16 h-24">
                                    <img
                                        :src="product.thumbnail"
                                        class="w-full h-full object-contain"
                                    />
                                </div>
                                <div
                                    class="space-y-1 grow flex flex-col justify-between"
                                >
                                    <p
                                        class="self-stretch justify-start text-rose-500 text-xs font-normal leading-none"
                                    >
                                        {{ product.brand }}
                                    </p>
                                    <p
                                        class="self-stretch justify-start text-slate-950 text-sm lg:text-base font-normal leading-normal"
                                    >
                                        {{ truncate(product.name, 50) }}
                                    </p>
    
                                    <div
                                        class="flex flex-wrap justify-between items-center gap-3"
                                    >
                                        <!-- Size and color -->
                                        <div
                                            class="flex items-center flex-wrap gap-1"
                                        >
                                            <div
                                            v-if="product.sisdfze"
                                                class="min-w-8 text-center px-2 py-1 bg-slate-100 rounded text-slate-800 text-[10px] md:text-xs font-normal"
                                            >
                                                {{ product.size }}
                                            </div>
                                            <div
                                            v-if="product.color"
                                                class="px-2 py-1 bg-slate-100 rounded text-slate-800 text-[10px] md:text-xs font-normal"
                                            >
                                                {{ product.color }}
                                            </div>
                                        </div>
    
                                        <!-- quantity and price -->
                                        <div
                                            class="text-slate-800 text-base font-normal leading-normal"
                                        >
                                            {{ product.order_qty }} X
                                            {{
                                                master.showCurrency(
                                                    product.discount_price > 0
                                                        ? product.discount_price
                                                        : product.price
                                                )
                                            }}
                                        </div>
                                    </div>
                                </div>
    
                                <div
                                    v-if="product.is_returned"
                                    class="w-0 self-stretch origin-top-left outline outline-1 outline-offset-[-0.50px] outline-slate-200"
                                ></div>
    
                                <div v-if="product.is_returned">
                                    <button
                                        v-if="isNumberInArray(product.id)"
                                        class="w-8 h-8 bg-primary-100 rounded-[50px] flex justify-center items-center"
                                    >
                                        <img
                                            src="../../../public/assets/icons/red-radio-checked.png"
                                            alt=""
                                            class="w-5 h-5"
                                        />
                                    </button>
                                    <button
                                        v-else
                                        class="w-8 h-8 bg-slate-100 rounded-[50px] flex justify-center items-center"
                                    >
                                        <img
                                            src="../../../public/assets/icons/gray-radio.png"
                                            alt=""
                                            class="w-5 h-5"
                                        />
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- column 2 -->
                <div
                    class="p-4 rounded-xl outline outline-1 outline-offset-[-1px] outline-slate-100 space-y-3"
                >
                    <p
                        class="text-zinc-800 text-base lg:text-lg font-medium leading-tight lg:leading-relaxed"
                    >
                        Please fill out the form below to request a return or
                        replacement.
                    </p>

                    <div class="space-y-1 lg:space-y-3 flex flex-col w-full">
                        <label
                            for="account-number text-gray-900 text-sm md:text-base font-normal leading-tight md:leading-snug"
                        >
                            Account Number</label
                        >
                        <div>
                            <input
                                type="text"
                                v-model="returnProductsData.bank_account_number"
                                id="account-number"
                                name="account-number"
                                class="p-2 md:p-4 placeholder:text-neutral-400 rounded-lg md:rounded-xl outline outline-1 outline-offset-[-1.09px] outline-gray-300 w-full"
                                placeholder="Account No"
                            />
                            <p class="text-red-500 text-xs mt-1">
                                {{
                                    returnProductsErrorsData.bank_account_number
                                }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-1 lg:space-y-3 flex flex-col w-full">
                        <label
                            for="location text-gray-900 text-sm md:text-base font-normal leading-tight md:leading-snug"
                        >
                            Collection Location</label
                        >

                        <div>
                            <input
                                type="text"
                                v-model="returnProductsData.return_address"
                                id="location"
                                name="location"
                                class="p-2 md:p-4 placeholder:text-neutral-400 rounded-lg md:rounded-xl outline outline-1 outline-offset-[-1.09px] outline-gray-300 w-full"
                                placeholder="Location"
                            />
                            <p class="text-red-500 text-xs mt-1">
                                {{ returnProductsErrorsData.return_address }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-1 lg:space-y-3 flex flex-col w-full">
                        <label
                            for="reason text-gray-900 text-sm md:text-base font-normal leading-tight md:leading-snug"
                        >
                            Reason for Return

                            <router-link
                                to="/return-policy"
                                class="text-rose-600 text-xs font-normal leading-tight"
                            >
                                (View Our Return Policy)
                            </router-link>
                        </label>

                        <div>
                            <textarea
                                type="text"
                                v-model="returnProductsData.reason"
                                id="reason"
                                name="reason"
                                class="p-2 md:p-4 placeholder:text-neutral-400 rounded-lg md:rounded-xl outline outline-1 outline-offset-[-1.09px] outline-gray-300 w-full"
                                placeholder="Reason"
                                rows="4"
                            />
                            <p class="text-red-500 text-xs">
                                {{ returnProductsErrorsData.reason }}
                            </p>
                        </div>
                    </div>

                    <button
                        @click="sendReturnRequest"
                        class="w-full h-10 md:h-14 md:px-6 md:py-4 bg-rose-500 rounded-[10px] inline-flex justify-center items-center gap-2.5"
                    >
                        <div class="flex justify-center items-center gap-1">
                            <div
                                class="justify-start text-white text-base font-medium font-['Roboto'] leading-normal"
                            >
                                Submit
                            </div>
                        </div>
                    </button>
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
import { useToast } from "vue-toastification";

import { useAuth } from "../stores/AuthStore";
import OrderDetailsPageHeader from "../components/OrderDetailsPageHeader.vue";
import { useMaster } from "../stores/MasterStore";
import {useTruncateText} from '../composables/useTruncateText'
import { computed } from "vue";
import axios from "axios";

// apis
const returnOrderApi = "/return-order";

const master = useMaster();
const authStore = useAuth();
const route = useRoute();
const router = useRouter();
const toast = useToast();
const { truncate } = useTruncateText();

const order = ref({});

const selectedProducts = ref([]);

const returnProductsData = ref({
    order_id: "",
    return_address: "",
    reason: "",
    bank_account_number: "",
});

const returnProductsErrorsData = ref({
    order_id: "",
    return_address: "",
    reason: "",
    bank_account_number: "",
});

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

function isNumberInArray(number) {
    return selectedProducts.value.includes(number);
}

function isProductReturnable(product) {
    if (product.is_returned) {
        if (selectedProducts.value.includes(product.id)) {
            selectedProducts.value = selectedProducts.value.filter(
                (id) => id !== product.id
            );
        } else {
            selectedProducts.value.push(product.id);
        }
    } else {
        toast.error("Product is not returnable", {
            position:
                master.langDirection === "rtl" ? "bottom-right" : "bottom-left",
        });
    }
}

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

const sendReturnRequest = () => {
    clearErrorData();
    returnProductsData.value.order_id = order.value.id;
    console.log(returnProductsData.value);

    const formData = new FormData();

    formData.append("order_id", returnProductsData.value.order_id);
    formData.append("return_address", returnProductsData.value.return_address);
    formData.append("reason", returnProductsData.value.reason);
    formData.append(
        "bank_account_number",
        returnProductsData.value.bank_account_number
    );

    if (selectedProducts.value.length == 0) {
        toast.error("Please select at least one product", {
            position:
                master.langDirection === "rtl" ? "bottom-right" : "bottom-left",
        });
        return;
    }

    selectedProducts.value.forEach((product) => {
        formData.append("product_ids[]", product);
    });

    axios
        .post(returnOrderApi, formData, {
            headers: {
                Authorization: authStore.token,
            },
        })
        .then((response) => {
            console.log(response);
            toast.success(response.data.message, {
                position:
                    master.langDirection === "rtl"
                        ? "bottom-right"
                        : "bottom-left",
            });

            router.push("/returned-order-history");
        })
        .catch((error) => {
            console.log(error.response.data.errors);
            toast.error(error.response.data.message, {
                position:
                    master.langDirection === "rtl"
                        ? "bottom-right"
                        : "bottom-left",
            });
            returnProductsErrorsData.value.bank_account_number = error.response
                .data.errors["bank_account_number"]
                ? error.response.data.errors["bank_account_number"][0]
                : "";

            returnProductsErrorsData.value.return_address = error.response.data
                .errors["return_address"]
                ? error.response.data.errors["return_address"][0]
                : "";

            returnProductsErrorsData.value.reason = error.response.data.errors[
                "reason"
            ]
                ? error.response.data.errors["reason"][0]
                : "";
        });
};

const clearErrorData = () => {
    returnProductsErrorsData.value.bank_account_number = "";
    returnProductsErrorsData.value.return_address = "";
    returnProductsErrorsData.value.reason = "";
};
</script>
