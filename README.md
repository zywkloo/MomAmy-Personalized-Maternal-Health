# Mom Amy – Your Smart Pregnancy & Baby Care Companion

A Laravel 11 backend foundation for **Mom Amy Care – 艾米智慧孕产AI助手**. It provides FHIR-friendly APIs to manage pregnancy records, observations, reports, and vitals for families while matching enterprise healthcare requirements (Gravit-e, Optum).

The goal is to grow this service into a personalized maternal health assistant anchored by an AI avatar named **Mom Amy** who
can guide every user through their care journey with proactive recommendations.

---

## ✨ Key Modules

- **Pregnancy records** (`patients`, FHIR `Patient`): demographic profiles, pregnancy timeline, due date calculations.
- **Observations & vitals** (`observations`, `vitals`, FHIR `Observation`): weight, blood pressure, fetal heart rate, glucose trends.
- **Service requests** (`service_requests`, FHIR `ServiceRequest`): lab / ultrasound order history.
- **Diagnostic reports** (`reports`, FHIR `DiagnosticReport`): PDF/JPG/PNG uploads with metadata and secure download URLs.
- **Imaging studies** (`imaging_studies`, FHIR `ImagingStudy`): DICOM metadata placeholders ready for gRPC parsing.
- **Security & sharing**: Laravel Passport JWT, role-ready architecture, Couchbase session/document storage scaffolding.

---

## 🏗️ Architecture Overview

- Laravel 11 API with Passport for OAuth2 / JWT access tokens.
- MySQL handles transactional data; Couchbase reserved for document storage (FHIR JSON, session sharing).
- gRPC DICOM microservice stub (`grpc-dicom/`) prepared for image metadata parsing / de-identification.
- Job-ready scaffolding (queues, events) for OCR parsing, AI insights, BLE ingestion.
- API-first design so the future Vue 3 dashboard / mobile apps can connect consistently.

---

## 🚀 Getting Started

### Prerequisites

- PHP 8.2+
- Composer
- MySQL 8+
- Couchbase Server 7+
- Node 18+ (for future Vue frontend)

### Installation

```bash
git clone <repo-url> mom-amy-care
cd mom-amy-care
composer install
cp .env.example .env
php artisan key:generate
```

Set up database connections in `.env` (MySQL + Couchbase credentials). Then run migrations:

```bash
php artisan migrate
```

Passport client & storage setup:

```bash
php artisan passport:install
php artisan storage:link
```

Run the Laravel development server:

```bash
php artisan serve
```

Optional: start the gRPC DICOM stub (requires Go):

```bash
cd grpc-dicom
go run dicom_parser.go
```

---

## 🔑 API Highlights

- `POST /api/auth/register` – create user + linked patient profile, returns Passport token.
- `POST /api/auth/login` – obtain JWT for authenticated calls.
- `GET /api/fhir/patients/{patient}` – FHIR Patient resource view.
- `GET /api/fhir/patients/{patient}/observations` – bundled Observation resources.
- `POST /api/patients/{patient}/vitals` – store vitals & prep for FHIR mapping.
- `POST /api/patients/{patient}/reports` – upload PDF/JPG/PNG diagnostic reports.

All secured routes require `Authorization: Bearer <token>` header.

---

## ☁️ Deployment Notes

- Docker Compose template (coming soon) will orchestrate Laravel, MySQL, Couchbase, gRPC service, and Nginx.
- For GKE / Cloud Run: move environment secrets to Secret Manager, enable Cloud SQL / Couchbase managed cluster.
- Frontend can be deployed separately (e.g., Vercel) consuming the REST endpoints.
- Log aggregation via Cloud Logging or ELK stack recommended for medical audits.

---

## 🧭 Roadmap Ideas

- BLE device ingestion (VitalMark reuse) streaming into `vitals` + Observation generation.
- OCR + AI summarization jobs producing pregnancy visit summaries (GPT Codex service).
- Temporary sharing tokens for physicians / doulas; integrate Keycloak or SAML SSO.
- FHIR bulk export + SMART-on-FHIR compatibility for enterprise partners.

---

## 🤝 Contributing

Issues and PRs are welcome as the platform grows from family-use prototype to healthcare-grade solution.
