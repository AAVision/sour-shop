<template>
    <div class="space-y-2 divide-y divide-slate-200">
        <!-- item 1-->
        <div
            v-for="product in order.products"
            :key="product.id"
            class="flex gap-4 justify-start w-full items-center pt-2"
        >
            <div class="w-[72px] h-[72px]">
                <img
                    :src="product.thumbnail"
                    class="w-full h-full object-contain rounded-lg"
                />
            </div>
            <div class="flex flex-col gap-1 w-full relative">
                <!-- Brand -->
                <div class="text-primary text-xs font-normal leading-none">
                    {{ product.brand }}
                </div>

                <div class="flex justify-between items-center gap-2 mb-3">
                    <!-- Product Name -->
                    <div
                        class="text-slate-950 text-base font-normal leading-normal"
                    >
                        {{ truncate(product.name, 50) }}
                    </div>

                    <!-- Rating -->
                        <div class="flex gap-2">
                            <div v-show="order.order_status === 'Delivered'">
                                <button
                                    v-if="!product.rating"
                                    class="px-4 py-1 w-full sm:w-20 bg-amber-50 border border-amber-400 text-amber-500 rounded-md md:rounded-lg text-xs font-normal"
                                    @click="showRating(product.id)"
                                >
                                    {{ $t("Review") }}
                                </button>

                                <div v-else class="flex items-center gap-0">
                                    <span v-for="i in 5" :key="i">
                                        <StarIcon
                                            class="w-5 h-5"
                                            :class="
                                                i <= product.rating
                                                    ? 'text-amber-500'
                                                    : 'text-slate-200'
                                            "
                                        />
                                    </span>
                                </div>
                            </div>
                            <div v-if="product.is_digital == true && order.payment_status === 'Paid'">
                                <div class="relative">
                                    <button
                                        type="button"
                                        class="flex items-center justify-between w-full sm:w-24 px-2 py-1 bg-red-50 border border-red-400 rounded-lg text-red-500 text-[10px] font-medium hover:bg-white hover:text-red-500 transition"
                                        @click="toggleDropdown(product.id)"
                                    >
                                        <span class="flex flex-wrap justify-start items-center gap-1">
                                            <ArrowDownTrayIcon class="w-3 h-3" />
                                            {{ $t("Download") }}
                                        </span>
                                        <svg
                                            :class="[
                                                'w-3 h-3 ml-1 transition-transform duration-300',
                                                dropdownOpen == product.id ? 'rotate-180' : ''
                                            ]"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            viewBox="0 0 24 24"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <div
                                        v-if="dropdownOpen == product.id"
                                        class="absolute right-0 z-10 mt-1 w-32 bg-white border border-red-400 rounded-lg shadow-lg"
                                    >
                                        <ul class="p-2 text-[10px] text-slate-700">
                                            <li v-for="attachment in product.attachments">
                                                <a :href="attachment.url"
                                                    download
                                                    class="flex items-center justify-start gap-1 py-2 px-2 hover:bg-red-100 transition rounded-t-lg uppercase"
                                                >
                                                    <FolderIcon class="w-3 h-3" />
                                                    {{ attachment.extension }}
                                                </a>
                                            </li>
                                            <li>
                                                <a :href="product.license_download_link"
                                                    class="flex items-center justify-start gap-1 py-2 px-2 hover:bg-red-100 transition rounded-b-lg"
                                                >
                                                    <ClipboardDocumentListIcon class="w-3 h-3" />
                                                    {{ $t("License") }}
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <div class="flex flex-wrap items-center gap-3" :class="product.is_digital != true ? 'justify-between' : 'justify-end'">
                    <!-- Size and color -->
                    <div class="flex items-center gap-1" v-if="product.is_digital != true">
                        <div
                            class="min-w-8 text-center px-2 py-1 bg-slate-100 rounded text-slate-800 text-xs font-normal"
                        >
                            {{ product.size }}
                        </div>
                        <div
                            class="px-2 py-1 bg-slate-100 rounded text-slate-800 text-xs font-normal"
                        >
                            {{ product.color }}
                        </div>
                    </div>
                    <!-- quantity and price -->
                    <div class="text-slate-800 text-base font-normal leading-normal">
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

                <!--  -->
            </div>
        </div>
    </div>

    <!-- Rating Modal -->
    <TransitionRoot as="template" :show="showRatingModal">
        <Dialog as="div" class="relative z-10" @close="showRatingModal = false">
            <TransitionChild
                as="template"
                enter="ease-out duration-300"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="ease-in duration-200"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div
                    class="fixed inset-0 bg-gray-500 bg-opacity-50 transition-opacity"
                />
            </TransitionChild>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div
                    class="flex min-h-full items-center justify-center p-4 text-center sm:p-0"
                >
                    <TransitionChild
                        as="template"
                        enter="ease-out duration-300"
                        enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to="opacity-100 translate-y-0 sm:scale-100"
                        leave="ease-in duration-200"
                        leave-from="opacity-100 translate-y-0 sm:scale-100"
                        leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    >
                        <DialogPanel
                            class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all my-8 md:my-0 w-full sm:max-w-lg md:max-w-xl"
                        >
                            <div class="bg-white p-5 sm:p-8 relative">
                                <!-- close button -->
                                <div
                                    class="w-9 h-9 bg-slate-100 rounded-[32px] absolute top-4 right-4 flex justify-center items-center cursor-pointer"
                                    @click="showRatingModal = false"
                                >
                                    <XMarkIcon class="w-6 h-6 text-slate-600" />
                                </div>
                                <!-- end close button -->

                                <h2
                                    class="text-xl font-semibold text-gray-800 mb-4"
                                >
                                    {{ $t("Rate Your Experience") }}
                                </h2>

                                <div
                                    class="flex justify-center items-center gap-3 mb-4"
                                >
                                    <!-- Star Rating -->
                                    <Vue3StarRatings
                                        v-model="rating"
                                        :star-size="42"
                                        inactiveColor="#cbd5e1"
                                    />
                                    <div class="text-gray-600 text-lg">
                                        <span>{{ $t("Rating") }}:</span>
                                        {{ rating }}
                                    </div>
                                </div>
                                <div>
                                    <label for="description">{{ $t('Message') }}</label>
                                    <textarea
                                        v-model="description"
                                        class="border border-gray-300 p-2 w-full rounded focus:border-primary outline-none"
                                        rows="4"
                                        :placeholder="
                                            $t('Enter your description')
                                        "
                                    ></textarea>
                                </div>

                                <div class="flex justify-end space-x-3 mt-4">
                                    <button
                                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3.5 px-4 rounded-lg focus:outline-none flex-grow"
                                        @click="showRatingModal = false"
                                    >
                                        {{ $t("Cancel") }}
                                    </button>
                                    <button
                                        class="bg-primary hover:bg-primary-600 text-white font-semibold py-3.5 px-4 rounded-lg focus:outline-none flex-grow"
                                        @click="submitRating()"
                                    >
                                        {{ $t("Submit") }}
                                    </button>
                                </div>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>
import {
    Dialog,
    DialogPanel,
    TransitionChild,
    TransitionRoot,
} from "@headlessui/vue";
import { XMarkIcon } from "@heroicons/vue/24/outline";
import { StarIcon } from "@heroicons/vue/24/solid";
import { ArrowDownTrayIcon  } from "@heroicons/vue/20/solid";
import { FolderIcon } from "@heroicons/vue/20/solid";
import { ClipboardDocumentListIcon } from "@heroicons/vue/20/solid";

import { ref } from "vue";
import { useToast } from "vue-toastification";
import Vue3StarRatings from "vue3-star-ratings";
import { useAuth } from "../stores/AuthStore";
import { useMaster } from "../stores/MasterStore";
import { useTruncateText } from "../composables/useTruncateText";
import axios from "axios";

const toast = useToast();
const authStore = useAuth();
const { truncate } = useTruncateText();

const props = defineProps({
    order: Object,
    default: () => {},
});

const emit = defineEmits(["refresh"]);

const master = useMaster();
const rating = ref(0);
const description = ref("");
const showRatingModal = ref(false);
const productID = ref("");

const showRating = (id) => {
    productID.value = id;
    rating.value = 0;
    description.value = "";
    showRatingModal.value = true;
};

const submitRating = () => {
    if (rating.value == 0) {
        toast.error("Please select rating", {
            position:
                master.langDirection === "rtl" ? "bottom-right" : "bottom-left",
        });
        return false;
    } else if (description.value == "") {
        toast.error("Please enter message", {
            position:
                master.langDirection === "rtl" ? "bottom-right" : "bottom-left",
        });
        return false;
    }
    axios
        .post(
            "/product-review",
            {
                rating: rating.value,
                description: description.value,
                order_id: props.order.id,
                product_id: productID.value,
            },
            { headers: { Authorization: `${authStore.token}` } }
        )
        .then((response) => {
            toast.success(response.data.message, {
                position:
                    master.langDirection === "rtl"
                        ? "bottom-right"
                        : "bottom-left",
            });
            showRatingModal.value = false;
            emit("refresh");
        })
        .catch((error) => {
            toast.error(error.response.data.message, {
                position:
                    master.langDirection === "rtl"
                        ? "bottom-right"
                        : "bottom-left",
            });
        });
};

const dropdownOpen = ref(null);

function toggleDropdown(id) {
    dropdownOpen.value = dropdownOpen.value == id ? null : id;
}

</script>
