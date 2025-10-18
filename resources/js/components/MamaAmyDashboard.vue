<template>
    <div class="mama-amy-dashboard" :class="{ 'post-conversation': conversationComplete }">
        <section v-if="!conversationComplete" class="hero" :class="{ 'conversation-mode': !conversationComplete }">
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

                <ConversationCard
                    :question="currentQuestion"
                    :current-step="currentStep"
                    :total-steps="totalSteps"
                    :displayed-question="displayedQuestion"
                    :is-typing="isTyping"
                    :answers="answers"
                    :current-question-error="currentQuestionError"
                    :can-proceed="canProceed"
                    @select-option="selectOption"
                    @update-answer="updateAnswer"
                    @proceed-date="proceedFromDateQuestion"
                />
            </div>
        </section>

        <div v-else class="post-conversation-main">
            <div class="post-layout">
                <PostConversationInfoPanel
                    :answers="answers"
                    :friendly-pregnancy-info="friendlyPregnancyInfo"
                    :formatted-last-period="formattedLastPeriod"
                    :estimated-due-date="estimatedDueDate"
                    :profile-age="profile.age"
                    :pregnancy-order-copy="pregnancyOrderCopy"
                    :companion-copy="companionCopy"
                />

                <div class="post-right-column">
                    <JourneyRoadmap :timeline="timeline" :gestation-copy="gestationCopy" />
                    <EssentialsCarousel :recommendations="recommendations" :profile="profile" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, nextTick, onMounted, reactive, ref, watch } from 'vue';

import ConversationCard from './ConversationCard.vue';
import EssentialsCarousel from './EssentialsCarousel.vue';
import JourneyRoadmap from './JourneyRoadmap.vue';
import PostConversationInfoPanel from './PostConversationInfoPanel.vue';

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

const updateAnswer = (id, value) => {
    answers[id] = value;
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
        title: 'Mid-Pregnancy Anatomy Scan',
        description:
            'Review baby’s growth and anatomy, discuss birthing preferences, and check in on your emotional wellbeing.',
        window: 'Weeks 18-22',
    },
    {
        title: 'Third Trimester Prep',
        description:
            'Attend childbirth classes, review your birth plan with your provider, and prepare your postpartum support network.',
        window: 'Weeks 28-32',
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
</script>

<style scoped>
.mama-amy-dashboard {
    font-family: 'Nunito', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    display: flex;
    flex-direction: column;
    gap: 3rem;
    color: #1f2933;
}

.mama-amy-dashboard.post-conversation {
    background: radial-gradient(circle at top right, rgba(250, 232, 255, 0.45), rgba(240, 253, 244, 0.3));
    padding-bottom: 4rem;
}

.hero {
    background: radial-gradient(circle at top left, #ffe6f2, #fdf3f0, #ffffff 70%);
    padding: 3.5rem 1.5rem 5rem;
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

.post-conversation-main {
    padding: 0 1.5rem;
}

.post-layout {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: minmax(280px, 360px) 1fr;
    gap: 2.5rem;
    align-items: flex-start;
}

.post-right-column {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

@media (max-width: 960px) {
    .post-layout {
        grid-template-columns: 1fr;
    }
}

@keyframes float {
    0%,
    100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-8px);
    }
}

@keyframes pulse {
    0%,
    100% {
        opacity: 0.7;
    }
    50% {
        opacity: 0.5;
    }
}

@keyframes blink {
    0%,
    92%,
    100% {
        transform: scaleY(1);
    }
    95% {
        transform: scaleY(0.2);
    }
}

@keyframes heartbeat {
    0%,
    100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.12);
    }
}
</style>
