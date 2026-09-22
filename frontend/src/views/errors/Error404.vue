<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
</script>

<template>
    <main class="relative flex justify-center items-center bg-[#070b12] px-6 min-h-screen overflow-hidden text-white">
        <!-- Ambient background -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="top-[20%] left-[10%] absolute bg-emerald-400/60 rounded-full w-2 h-2 animate-pulse" />
            <div class="top-[65%] left-[25%] absolute bg-emerald-300/50 rounded-full w-1.5 h-1.5 animate-pulse" />
            <div class="top-[25%] right-[18%] absolute bg-emerald-400/50 rounded-full w-2 h-2 animate-pulse" />
            <div class="right-[10%] bottom-[20%] absolute bg-emerald-400/30 rounded-full w-3 h-3 animate-pulse" />

            <!-- Glow -->
            <div class="top-[38%] left-1/2 absolute bg-emerald-500/10 blur-[120px] rounded-full w-[500px] h-[500px] -translate-x-1/2" />
        </div>

        <!-- Content -->
        <section class="z-10 relative flex flex-col items-center max-w-xl text-center">

            <!-- Animated robot -->
            <div class="relative mb-8 w-64 h-64">

                <!-- Floating question mark -->
                <div class="top-0 right-8 absolute font-bold text-emerald-400 text-5xl animate-question">
                    ?
                </div>

                <!-- Orbit -->
                <div class="top-1/2 left-1/2 absolute border border-emerald-400/20 rounded-full w-48 h-48 -translate-x-1/2 -translate-y-1/2 animate-orbit" />

                <!-- Robot -->
                <div class="absolute inset-0 flex justify-center items-center animate-float">

                    <div class="relative w-32 h-36">

                        <!-- Antenna -->
                        <div class="-top-5 left-1/2 absolute bg-emerald-400/60 w-px h-6 -translate-x-1/2" />

                        <div class="-top-7 left-1/2 absolute bg-emerald-400 shadow-[0_0_12px_rgba(52,211,153,0.9)] rounded-full w-2 h-2 -translate-x-1/2" />

                        <!-- Head -->
                        <div class="top-2 absolute inset-x-0 bg-gradient-to-b from-slate-200 to-slate-400 shadow-[0_20px_60px_rgba(0,0,0,0.5)] border border-white/20 rounded-[2rem] h-24">
                            <!-- Face -->
                            <div class="absolute inset-3 flex justify-center items-center gap-7 bg-[#071018] shadow-inner rounded-[1.5rem]">
                                <span class="bg-emerald-400 shadow-[0_0_14px_rgba(52,211,153,0.9)] rounded-full w-3 h-5 animate-blink" />
                                <span class="bg-emerald-400 shadow-[0_0_14px_rgba(52,211,153,0.9)] rounded-full w-3 h-5 animate-blink" />
                            </div>

                            <!-- Ear -->
                            <div class="top-8 -left-4 absolute bg-slate-400 border border-white/20 rounded-full w-7 h-10">
                                <div class="absolute inset-2 border-2 border-emerald-400/70 rounded-full" />
                            </div>
                        </div>

                        <!-- Body -->
                        <div class="right-4 bottom-0 left-4 absolute bg-gradient-to-b from-slate-300 to-slate-500 border border-white/10 rounded-2xl h-14">
                            <div class="top-3 left-1/2 absolute bg-emerald-400/80 rounded-full w-8 h-2 -translate-x-1/2" />
                        </div>

                        <!-- Arms -->
                        <div class="top-20 -left-5 absolute bg-slate-300 rounded-full w-10 h-8 -rotate-[35deg]" />

                        <div class="top-20 -right-5 absolute bg-slate-300 rounded-full w-10 h-8 rotate-[35deg]" />

                        <!-- Legs -->
                        <div class="bottom-[-12px] left-8 absolute bg-slate-400 rounded-full w-6 h-7 rotate-[15deg]" />

                        <div class="right-8 bottom-[-12px] absolute bg-slate-400 rounded-full w-6 h-7 -rotate-[15deg]" />
                    </div>
                </div>

                <!-- Floating particles -->
                <span class="top-24 left-3 absolute bg-slate-500/60 rounded-full w-3 h-3 animate-particle-1" />
                <span class="top-36 right-5 absolute bg-slate-600/60 rounded-full w-4 h-4 animate-particle-2" />
                <span class="bottom-10 left-12 absolute bg-emerald-400/70 rounded-full w-2 h-2 animate-particle-3" />

                <!-- Platform -->
                <div class="bottom-2 left-1/2 absolute bg-emerald-400/10 shadow-[0_0_30px_rgba(52,211,153,0.35)] border border-emerald-400/60 rounded-[50%] w-36 h-4 -translate-x-1/2 animate-platform" />
            </div>

            <!-- Error badge -->
            <div class="flex items-center gap-2 bg-emerald-400/5 mb-5 px-4 py-1.5 border border-emerald-400/10 rounded-full font-medium text-emerald-300 text-xs tracking-[0.2em]">
                <span class="bg-emerald-400 shadow-[0_0_10px_rgba(52,211,153,0.8)] rounded-full w-2 h-2" />
                {{ t('errors.404.code') }}
            </div>

            <!-- Heading -->
            <h1 class="font-bold text-4xl sm:text-5xl tracking-tight">
                {{ t('errors.404.titlePrefix') }}
                <span class="text-emerald-400">
                    {{ t('errors.404.titleHighlight') }}
                </span>
                {{ t('errors.404.titleSuffix') }}
            </h1>

            <!-- Description -->
            <p class="mt-4 max-w-md text-slate-400 text-sm sm:text-base leading-6">
                {{ t('errors.404.description') }}
            </p>

            <!-- Button -->
            <RouterLink to="/" class="group inline-flex items-center gap-4 bg-emerald-400 hover:bg-emerald-300 shadow-[0_0_30px_rgba(52,211,153,0.2)] hover:shadow-[0_0_40px_rgba(52,211,153,0.35)] mt-8 px-6 py-3.5 rounded-xl font-medium text-slate-950 transition-all hover:-translate-y-0.5 duration-300">
                <!-- Home icon -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5">
                    <path d="m3 10 9-7 9 7" />
                    <path d="M5 9v11h14V9" />
                    <path d="M9 20v-6h6v6" />
                </svg>

                <span>
                    {{ t('errors.404.back') }}
                </span>

                <!-- Arrow -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5 transition-transform group-hover:translate-x-1 duration-300">
                    <path d="M5 12h14" />
                    <path d="m13 6 6 6-6 6" />
                </svg>
            </RouterLink>
        </section>
    </main>
</template>

<style scoped>
/* Robot floating */
@keyframes float {

    0%,
    100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-12px);
    }
}

.animate-float {
    animation: float 4s ease-in-out infinite;
}

/* Question mark */
@keyframes question {

    0%,
    100% {
        transform: rotate(-8deg) translateY(0);
    }

    50% {
        transform: rotate(8deg) translateY(-8px);
    }
}

.animate-question {
    animation: question 2.5s ease-in-out infinite;
}

/* Orbit */
@keyframes orbit {
    from {
        transform: translate(-50%, -50%) rotate(0deg);
    }

    to {
        transform: translate(-50%, -50%) rotate(360deg);
    }
}

.animate-orbit {
    animation: orbit 12s linear infinite;
}

/* Robot eyes */
@keyframes blink {

    0%,
    45%,
    55%,
    100% {
        transform: scaleY(1);
    }

    50% {
        transform: scaleY(0.1);
    }
}

.animate-blink {
    animation: blink 4s ease-in-out infinite;
}

/* Floating particles */
@keyframes particle-1 {

    0%,
    100% {
        transform: translate(0, 0);
        opacity: 0.3;
    }

    50% {
        transform: translate(15px, -20px);
        opacity: 0.8;
    }
}

@keyframes particle-2 {

    0%,
    100% {
        transform: translate(0, 0);
        opacity: 0.4;
    }

    50% {
        transform: translate(-18px, 15px);
        opacity: 0.9;
    }
}

@keyframes particle-3 {

    0%,
    100% {
        transform: translate(0, 0);
        opacity: 0.4;
    }

    50% {
        transform: translate(10px, -18px);
        opacity: 1;
    }
}

.animate-particle-1 {
    animation: particle-1 4s ease-in-out infinite;
}

.animate-particle-2 {
    animation: particle-2 5s ease-in-out infinite;
}

.animate-particle-3 {
    animation: particle-3 3s ease-in-out infinite;
}

/* Platform pulse */
@keyframes platform {

    0%,
    100% {
        transform: translateX(-50%) scaleX(1);
        opacity: 0.5;
    }

    50% {
        transform: translateX(-50%) scaleX(1.15);
        opacity: 1;
    }
}

.animate-platform {
    animation: platform 2.5s ease-in-out infinite;
}

/* Accessibility */
@media (prefers-reduced-motion: reduce) {

    .animate-float,
    .animate-question,
    .animate-orbit,
    .animate-blink,
    .animate-particle-1,
    .animate-particle-2,
    .animate-particle-3,
    .animate-platform {
        animation: none;
    }
}
</style>
