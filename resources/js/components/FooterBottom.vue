<template>
    <div class="main-container bg-primary-950">
        <!--==== Copyright ====-->
        <div class="py-3 w-full border-t border-primary-900">
            <div class="text-center text-white text-sm font-normal leading-tight">
                © {{ new Date().getFullYear() }} {{ master.footerText || 'All right reserved by company' }}
            </div>
        </div>

    </div>
</template>

<script setup>
import { useMaster } from '../stores/MasterStore';

const master = useMaster();

</script>

