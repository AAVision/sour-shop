<template>
    <div class="main-container mt-6 mb-12">

        <div v-if="!isLoading" class="flex justify-between items-center gap-4 flex-wrap">
            <div class="text-slate-950 text-lg  md:text-3xl font-bold leading-9">{{ $t('Categories') }}</div>

            <div class="flex justify-center items-center gap-4">
                <button class="w-11 h-11 rounded-xl justify-center items-center gap-2 flex" @click="swiperPrevSlide">
                    <ChevronLeftIcon class="w-6 h-6 text-slate-600" />
                </button>
                <router-link to="/categories" class="text-slate-600 text-base font-normal leading-normal">
                    {{ $t('View All') }}
                </router-link>
                <button class="w-11 h-11 rounded-xl justify-center items-center gap-2 flex" @click="swiperNextSlide">
                    <ChevronRightIcon class="w-6 h-6 text-slate-600" />
                </button>
            </div>
        </div>
        <!-- loading -->
         <SkeletonLoader v-else class="w-48 sm:w-60 md:w-72 lg:w-96 h-12 rounded-lg" />

        <div class="mt-8" :dir="master.langDirection || 'ltr'">
            <swiper :breakpoints="breakpoints" :loop="false"  ref="swiperRef" @swiper="onSwiper" :modules="[Navigation]">
                <swiper-slide v-if="!isLoading" v-for="category in categories" :key="category.id">
                    <CategoryCard :category="category" />
                </swiper-slide>

                <!-- loading -->
                <swiper-slide v-else v-for="i in 8" :key="i">
                    <SkeletonLoader class="w-full h-[146px] rounded-lg" />
                </swiper-slide>
            </swiper>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation } from 'swiper/modules';
import CategoryCard from './CategoryCard.vue';
import SkeletonLoader from './SkeletonLoader.vue';
import { useMaster } from '../stores/MasterStore';

const master = useMaster();
import 'swiper/css';

const props = defineProps({
    categories: Array,
    isLoading: {
        type: Boolean,
        default: true
    }
});

const swiperInstance = ref()

function onSwiper(swiper) {
    swiperInstance.value = swiper
}

const swiperNextSlide = () => {
    swiperInstance.value.slideNext()
};
const swiperPrevSlide = () => {
    swiperInstance.value.slidePrev()
};

const breakpoints = {
    320: {
        slidesPerView: 2,
        spaceBetween: 10
    },
    768: {
        slidesPerView: 4,
        spaceBetween: 10
    },
    1024: {
        slidesPerView: 6,
        spaceBetween: 30
    },

    1280: {
        slidesPerView: 8,
        spaceBetween: 30
    }
};

</script>

<style lang="scss" scoped></style>
