# Mama Amy – Your Smart Pregnancy & Baby Care Companion
A Laravel 11 backend foundation for Mama Amy Care – 艾米智慧孕产AI助手. It provides FHIR-friendly APIs to manage pregnancy records, observations, reports, and vitals for families.

The goal is to grow this service into a personalized maternal health assistant anchored by an AI companion *Mama Amy* who can guide every user through their care journey with proactive recommendations.

---

## 🧩 Key Modules
- **Care Journeys:** Pregnancy timelines, prenatal visit tracking, and postpartum follow-up records under `app/Models` and `app/Http/Controllers`.
- **Observations & Vitals:** Structured FHIR-like resources for vitals, lab reports, and clinician notes exposed through REST controllers.
- **Family & Profile Management:** Household links, dependents, and caregiver roles, backed by policy guards and Sanctum authentication.
- **Content & Recommendations:** Seeds, events, and recommendation scaffolding that the AI companion can use to personalize guidance.

---

## 🏗️ Architecture Overview
| Layer | Current Stack | Notes |
| --- | --- | --- |
| Frontend | Vue 3 SPA deployed on Vercel | Consumes the Laravel API and hosts the roadmap + recommendation UI. |
| Backend | Laravel 11 on PHP-FPM (Forge) | Provides REST APIs, queues, and scheduled tasks. |
| Data | MySQL (PlanetScale/Aurora) + Couchbase Capella | Relational records + flexible user profiles. |
| Caching & Queues | Redis (Upstash or VM-local) | Supports broadcast events and background jobs. |
| Observability | Sentry + structured logs | Expandable to Prometheus/Grafana as scale grows. |

Containerization via Dockerfiles/docker-compose is encouraged early so the application can later migrate to Fly.io, Render, or ECS without rewriting deployment scripts.

---

## 🔑 API Highlights
- `GET /api/profile` – Fetch the authenticated parent or caregiver profile.
- `GET /api/pregnancies` / `POST /api/pregnancies` – Manage pregnancy journeys, due dates, and milestones.
- `GET /api/vitals` / `POST /api/vitals` – Record and retrieve vitals in FHIR-friendly structures.
- `POST /api/reports/upload` – Attach documents or lab summaries to a care journey.
- `GET /api/recommendations` – Retrieve merch and care content tailored by stage and preferences.
- Sanctum-based authentication endpoints under `/api/login` and `/api/logout` protect sensitive data.

---

## 🛣️ Roadmap Snapshot
- **Launch:** Deploy Laravel via Forge, connect PlanetScale + Capella, ship baseline recommendation endpoint, and enable Sentry.
- **Near Term:** Add Dockerized local environment, queue workers for background insights, and nightly backups.
- **Growth:** Introduce vector-powered recommendations, job-specific services, and richer observability before exploring multi-region support.

---

## ⚙️ Local Development
```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan serve
```

Run `php artisan test` and `npm run test` (when available) before opening pull requests. Keep environment variables in sync with Forge or your container orchestration platform.
