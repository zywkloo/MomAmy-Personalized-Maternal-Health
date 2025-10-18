<template>
    <div class="conversation-card">
        <p class="question-progress">Question {{ currentStep }} of {{ totalSteps }}</p>
        <p class="greeting" :aria-live="isTyping ? 'off' : 'polite'">{{ displayedQuestion }}</p>
        <div class="response-area">
            <template v-if="question && question.type === 'choice'">
                <button
                    v-for="option in question.options"
                    :key="option.value"
                    type="button"
                    class="choice-button"
                    @click="$emit('select-option', option.value)"
                >
                    {{ option.label }}
                </button>
            </template>
            <template v-else-if="question && question.type === 'date'">
                <label class="visually-hidden" :for="question.id">{{ question.label }}</label>
                <input
                    :id="question.id"
                    type="date"
                    class="date-input"
                    :value="currentAnswer"
                    :min="question.min"
                    :max="question.max"
                    :aria-describedby="currentQuestionError ? `${question.id}-error` : undefined"
                    @input="onInput"
                    @keyup.enter="$emit('proceed-date')"
                />
                <button
                    type="button"
                    class="cta-button"
                    :disabled="!canProceed"
                    @click="$emit('proceed-date')"
                >
                    Continue
                </button>
                <p v-if="currentQuestionError" :id="`${question.id}-error`" class="input-error">
                    {{ currentQuestionError }}
                </p>
            </template>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    question: {
        type: Object,
        default: null,
    },
    currentStep: {
        type: Number,
        required: true,
    },
    totalSteps: {
        type: Number,
        required: true,
    },
    displayedQuestion: {
        type: String,
        default: '',
    },
    isTyping: {
        type: Boolean,
        default: false,
    },
    answers: {
        type: Object,
        required: true,
    },
    currentQuestionError: {
        type: String,
        default: '',
    },
    canProceed: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['select-option', 'update-answer', 'proceed-date']);

const currentAnswer = computed(() => {
    if (!props.question || props.question.type !== 'date') {
        return '';
    }

    return props.answers[props.question.id] || '';
});

const onInput = (event) => {
    if (!props.question) {
        return;
    }

    emit('update-answer', props.question.id, event.target.value);
};
</script>

<style scoped>
.conversation-card {
    background-color: rgba(255, 255, 255, 0.95);
    border-radius: 1.75rem;
    padding: 2.75rem 2.25rem;
    box-shadow: 0 15px 35px -20px rgba(14, 116, 144, 0.35);
    display: flex;
    flex-direction: column;
    gap: 1.75rem;
}

.question-progress {
    font-size: 0.95rem;
    letter-spacing: 0.05em;
    color: #f472b6;
    text-transform: uppercase;
}

.greeting {
    font-size: 1.55rem;
    font-weight: 600;
    color: #1f2937;
    min-height: 4.5rem;
    line-height: 1.6;
    position: relative;
}

.response-area {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    align-items: center;
}

.choice-button {
    flex: 1 1 220px;
    padding: 0.95rem 1.5rem;
    border-radius: 999px;
    border: none;
    background: linear-gradient(135deg, #f472b6, #facc15);
    color: #ffffff;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.choice-button:hover,
.choice-button:focus {
    transform: translateY(-2px);
    box-shadow: 0 15px 25px -18px rgba(236, 72, 153, 0.7);
}

.choice-button:focus-visible {
    outline: 3px solid rgba(14, 116, 144, 0.3);
    outline-offset: 4px;
}

.date-input {
    flex: 1 0 200px;
    padding: 0.85rem 1rem;
    border-radius: 0.85rem;
    border: 1px solid rgba(15, 118, 110, 0.25);
    background: rgba(236, 254, 255, 0.4);
    color: #0f172a;
    font-size: 1rem;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.date-input:focus {
    outline: none;
    border-color: rgba(14, 116, 144, 0.7);
    box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.2);
}

.cta-button {
    flex: 0 0 auto;
    padding: 0.9rem 2.5rem;
    border-radius: 999px;
    border: none;
    background: linear-gradient(135deg, #0ea5e9, #10b981);
    color: #ffffff;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.cta-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.cta-button:not(:disabled):hover,
.cta-button:not(:disabled):focus {
    transform: translateY(-2px);
    box-shadow: 0 15px 25px -18px rgba(14, 165, 233, 0.7);
}

.input-error {
    flex-basis: 100%;
    color: #dc2626;
    font-size: 0.9rem;
}

.visually-hidden {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}
</style>
