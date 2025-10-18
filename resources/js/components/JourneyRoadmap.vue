<template>
    <section class="journey-roadmap">
        <h2>Pregnancy Journey Highlights</h2>
        <p class="timeline-intro" v-if="gestationCopy">{{ gestationCopy }}</p>
        <div class="timeline">
            <div v-for="event in timeline" :key="event.title" class="timeline-event">
                <div class="event-header">
                    <span class="event-trimester">{{ event.window }}</span>
                    <h3>{{ event.title }}</h3>
                </div>
                <p>{{ event.description }}</p>
            </div>
        </div>
    </section>
</template>

<script setup>
const props = defineProps({
    timeline: {
        type: Array,
        default: () => [],
    },
    gestationCopy: {
        type: String,
        default: '',
    },
});
</script>

<style scoped>
.journey-roadmap {
    background: linear-gradient(170deg, rgba(240, 249, 255, 0.9), rgba(219, 234, 254, 0.6));
    border-radius: 2rem;
    padding: 2.75rem 2.5rem;
    display: flex;
    flex-direction: column;
    gap: 2rem;
    box-shadow: 0 20px 40px -25px rgba(14, 165, 233, 0.35);
}

.journey-roadmap h2 {
    font-size: 1.75rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}

.timeline-intro {
    font-size: 1rem;
    color: #1f2937;
    margin: 0;
}

.timeline {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.75rem;
}

.timeline-event {
    background: #ffffff;
    border-radius: 1.5rem;
    padding: 1.75rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    box-shadow: 0 18px 35px -26px rgba(14, 116, 144, 0.4);
}

.event-header {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.event-trimester {
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: rgba(14, 116, 144, 0.7);
}

.event-header h3 {
    font-size: 1.2rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}

.timeline-event p {
    margin: 0;
    color: #475569;
    line-height: 1.6;
}
</style>
