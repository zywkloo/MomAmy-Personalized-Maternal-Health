<template>
    <aside class="info-panel">
        <p class="greeting">Lovely, here’s what I’ve prepared for you.</p>
        <ul class="summary-list">
            <li>
                <span class="summary-label">Pregnancy shared:</span>
                <span class="summary-value">{{ friendlyPregnancyInfo }}</span>
            </li>
            <li v-if="answers.lastPeriod">
                <span class="summary-label">Last period started:</span>
                <span class="summary-value">{{ formattedLastPeriod }}</span>
            </li>
            <li v-if="estimatedDueDate">
                <span class="summary-label">Estimated due date:</span>
                <span class="summary-value">{{ estimatedDueDate }}</span>
            </li>
            <li v-if="answers.dob">
                <span class="summary-label">Your age:</span>
                <span class="summary-value">{{ profileAge }} years</span>
            </li>
            <li v-if="answers.pregnancyOrder">
                <span class="summary-label">Pregnancy number:</span>
                <span class="summary-value">{{ pregnancyOrderCopy }}</span>
            </li>
            <li v-if="answers.companion">
                <span class="summary-label">Support circle:</span>
                <span class="summary-value">{{ companionCopy }}</span>
            </li>
        </ul>
    </aside>
</template>

<script setup>
const props = defineProps({
    answers: {
        type: Object,
        required: true,
    },
    friendlyPregnancyInfo: {
        type: String,
        default: '',
    },
    formattedLastPeriod: {
        type: String,
        default: '',
    },
    estimatedDueDate: {
        type: String,
        default: '',
    },
    profileAge: {
        type: Number,
        default: 0,
    },
    pregnancyOrderCopy: {
        type: String,
        default: '',
    },
    companionCopy: {
        type: String,
        default: '',
    },
});
</script>

<style scoped>
.info-panel {
    background: linear-gradient(160deg, rgba(255, 255, 255, 0.95), rgba(236, 254, 255, 0.8));
    border-radius: 2rem;
    box-shadow: 0 20px 35px -25px rgba(14, 116, 144, 0.35);
    padding: 2.75rem 2.5rem;
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.greeting {
    font-size: 1.5rem;
    font-weight: 700;
    color: #0f172a;
}

.summary-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.summary-list li {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.summary-label {
    font-size: 0.95rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: rgba(15, 118, 110, 0.75);
}

.summary-value {
    font-size: 1.2rem;
    font-weight: 600;
    color: #1f2937;
}
</style>
