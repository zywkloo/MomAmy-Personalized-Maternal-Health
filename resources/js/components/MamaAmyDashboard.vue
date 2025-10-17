<template>
    <div class="mama-amy-dashboard">
        <section class="hero">
            <div class="hero-content">
                <div class="avatar-card">
                    <div class="avatar">
                        <span>Mama Amy</span>
                    </div>
                    <p class="avatar-message">
                        Your compassionate companion for every milestone and question.
                    </p>
                </div>
                <div class="welcome-card">
                    <p class="greeting">Sweetheart, when did your most recent period begin?</p>
                    <div class="input-row">
                        <input
                            v-model="lastPeriod"
                            type="date"
                            class="period-input"
                            aria-label="Last period start date"
                        />
                        <button type="button" class="cta-button">
                            Share with Mama Amy
                        </button>
                    </div>
                    <p class="congrats" role="status">
                        Congratulations on stepping into motherhood! Mama Amy is here to celebrate and guide you.
                    </p>
                </div>
            </div>
        </section>

        <section class="timeline-section">
            <h2>Pregnancy Journey Highlights</h2>
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

        <section class="carousel-section">
            <div class="carousel-header">
                <h2>Personalized Essentials for You</h2>
                <p class="personalization-note">
                    Tailored for a {{ profile.age }} year old, {{ profile.healthCondition.toLowerCase() }} mom-to-be in month {{ profile.babyMonth }}.
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
import { computed, reactive, ref } from 'vue';

const lastPeriod = ref('');

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

const profile = reactive({
    age: 29,
    healthCondition: 'Healthy',
    babyMonth: 2,
});

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
    padding: 3rem 1.5rem;
}

.hero-content {
    max-width: 1100px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    align-items: center;
}

.avatar-card {
    text-align: center;
    background-color: #ffffff;
    border-radius: 1.5rem;
    box-shadow: 0 20px 45px -25px rgba(244, 114, 182, 0.6);
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.avatar {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    display: grid;
    place-items: center;
    background: linear-gradient(135deg, #f472b6, #fcd34d);
    color: white;
    font-weight: 700;
    font-size: 1.2rem;
    letter-spacing: 0.05em;
    box-shadow: 0 18px 30px -20px rgba(236, 72, 153, 0.7);
}

.avatar-message {
    font-size: 0.95rem;
    color: #475569;
    line-height: 1.6;
}

.welcome-card {
    background-color: rgba(255, 255, 255, 0.92);
    border-radius: 1.5rem;
    padding: 2.5rem;
    box-shadow: 0 15px 35px -20px rgba(14, 116, 144, 0.35);
}

.greeting {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 1.5rem;
}

.input-row {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    align-items: center;
}

.period-input {
    flex: 1 1 220px;
    padding: 0.85rem 1rem;
    border-radius: 999px;
    border: 1px solid #d4d4d8;
    font-size: 1rem;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.period-input:focus {
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

.cta-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 25px -15px rgba(236, 72, 153, 0.6);
}

.congrats {
    margin-top: 1.75rem;
    font-size: 1.05rem;
    color: #0f172a;
    line-height: 1.7;
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

@media (max-width: 768px) {
    .hero {
        padding: 2.5rem 1rem;
    }

    .welcome-card {
        padding: 2rem 1.5rem;
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
