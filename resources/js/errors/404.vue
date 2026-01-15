<template>
    <main class="flex justify-center items-center">
        <section>
            <div class="image">
                <img src="/public/assets/images/dino-404.svg" alt="">
            </div>
            <div class="details">
                <p class="title">Page Not Found</p>
                <p class="description">The page you’re looking for might have been moved, deleted, or does not exist.
                    Please check the URL or return to the homepage.
                    <br>
                </p>
            </div>
            <a href="/" class="reload_btn_404" style="text-decoration: none">
                Return to Homepage
            </a>
        </section>
    </main>
</template>

<style scoped>
body {
    font-family: 'Poppins', sans-serif;
    margin: 0px;
    min-height: 100vh;
    max-height: 100vh;
}

main {
    min-height: 100vh;
    max-height: 100vh;
}

main>section {
    padding: 32px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 24px;
    max-width: 793px;
    margin: 0 auto;
    /* Center the section */
}

.details {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 28px;
}

.image>img {
    max-width: 347px;
    width: 100%;
    /* Ensure image scales with container */
}

.title {
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
    font-style: normal;
    /* Corrected font-style */
    font-size: 40px;
    line-height: 8px;
    letter-spacing: 0;
    text-align: center;
}

.description {
    font-family: 'Poppins', sans-serif;
    font-weight: 400;
    font-style: normal;
    /* Corrected font-style */
    font-size: 18px;
    line-height: 26px;
    letter-spacing: 0;
    text-align: center;
}

.description>span {
    color: var(--primary);
}

.reload_btn_404 {
    background: #ee456b;
    padding: 12px 40px;
    font-size: 20px;
    font-style: normal;
    font-weight: 600;
    line-height: 28px;
    border-radius: 12px;
    border: none;
    color: white;
    cursor: pointer;
    /* Improve UX for button */
    min-width: 200px;
}

.h-full {
    height: 100%;
}

.flex {
    display: flex;
}

.justify-center {
    justify-content: center;
}

.items-center {
    align-items: center;
}

.bg-primary {
    background-color: var(--primary);
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
    main>section {
        padding: 16px;
        /* Reduced padding for mobile */
        gap: 16px;
        /* Smaller gap between elements */
    }

    .image>img {
        max-width: 80vw;
        /* Image scales to 80% of viewport width */
    }

    .title {
        font-size: 28px;
        /* Smaller font size */
        line-height: 34px;
    }

    .description {
        font-size: 16px;
        /* Smaller font size */
        line-height: 22px;
    }

    .reload_btn_404 {
        padding: 10px 24px;
        /* Smaller padding */
        font-size: 16px;
        /* Smaller font size */
        line-height: 24px;
    }
}

@media (max-width: 480px) {
    .image>img {
        max-width: 90vw;
        /* Further adjust for very small screens */
    }

    .title {
        font-size: 24px;
        /* Even smaller for tiny screens */
        line-height: 30px;
    }

    .description {
        font-size: 14px;
        line-height: 20px;
    }

    .reload_btn_404 {
        padding: 8px 20px;
        font-size: 14px;
        line-height: 20px;
    }
}



@media (max-width: 320px) {
    main>section {
        padding: 16px;
        /* Reduced padding for mobile */
        gap: 16px;
        /* Smaller gap between elements */
    }

    .image>img {
        max-width: 90vw;
        /* Further adjust for very small screens */
    }

    .title {
        font-size: 20px;
        /* Even smaller for tiny screens */
        line-height: 28px;
        font-weight: 600;
        margin: 0px;
    }

    .description {
        font-size: 14px;
        line-height: 22px;
        margin: 0px;
        margin-top: 8px;
    }

    .reload_btn_404 {
        padding: 8px 20px;
        font-size: 14px;
        line-height: 20px;
    }
}
</style>


<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

function goHome() {
    router.push('/')
}
</script>
