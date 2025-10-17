<template>
    <div class="mama-amy-dashboard">
        <section class="hero" :class="{ 'conversation-mode': !conversationComplete }">
            <div class="hero-content">
                <div class="avatar-card">
                    <div class="animated-avatar" role="img" aria-label="Animated portrait of Mama Amy offering guidance">
                        <div class="avatar-glow"></div>
                        <div class="avatar-body">
                            <div class="avatar-face">
                                <span class="avatar-eye left"></span>
                                <span class="avatar-eye right"></span>
                                <span class="avatar-smile"></span>
                            </div>
                            <div class="avatar-heart"></div>
                        </div>
                    </div>
                    <p class="avatar-message">
                        {{
                            conversationComplete
                                ? 'Thank you for sharing your story. I have a journey and essentials curated just for you.'
                                : 'I am right beside you—let’s walk through a few gentle questions together.'
                        }}
                    </p>
                </div>

                <div v-if="!conversationComplete" class="conversation-card">
                    <p class="question-progress">Question {{ currentStep }} of {{ totalSteps }}</p>
                    <p class="greeting" :aria-live="isTyping ? 'off' : 'polite'">{{ displayedQuestion }}</p>
                    <div class="response-area">
                        <template v-if="currentQuestion && currentQuestion.type === 'choice'">
                            <button
                                v-for="option in currentQuestion.options"
                                :key="option.value"
                                type="button"
                                class="choice-button"
                                @click="selectOption(option.value)"
                            >
                                {{ option.label }}
                            </button>
                        </template>
                        <template v-else-if="currentQuestion && currentQuestion.type === 'date'">
                            <label class="visually-hidden" :for="currentQuestion.id">{{ currentQuestion.label }}</label>
                            <input
                                :id="currentQuestion.id"
                                type="date"
                                class="date-input"
                                v-model="answers[currentQuestion.id]"
                                :min="currentQuestion.min"
                                :max="currentQuestion.max"
                                :aria-describedby="currentQuestionError ? `${currentQuestion.id}-error` : undefined"
                                @keyup.enter="proceedFromDateQuestion"
                            />
                            <button
                                type="button"
                                class="cta-button"
                                :disabled="!canProceed"
                                @click="proceedFromDateQuestion"
                            >
                                Continue
                            </button>
                            <p v-if="currentQuestionError" :id="`${currentQuestion.id}-error`" class="input-error">
                                {{ currentQuestionError }}
                            </p>
                        </template>
                    </div>
                </div>

                <div v-else class="welcome-card">
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
                            <span class="summary-value">{{ profile.age }} years</span>
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
                </div>
            </div>
        </section>

        <section v-if="conversationComplete" class="timeline-section">
            <h2>Pregnancy Journey Highlights</h2>
            <p class="timeline-intro" v-if="gestationCopy">{{ gestationCopy }}</p>
            <div class="timeline">
                <div
                    v-for="event in timeline"
                    :key="event.title"
                    class="timeline-event"
                >
                    <div class="event-header">
                        <span class="event-trimester">{{ event.window }}</span>
                        <h3>{{ event.title }}</h3>
                    </div>
                    <p>{{ event.description }}</p>
                </div>
            </div>
        </section>

        <section v-if="conversationComplete" class="carousel-section">
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
                    <article
                        v-for="item in visibleRecommendations"
                        :key="item.title"
                        class="carousel-card"
                    >
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
    </div>
</template>

<script setup>
import { computed, nextTick, onMounted, reactive, ref, watch } from 'vue';

const answers = reactive({
    pregnancyInfo: '',
    lastPeriod: '',
    dob: '',
    pregnancyOrder: '',
    companion: '',
});

const today = new Date();

const formatDate = (date) => date.toISOString().split('T')[0];
const shiftDays = (date, amount) => {
    const shifted = new Date(date);
    shifted.setDate(shifted.getDate() + amount);
    return shifted;
};
const shiftYears = (date, amount) => {
    const shifted = new Date(date);
    shifted.setFullYear(shifted.getFullYear() - amount);
    return shifted;
};
const formatDisplayDate = (value) => {
    if (!value) {
        return '';
    }
    return new Date(value).toLocaleDateString(undefined, {
        month: 'long',
        day: 'numeric',
        year: 'numeric',
    });
};

const lastPeriodMin = formatDate(shiftDays(today, -365));
const lastPeriodMax = formatDate(today);
const dobMin = formatDate(shiftYears(today, 55));
const dobMax = formatDate(shiftYears(today, 13));

const questions = [
    {
        id: 'pregnancyInfo',
        text: 'Sweetheart, would you like to share a few pregnancy details with me today?',
        type: 'choice',
        options: [
            { value: 'yes', label: 'Yes, I’d love to' },
            { value: 'no', label: 'Maybe later' },
        ],
    },
    {
        id: 'lastPeriod',
        text: 'Thank you. When did your most recent period begin, love?',
        type: 'date',
        min: lastPeriodMin,
        max: lastPeriodMax,
        label: 'Last period start date',
    },
    {
        id: 'dob',
        text: 'And when is your birthday so I can care for you at every stage?',
        type: 'date',
        min: dobMin,
        max: dobMax,
        label: 'Your date of birth',
    },
    {
        id: 'pregnancyOrder',
        text: 'Is this your first pregnancy or have you welcomed little ones before?',
        type: 'choice',
        options: [
            { value: 'first', label: 'It’s my first time' },
            { value: 'second', label: 'It’s my second time' },
            { value: 'more', label: 'I’ve experienced this before' },
        ],
    },
    {
        id: 'companion',
        text: 'Who is lovingly supporting you on this journey?',
        type: 'choice',
        options: [
            { value: 'none', label: 'Just me right now' },
            { value: 'partner', label: 'My partner' },
            { value: 'family', label: 'Another family member' },
            { value: 'partner_family', label: 'Partner and more loved ones' },
        ],
    },
];

const totalSteps = questions.length;
const currentQuestionIndex = ref(0);
const conversationComplete = ref(false);

const currentQuestion = computed(() =>
    conversationComplete.value ? null : questions[currentQuestionIndex.value]
);

const displayedQuestion = ref('');
const isTyping = ref(false);

const startTyping = () => {
    if (!currentQuestion.value) {
        displayedQuestion.value = '';
        return;
    }

    const text = currentQuestion.value.text;
    displayedQuestion.value = '';
    isTyping.value = true;
    let index = 0;

    const typeNext = () => {
        if (!currentQuestion.value || currentQuestion.value.text !== text) {
            return;
        }

        if (index < text.length) {
            displayedQuestion.value += text.charAt(index);
            index += 1;
            setTimeout(typeNext, 32);
        } else {
            isTyping.value = false;
        }
    };

    typeNext();
};

watch(
    () => currentQuestionIndex.value,
    () => {
        nextTick(() => {
            startTyping();
        });
    }
);

watch(
    () => conversationComplete.value,
    (complete) => {
        if (complete) {
            displayedQuestion.value = '';
        }
    }
);

onMounted(() => {
    startTyping();
});

const currentStep = computed(() => currentQuestionIndex.value + 1);

const goToNext = () => {
    if (currentQuestionIndex.value < totalSteps - 1) {
        currentQuestionIndex.value += 1;
    } else {
        conversationComplete.value = true;
    }
};

const selectOption = (value) => {
    if (!currentQuestion.value) {
        return;
    }

    answers[currentQuestion.value.id] = value;
    goToNext();
};

const currentQuestionError = computed(() => {
    const question = currentQuestion.value;

    if (!question || question.type !== 'date') {
        return '';
    }

    const value = answers[question.id];

    if (!value) {
        return '';
    }

    if (value < question.min) {
        return question.id === 'dob'
            ? 'Please share a birth date that keeps you within our supported age range.'
            : 'Please choose a date within the last 12 months.';
    }

    if (value > question.max) {
        return question.id === 'dob'
            ? 'This would make you younger than 13. Please double-check the year.'
            : 'The date can’t be in the future. Let’s try again.';
    }

    return '';
});

const canProceed = computed(() => {
    const question = currentQuestion.value;

    if (!question || question.type !== 'date') {
        return false;
    }

    const value = answers[question.id];
    return Boolean(value) && !currentQuestionError.value;
});

const proceedFromDateQuestion = () => {
    if (!currentQuestion.value || currentQuestion.value.type !== 'date') {
        return;
    }

    if (!canProceed.value) {
        return;
    }

    goToNext();
};

const friendlyPregnancyInfo = computed(() => {
    if (answers.pregnancyInfo === 'yes') {
        return 'Joyfully shared today';
    }

    if (answers.pregnancyInfo === 'no') {
        return 'We’ll take it gently when you’re ready';
    }

    return 'Awaiting your cue';
});

const formattedLastPeriod = computed(() => formatDisplayDate(answers.lastPeriod));

const estimatedDueDate = computed(() => {
    if (!answers.lastPeriod) {
        return '';
    }

    const dueDate = shiftDays(new Date(answers.lastPeriod), 280);
    return formatDisplayDate(formatDate(dueDate));
});

const calculateAge = (dob) => {
    if (!dob) {
        return 29;
    }

    const birthDate = new Date(dob);
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();

    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age -= 1;
    }

    return age;
};

const calculateGestationalWeeks = (lastPeriod) => {
    if (!lastPeriod) {
        return 0;
    }

    const diffMs = today.getTime() - new Date(lastPeriod).getTime();
    return Math.max(0, Math.floor(diffMs / (7 * 24 * 60 * 60 * 1000)));
};

const profile = computed(() => {
    const weeks = calculateGestationalWeeks(answers.lastPeriod);
    const months = weeks > 0 ? Math.max(1, Math.min(10, Math.round(weeks / 4.3))) : 1;

    return {
        age: calculateAge(answers.dob),
        healthCondition: 'Healthy',
        babyMonth: months,
    };
});

const gestationCopy = computed(() => {
    const weeks = calculateGestationalWeeks(answers.lastPeriod);

    if (weeks <= 0) {
        return '';
    }

    return `You’re approximately ${weeks} weeks along — here’s what to keep an eye on next.`;
});

const pregnancyOrderCopy = computed(() => {
    switch (answers.pregnancyOrder) {
        case 'first':
            return 'First journey';
        case 'second':
            return 'Second journey';
        case 'more':
            return 'A beautiful experience you know well';
        default:
            return '';
    }
});

const companionCopy = computed(() => {
    switch (answers.companion) {
        case 'none':
            return 'Gathering courage solo for now';
        case 'partner':
            return 'Partner by your side';
        case 'family':
            return 'Cherished family member';
        case 'partner_family':
            return 'Partner and loving family support';
        default:
            return '';
    }
});

const timeline = [
    {
        title: 'Confirm with Your Care Team',
        description:
            'Book your first visit with a family doctor, midwife, or obstetrician to confirm your pregnancy and discuss prenatal vitamins.',
        window: 'Weeks 4-8',
    },
    {
        title: 'Dating Ultrasound & Prenatal Labs',
        description:
            'Schedule initial labs and an early ultrasound to estimate your due date and establish a health baseline.',
        window: 'Weeks 8-12',
    },
    {
        title: 'Share the News & Plan Support',
        description:
            'Consider telling loved ones, explore community resources, and plan your support circle for the months ahead.',
        window: 'Weeks 12-16',
    },
    {
        title: 'Anatomy Scan',
        description:
            'A detailed ultrasound checks in on baby’s growth and can reveal the baby’s sex if you wish to know.',
        window: 'Week 20',
    },
    {
        title: 'Glucose Screening',
        description:
            'Complete a glucose tolerance test to monitor for gestational diabetes and adjust nutrition plans if needed.',
        window: 'Weeks 24-28',
    },
    {
        title: 'Birth Preferences & Classes',
        description:
            'Finalize your birth plan, tour delivery locations, and join prenatal, lactation, or newborn care classes.',
        window: 'Weeks 30-34',
    },
    {
        title: 'Final Preparations',
        description:
            'Pack your hospital bag, arrange parental leave, and prepare your home for baby’s arrival.',
        window: 'Weeks 35-40',
    },
];

const recommendations = [
    {
        title: 'Prenatal Vitamin Bundle',
        category: 'Health Essentials',
        details: 'Daily folate, iron, and DHA to support baby’s early development and your energy levels.',
    },
    {
        title: 'Stretch Mark Comfort Oil',
        category: 'Body Care',
        details: 'Nourishing botanical oil for gentle evening belly massages to soothe stretching skin.',
    },
    {
        title: 'Supportive Sleep Pillow',
        category: 'Rest & Recovery',
        details: 'Full-body pregnancy pillow that relieves hip and back pressure for deeper sleep.',
    },
    {
        title: 'Cozy Maternity Lounge Set',
        category: 'Apparel',
        details: 'Soft, breathable fabrics designed to grow with you throughout every trimester.',
    },
    {
        title: 'At-Home Doppler Check-Ins',
        category: 'Monitoring',
        details: 'Monthly virtual sessions with a nurse to listen to baby’s heartbeat and answer questions.',
    },
    {
        title: 'Mindful Movement Streaming',
        category: 'Wellness',
        details: 'Prenatal yoga and stretching classes curated for second-trimester comfort.',
    },
];

const visibleCount = 3;
const currentSlide = ref(0);

const totalSlides = computed(() => Math.ceil(recommendations.length / visibleCount));

const visibleRecommendations = computed(() => {
    const start = currentSlide.value * visibleCount;
    return recommendations.slice(start, start + visibleCount);
});

const previous = () => {
    currentSlide.value = (currentSlide.value - 1 + totalSlides.value) % totalSlides.value;
};

const next = () => {
    currentSlide.value = (currentSlide.value + 1) % totalSlides.value;
};

const goTo = (index) => {
    currentSlide.value = index;
};
</script>

<style scoped>
.mama-amy-dashboard {
    font-family: 'Nunito', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    display: flex;
    flex-direction: column;
    gap: 3rem;
    color: #1f2933;
}

.hero {
    background: radial-gradient(circle at top left, #ffe6f2, #fdf3f0, #ffffff 70%);
    padding: 3.5rem 1.5rem;
    transition: padding-bottom 0.3s ease;
}

.hero.conversation-mode {
    padding-bottom: 5rem;
}

.hero-content {
    max-width: 1100px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2.5rem;
    align-items: center;
}

.avatar-card {
    text-align: center;
    background-color: #ffffff;
    border-radius: 1.75rem;
    box-shadow: 0 20px 45px -25px rgba(244, 114, 182, 0.6);
    padding: 2.5rem 1.75rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.25rem;
}

.animated-avatar {
    position: relative;
    width: 160px;
    height: 160px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f472b6, #fcd34d);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    box-shadow: 0 18px 30px -20px rgba(236, 72, 153, 0.7);
    animation: float 5s ease-in-out infinite;
}

.avatar-glow {
    position: absolute;
    inset: 6px;
    border-radius: 50%;
    background: radial-gradient(circle at top, rgba(255, 255, 255, 0.6), rgba(255, 255, 255, 0));
    pointer-events: none;
    animation: pulse 4s ease-in-out infinite;
}

.avatar-body {
    position: relative;
    width: 110px;
    height: 110px;
    border-radius: 50%;
    background: linear-gradient(160deg, rgba(255, 255, 255, 0.9), rgba(255, 240, 248, 0.4));
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    gap: 0.5rem;
}

.avatar-face {
    position: relative;
    width: 72px;
    height: 40px;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    padding: 0 12px 8px;
}

.avatar-eye {
    width: 12px;
    height: 12px;
    background: #ec4899;
    border-radius: 50%;
    animation: blink 6s ease-in-out infinite;
}

.avatar-eye.right {
    animation-delay: 1.5s;
}

.avatar-smile {
    position: absolute;
    bottom: 3px;
    left: 50%;
    width: 36px;
    height: 18px;
    border-bottom: 3px solid rgba(236, 72, 153, 0.75);
    border-radius: 0 0 18px 18px;
    transform: translateX(-50%);
}

.avatar-heart {
    width: 32px;
    height: 32px;
    background: linear-gradient(140deg, #f472b6, #fb7185);
    clip-path: path('M16 29C6 21 0 16 0 10a10 10 0 0 1 16-8 10 10 0 0 1 16 8c0 6-6 11-16 19z');
    opacity: 0.7;
    animation: heartbeat 2.8s ease-in-out infinite;
}

.avatar-message {
    font-size: 1rem;
    color: #475569;
    line-height: 1.7;
}

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
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.choice-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 25px -15px rgba(236, 72, 153, 0.6);
}

.date-input {
    flex: 1 1 220px;
    padding: 0.85rem 1rem;
    border-radius: 14px;
    border: 1px solid #d4d4d8;
    font-size: 1rem;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.date-input:focus {
    outline: none;
    border-color: #f472b6;
    box-shadow: 0 0 0 4px rgba(244, 114, 182, 0.2);
}

.cta-button {
    padding: 0.85rem 1.75rem;
    border-radius: 999px;
    border: none;
    background: linear-gradient(135deg, #f472b6, #facc15);
    color: #ffffff;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.cta-button:disabled {
    cursor: not-allowed;
    opacity: 0.5;
    box-shadow: none;
}

.cta-button:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 12px 25px -15px rgba(236, 72, 153, 0.6);
}

.input-error {
    width: 100%;
    font-size: 0.95rem;
    color: #dc2626;
}

.welcome-card {
    background-color: rgba(255, 255, 255, 0.95);
    border-radius: 1.75rem;
    padding: 2.75rem 2.5rem;
    box-shadow: 0 15px 35px -20px rgba(14, 116, 144, 0.35);
}

.summary-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.summary-label {
    display: block;
    font-size: 0.9rem;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: #f472b6;
}

.summary-value {
    font-size: 1.1rem;
    color: #1f2937;
    font-weight: 600;
}

.timeline-section,
.carousel-section {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

.timeline-section h2,
.carousel-section h2 {
    font-size: 1.9rem;
    margin-bottom: 1rem;
    color: #0f172a;
}

.timeline-intro {
    font-size: 1rem;
    color: #475569;
    margin-bottom: 1.5rem;
}

.timeline {
    display: grid;
    gap: 1.5rem;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
}

.timeline-event {
    border-left: 4px solid #f472b6;
    padding: 1.5rem 1.5rem 1.5rem 1.75rem;
    background-color: #ffffff;
    border-radius: 1.25rem;
    box-shadow: 0 10px 30px -20px rgba(15, 23, 42, 0.4);
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.event-header {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.event-trimester {
    font-size: 0.85rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #ec4899;
}

.timeline-event h3 {
    font-size: 1.2rem;
    color: #1f2937;
    margin: 0;
}

.timeline-event p {
    margin: 0;
    color: #475569;
    line-height: 1.6;
}

.carousel-section {
    padding-bottom: 3rem;
}

.carousel-header {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    color: #334155;
}

.personalization-note {
    font-size: 1rem;
    color: #64748b;
}

.carousel {
    display: flex;
    align-items: stretch;
    gap: 1rem;
}

.carousel-control {
    background: transparent;
    border: none;
    font-size: 2.5rem;
    color: #f472b6;
    cursor: pointer;
    padding: 0 0.5rem;
    align-self: center;
}

.carousel-track {
    flex: 1;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.25rem;
}

.carousel-card {
    background: #ffffff;
    border-radius: 1.25rem;
    padding: 1.75rem 1.5rem;
    box-shadow: 0 15px 35px -25px rgba(15, 23, 42, 0.35);
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.carousel-card h3 {
    margin: 0;
    font-size: 1.25rem;
    color: #1f2937;
}

.category {
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #f472b6;
}

.details {
    flex: 1;
    margin: 0;
    color: #475569;
    line-height: 1.5;
}

.action-button {
    align-self: flex-start;
    padding: 0.65rem 1.2rem;
    border-radius: 999px;
    border: 1px solid rgba(236, 72, 153, 0.35);
    background: #fff7fb;
    color: #ec4899;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.action-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px -18px rgba(236, 72, 153, 0.8);
}

.carousel-dots {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 1.5rem;
}

.dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: none;
    background-color: #e2e8f0;
    cursor: pointer;
}

.dot.active {
    background-color: #f472b6;
}

.visually-hidden {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    border: 0;
}

@keyframes float {
    0%,
    100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

@keyframes pulse {
    0%,
    100% {
        opacity: 0.6;
    }
    50% {
        opacity: 0.9;
    }
}

@keyframes heartbeat {
    0%,
    100% {
        transform: scale(0.95);
    }
    50% {
        transform: scale(1.05);
    }
}

@keyframes blink {
    0%,
    5%,
    100% {
        transform: scaleY(1);
    }
    2.5% {
        transform: scaleY(0.2);
    }
}

@media (max-width: 768px) {
    .hero {
        padding: 2.75rem 1rem;
    }

    .conversation-card,
    .welcome-card {
        padding: 2.25rem 1.75rem;
    }

    .carousel {
        flex-direction: column;
    }

    .carousel-control {
        align-self: flex-end;
    }

    .carousel-track {
        grid-template-columns: 1fr;
    }
}
</style>
