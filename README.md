# Mama Amy – Personalized Maternal Health

Mama Amy is a Laravel 11 foundation for a maternal health companion that blends clinical record keeping with proactive guidance.
The service will launch quickly on a lean stack while keeping a clear runway toward richer recommendations and an AI assistant.

---

## 🚀 Ship Now (Option A)
- **Frontend:** Vue experience on Vercel calling the Laravel API.
- **Backend:** Laravel + PHP-FPM on a Forge-provisioned VPS (PHP 8.2/8.3, SSL enabled).
- **Data:**
  - PlanetScale or Aurora Serverless for MySQL transactions.
  - Couchbase Capella for flexible patient and profile documents.
- **Platform essentials:** Redis cache/queue (Upstash or VM-local), secrets managed via Forge + `.env`, backups handled by PlanetScale/Capella with a Laravel scheduled backup job.

Pair this with Dockerfiles and `docker-compose` checked into the repo from day one so the eventual move to Fly.io, Render, or ECS is configuration—not a rewrite.

---

## 🗺️ Growth Roadmap
### 0–6 Months: Launch & Learn
- Capture pregnancy journeys, vitals, reports, and visit timelines through REST APIs.
- Automate deploy hooks on Forge: `composer install`, `php artisan migrate`, `npm run build`, cache warms.
- Instrument Sentry (errors) and simple request logs.

### 6–12 Months: Scale & Observe
- Containerize the app and deploy to Fly/Render/ECS with separate web + worker services.
- Add observability (Prometheus/Grafana or managed APM), canary/blue-green deploys, and expanded job workers.
- Introduce a vector service alongside Laravel for embeddings and retrieval.

---

## 🛍️ Recommendations & AI Assistant
- **Data model:**
  - Catalog, categories, events (views/clicks/purchases) stay in MySQL.
  - Non-PHI user profiles live in Couchbase for flexible segmentation.
  - Treat any potential PHI with explicit consent, audit trails, encryption, and geography-aware compliance.
- **Merch recommendations:**
  - *Version 1 (2–4 weeks):* Content-based ranking (TF-IDF or embeddings) with rules around pregnancy stage; cold-start fallbacks for popular items.
  - *Version 2:* Add vector search (pgvector/Qdrant/Weaviate) plus nightly batch refresh for collaborative filtering (implicit ALS).
- **Consumer health AI:**
  - Start with an external LLM API, retrieval-augmented prompts sourced from vetted guidelines, safety guardrails, and “not medical advice” UX.
  - Plan clinician review hooks and escalation triggers before surfacing higher-risk guidance.

---

## ✅ Immediate Next Steps
1. Provision Forge VPS, install Couchbase extension (`libcouchbase` + `pecl`), confirm with `php -m | grep couchbase`.
2. Connect Laravel to PlanetScale + Capella through `.env` and rotate secrets regularly.
3. Commit Dockerfiles/docker-compose for local parity.
4. Define event schema (views, clicks, purchases) and ship a minimal `/recommendations` endpoint.

---

## 🧑‍💻 Local Development
```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan serve
```

Use the same steps inside Docker once the compose files are added. Run `php artisan test` and `npm run test` (when available) before opening pull requests.
