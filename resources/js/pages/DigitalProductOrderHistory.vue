<template>
    <div>
        <!-- Header -->
        <AuthPageHeader title="My Digital Products" />

                <!-- Order History -->
        <div class="px-2 pt-2 md:px-4 md:pt-4 lg:px-6 lg:pt-6">
            <div class="p-4 lg:p-6 bg-white rounded-xl flex flex-col gap-3">

                <!-- Order Item -->
                <div v-for="order in orders" :key="order.id">
                    <DigitalProductOrderItem :order="order" />
                </div>

                <!-- Order list empty -->
                <div v-if="orders.length == 0">
                    <p>{{ $t('No Order Found') }}</p>
                </div>

            </div>
        </div>

        <!-- pagination's -->
        <div v-if="totalItems > perPage" class="px-2 md:px-4 lg:px-6 mt-4">
            <div class="bg-white p-3 rounded-xl flex justify-between items-center w-full  gap-4 flex-wrap">
                <div class="text-slate-800 text-base font-normal leading-normal">
                    {{ $t('Showing') }} {{ perPage * (currentPage - 1) + 1 }} {{ $t('to') }} {{ perPage * (currentPage - 1) +
                    orders.length }} {{ $t('of') }} {{ totalItems }} {{ $t('results') }}
                </div>
                <div>

                    <vue-awesome-paginate :total-items="totalItems" :items-per-page="perPage" type="button"
                        :max-pages-shown="3" v-model="currentPage" :hide-prev-next-when-ends="true" @click="onClickHandler" />
                </div>
            </div>
        </div>


    </div>
</template>


<script setup>
import { onMounted, ref, watch } from 'vue';
import AuthPageHeader from '../components/AuthPageHeader.vue';
import DigitalProductOrderItem from '../components/DigitalProductOrderItem.vue';

import { useRouter } from 'vue-router';
import { useAuth } from '../stores/AuthStore';

const router = useRouter();
const authStore = useAuth();
const orderStatus = ref('digital');

const orders = ref([]);

const totalItems = ref(20);
const currentPage = ref(1);
const perPage = ref(10);

const onClickHandler = (page) => {
    currentPage.value = page;
    fetchOrders();
};

watch(orderStatus, () => {
    currentPage.value = 1;
    fetchOrders();
});

onMounted(() => {
    fetchOrders();
});

const fetchOrders = async () => {
    axios.get('/orders', {
        params: {
            order_status: orderStatus.value,
            page: currentPage.value,
            per_page: perPage.value
        },
        headers: {
            Authorization: authStore.token,
        }
    }).then((response) => {
        totalItems.value = response.data.data.total;
        orders.value = response.data.data.orders;
    }).catch((error) => {
        if (error.response.status === 401) {
            authStore.token = null;
            authStore.user = null;
            authStore.addresses = [];
            authStore.favoriteProducts = 0;
            router.push('/');
        }
    });
};

</script>
