# Mama Amy – Your Smart Pregnancy & Baby Care Companion
A Laravel 11 backend foundation for Mama Amy Care – 艾米智慧孕产AI助手. It provides FHIR-friendly APIs to manage pregnancy records, observations, reports, and vitals for families.

The goal is to grow this service into a personalized maternal health assistant anchored by an AI companion *Mama Amy* who can guide every user through their care journey with proactive recommendations.

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

### Windows (PowerShell) local development setup

If you're developing on Windows, you can stay entirely inside PowerShell. Install PHP 8.2+, Composer, Node 18+, and (optionally)
SQLite or MySQL. Then follow the flow below from a PowerShell prompt:

```powershell
# Clone and install dependencies
git clone <repo-url> mom-amy-care
Set-Location mom-amy-care   # PowerShell equivalent of "cd"; moves into the project folder
composer install

# Create your environment file and application key
Copy-Item .env.example .env
php artisan key:generate

# Make sure Laravel isn't using a cached config
php artisan config:clear

# Configure the database driver in .env if you prefer MySQL over SQLite
# Then run migrations (SQLite works out of the box)
php artisan migrate

# Launch the built-in server (http://127.0.0.1:8000)
php artisan serve
```

When you're finished, stop the server with `Ctrl+C`.

#### Troubleshooting “Invalid key supplied” on Windows

If the built-in server throws `LogicException: Invalid key supplied`, it means
Laravel is not reading the `APP_KEY` value you expect. Work through the checks
below directly in PowerShell:

1. **Make sure you are inside the project folder.** `Set-Location mom-amy-care`
   must appear before you run `php artisan serve` or any other Artisan command;
   otherwise Laravel will look for an `.env` file in the wrong directory.
2. **Regenerate the key and paste it into `.env`.**
   ```powershell
   php artisan key:generate --show
   ```
   Copy the `base64:...` string into the `APP_KEY=` line of `.env`, save the
   file, and rerun the next steps.
3. **Clear Laravel's cached configuration and runtime files.**
   ```powershell
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   php artisan optimize:clear
   ```
   Cached configuration or compiled artifacts in `bootstrap/cache/` can keep the
   previous key in memory; clearing them forces Laravel to reread `.env`.
4. **Confirm what Laravel sees at runtime.**
   `Get-ChildItem Env:APP_KEY` only lists Windows environment variables, so it
   will report “path not found” unless you manually exported the key. To verify
   the value Laravel actually loads, run:
   ```powershell
   php artisan tinker --execute "dump(config('app.key'))"
   ```
   or start Tinker (`php artisan tinker`) and call `config('app.key')`. The
   printed value should match the `APP_KEY=` entry in `.env`.

If those steps still fail, delete `bootstrap/cache/config.php` (if present),
restart the terminal, and try `php artisan serve` again.

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

### Deploying to Vercel

Vercel can run the full Laravel + Vue experience by treating Laravel as a serverless PHP function and serving the compiled Vite
assets from `public/build`.

1. Commit and push this repository to GitHub (or another Git provider that Vercel supports).
2. In your Vercel dashboard, click **Add New… → Project** and import the repository.
3. On the project configuration screen set:
   - **Framework Preset**: `Other` (Laravel runs through the custom runtime).
   - **Root Directory**: leave blank so the project root is used.
   - **Build Command**: `npm run build`
   - **Install Command**: `composer install --prefer-dist --no-dev --optimize-autoloader && npm install`
   - **Output Directory**: `public`
4. Under **Environment Variables**, add your production values (at minimum `APP_KEY`, plus any database or API keys your build
   requires). Use `php artisan key:generate --show` locally to create a deployable `APP_KEY`.
5. Click **Deploy**. Vercel will install Composer + npm dependencies, run the Vite build, then provision the PHP serverless
   function defined in `api/index.php`.
6. After the first deployment finishes, open the live URL and you should see the Mama Amy onboarding dashboard.

#### Local smoke test for the Vercel build

You can mimic the production bundle before pushing by running:

```bash
composer install --prefer-dist --no-dev --optimize-autoloader
npm install
npm run build
php artisan serve
```

Visit `http://127.0.0.1:8000` and confirm the dashboard renders correctly. When you're done, run `composer install` (without
`--no-dev`) to restore your local development dependencies.

---

## 🧭 Roadmap Ideas

- BLE device ingestion (VitalMark reuse) streaming into `vitals` + Observation generation.
- OCR + AI summarization jobs producing pregnancy visit summaries (GPT Codex service).
- Temporary sharing tokens for physicians / doulas; integrate Keycloak or SAML SSO.
- FHIR bulk export + SMART-on-FHIR compatibility for enterprise partners.

---

## 🤝 Contributing

Issues and PRs are welcome as the platform grows from family-use prototype to healthcare-grade solution.
