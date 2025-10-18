<template>
    <section class="carousel-section">
        <div class="carousel-header">
            <h2>Personalized Essentials for You</h2>
            <p class="personalization-note">
                Tailored for a {{ profile.age }} year old,
                {{ profile.healthCondition.toLowerCase() }} mom-to-be in month {{ profile.babyMonth }}.
            </p>
        </div>
        <div class="carousel">
            <button
                type="button"
                class="carousel-control"
                @click="previous"
                aria-label="Previous recommendations"
            >
                ‹
            </button>
            <div class="carousel-track">
                <article v-for="item in visibleRecommendations" :key="item.title" class="carousel-card">
                    <h3>{{ item.title }}</h3>
                    <p class="category">{{ item.category }}</p>
                    <p class="details">{{ item.details }}</p>
                    <button type="button" class="action-button">Add to Plan</button>
                </article>
            </div>
            <button
                type="button"
                class="carousel-control"
                @click="next"
                aria-label="Next recommendations"
            >
                ›
            </button>
        </div>
        <div class="carousel-dots" role="tablist">
            <button
                v-for="(_, index) in totalSlides"
                :key="index"
                type="button"
                class="dot"
                :class="{ active: index === currentSlide }"
                @click="goTo(index)"
                :aria-label="`Go to recommendation set ${index + 1}`"
                role="tab"
                :aria-selected="index === currentSlide"
            ></button>
        </div>
    </section>
</template>

<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    recommendations: {
        type: Array,
        default: () => [],
    },
    profile: {
        type: Object,
        default: () => ({ age: 0, healthCondition: '', babyMonth: 1 }),
    },
});

const visibleCount = 3;
const currentSlide = ref(0);

const totalSlides = computed(() =>
    props.recommendations.length === 0
        ? 0
        : Math.ceil(props.recommendations.length / visibleCount)
);

const visibleRecommendations = computed(() => {
    if (props.recommendations.length === 0) {
        return [];
    }

    const start = currentSlide.value * visibleCount;
    return props.recommendations.slice(start, start + visibleCount);
});

const previous = () => {
    if (totalSlides.value === 0) {
        return;
    }

    currentSlide.value = (currentSlide.value - 1 + totalSlides.value) % totalSlides.value;
};

const next = () => {
    if (totalSlides.value === 0) {
        return;
    }

    currentSlide.value = (currentSlide.value + 1) % totalSlides.value;
};

const goTo = (index) => {
    if (index < 0 || index >= totalSlides.value) {
        return;
    }

    currentSlide.value = index;
};
</script>

<style scoped>
.carousel-section {
    background: linear-gradient(155deg, rgba(254, 249, 195, 0.9), rgba(254, 226, 226, 0.75));
    border-radius: 2rem;
    padding: 2.75rem 2.5rem;
    display: flex;
    flex-direction: column;
    gap: 2rem;
    box-shadow: 0 20px 40px -25px rgba(250, 204, 21, 0.35);
}

.carousel-header h2 {
    font-size: 1.75rem;
    margin: 0 0 0.5rem;
    color: #0f172a;
}

.personalization-note {
    margin: 0;
    color: #334155;
    font-size: 1rem;
}

.carousel {
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 1.25rem;
    align-items: center;
}

.carousel-control {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    border: none;
    background: linear-gradient(135deg, #f472b6, #facc15);
    color: #ffffff;
    font-size: 2rem;
    line-height: 1;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.carousel-control:hover,
.carousel-control:focus {
    transform: translateY(-2px);
    box-shadow: 0 18px 30px -20px rgba(236, 72, 153, 0.65);
}

.carousel-control:focus-visible {
    outline: 3px solid rgba(14, 116, 144, 0.35);
    outline-offset: 4px;
}

.carousel-track {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 1.25rem;
}

.carousel-card {
    background: #ffffff;
    border-radius: 1.5rem;
    padding: 1.75rem;
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
    box-shadow: 0 18px 35px -26px rgba(250, 204, 21, 0.45);
}

.carousel-card h3 {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 700;
    color: #0f172a;
}

.category {
    margin: 0;
    font-size: 0.95rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: rgba(236, 72, 153, 0.7);
}

.details {
    margin: 0;
    color: #475569;
    line-height: 1.6;
}

.action-button {
    margin-top: auto;
    align-self: flex-start;
    padding: 0.75rem 2rem;
    border-radius: 999px;
    border: none;
    background: linear-gradient(135deg, #0ea5e9, #10b981);
    color: #ffffff;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.action-button:hover,
.action-button:focus {
    transform: translateY(-2px);
    box-shadow: 0 15px 25px -18px rgba(14, 165, 233, 0.6);
}

.carousel-dots {
    display: flex;
    justify-content: center;
    gap: 0.75rem;
}

.dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: none;
    background: rgba(15, 23, 42, 0.15);
    cursor: pointer;
    transition: transform 0.2s ease, background-color 0.2s ease;
}

.dot.active {
    transform: scale(1.2);
    background: linear-gradient(135deg, #f472b6, #facc15);
}
</style>
