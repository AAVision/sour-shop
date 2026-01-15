<template>
    <div class="mb-12">
        <!-- <div class="main-container py-12"> -->
        <div class="w-full h-[100px] relative bg-primary-900 flex flex-col items-center justify-center">
            <img :src="'/assets/images/flashIncomingLeft.svg'" alt="left" class="absolute bottom-0 left-0" />
            <img :src="'/assets/images/flashIncomingRight.svg'" alt="left" class="absolute bottom-0 right-0" />
            <div class="flex flex-col sm:flex-row justify-center items-center gap-3 z-[4]">
                <span class="text-white text-xl font-bold uppercase">
                    {{ $t("Flash Sale Incoming") }}
                </span>
                <div class="flex justify-center items-center gap-2 text-white">
                    <div v-if="endDay > 0"
                        class="w-10 h-10 sm:w-11 sm:h-11 p-[5.18px] bg-white rounded-md shadow-inner flex-col justify-center items-center flex">
                        <div class="text-center text-primary text-lg font-bold leading-tight">
                            {{ endDay }}
                        </div>
                        <div class="text-center text-[#687387] text-[9px] font-normal leading-none">
                            {{ $t("Days") }}
                        </div>
                    </div>

                    <span v-if="endDay > 0" class="text-white text-2xl font-bold">:</span>
                    <div
                        class="w-10 h-10 sm:w-11 sm:h-11 p-[5.18px] bg-white rounded-md shadow-inner flex-col justify-center items-center flex">
                        <div class="text-center text-primary text-lg font-bold leading-tight">
                            {{ endHour }}
                        </div>
                        <div class="text-center text-[#687387] text-[9px] font-normal leading-none">
                            {{ $t("Hours") }}
                        </div>
                    </div>

                    <span class="text-white text-2xl font-bold">:</span>
                    <div
                        class="w-10 h-10 sm:w-11 sm:h-11 p-[5.18px] bg-white rounded-md shadow-inner flex-col justify-center items-center flex">
                        <div class="text-center text-primary text-lg font-bold leading-tight">
                            {{ endMinute }}
                        </div>
                        <div class="text-center text-[#687387] text-[9px] font-normal leading-none">
                            {{ $t("Minutes") }}
                        </div>
                    </div>

                    <span class="text-white text-2xl font-bold">:</span>
                    <div class=" w-10 h-10 sm:w-11 sm:h-11 p-[5.18px] bg-white rounded-md shadow-inner flex-col justify-center items-center flex">
                        <div class="text-center text-primary text-lg font-bold leading-tight">
                            {{ endSecond }}
                        </div>
                        <div class="text-center text-[#687387] text-[9px] font-normal leading-none">
                            {{ $t("Seconds") }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- </div> -->
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";

const props = defineProps({
    flashSale: Object,
});

const endDay = ref("");
const endHour = ref("");
const endMinute = ref("");
const endSecond = ref("");
let countdownInterval = null;

const startCountdown = () => {
    const endDate = new Date(props.flashSale.end_date).getTime();

    countdownInterval = setInterval(() => {
        const now = new Date().getTime();
        const timeLeft = endDate - now;

        if (timeLeft <= 0) {
            clearInterval(countdownInterval);
            endDay.value = "00";
            endHour.value = "00";
            endMinute.value = "00";
            endSecond.value = "00";
        } else {
            endDay.value = String(Math.floor(timeLeft / (1000 * 60 * 60 * 24))).padStart(2, "0");
            endHour.value = String( Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, "0");
            endMinute.value = String(Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, "0");
            endSecond.value = String(Math.floor((timeLeft % (1000 * 60)) / 1000)).padStart(2, "0");
        }
    }, 1000);
};

onMounted(startCountdown);

onUnmounted(() => {
    clearInterval(countdownInterval);
});
</script>
