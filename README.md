# Healthfulforyou v2 API

Backend API for a subscription-based health education platform serving the Asia Pacific region. ( This is interview assigment project only)

## Tech Stack

- **Framework:** Laravel 12 (PHP 8.3)
- **Database:** PostgreSQL 16
- **Cache:** Redis 7
- **Auth:** Laravel Sanctum
- **Containerization:** Docker + Docker Compose

## Quick Start

```bash
git clone https://github.com/arikmpt/healthfulforu-assigment.git
cd healthfulforu-assigment
docker-compose up -d
docker-compose exec app composer install
docker-compose exec app php artisan migrate --seed
```

API will be available at `http://localhost:8000`

Test credentials: `user@healthfulforu.com` / `password`

---

## Local Setup

### Prerequisites
- Docker Desktop
- Git
- Composer

### Installation Steps

1. Clone repository and setup environment:

```bash
cp .env.example .env
# Edit .env as needed
docker-compose exec app php artisan key:generate
```

2. Run migrations and seeders:

```bash
docker-compose exec app php artisan migrate --seed
```

3. Run tests:

```bash
docker-compose exec app php artisan test
```

---

## API Endpoints

You can see the api documentation on
```bash
http://localhost:8000/docs
```

### Authentication (`/api/v1/auth`)
- `POST /register` - Register new user
- `POST /login` - User login
- `POST /logout` - Logout (requires auth)
- `POST /refresh` - Refresh token

### Content (`/api/v1/content`)
- `GET /contents` - List all content (supports filters: type, access_level, topic_id, search)
- `GET /contents/{slug}` - Get content details (premium access check applied)
- `POST /contents` - Create new content (admin/editor only)
- `PUT /contents/{id}` - Update content (admin/editor only)
- `DELETE /contents/{id}` - Delete content (admin only)
- `GET /contents/recommended` - Get personalized content recommendations

### Content Interactions (`/api/v1/content`)
- `POST /contents/{id}/like` - Toggle content like
- `POST /contents/{id}/bookmark` - Toggle content bookmark
- `POST /contents/{id}/share` - Record share event

### Topics (`/api/v1/content`)
- `GET /topics` - List topics/categories (supports filters: type, parent_id)
- `GET /topics/{id}` - Get topic details
- `POST /topics` - Create new topic (admin only)
- `PUT /topics/{id}` - Update topic (admin only)

### User Preferences (`/api/v1/content`)
- `GET /preferences` - Get user's topic preferences
- `POST /preferences` - Set/update topic preference
- `DELETE /preferences/topics/{id}` - Remove preference

### Subscriptions (`/api/v1/subscription`)
- `GET /plans` - List subscription plans
- `POST /subscriptions` - Subscribe to a plan (mock payment)
- `GET /subscriptions/current` - Get user's active subscription
- `POST /subscriptions/{id}/cancel` - Cancel subscription
- `POST /subscriptions/assign` - Assign subscription to user (admin only)

---

## Module Structure

This project uses modular architecture. Each module has a complete structure:

```
app/Modules/
├── Auth/
│   ├── Actions/          # Business logic units
│   ├── Models/           # Eloquent models
│   ├── Services/         # Service layer
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Requests/     # Form validation
│   │   ├── Resources/    # API transformers
│   │   └── Middleware/
│   ├── Database/
│   │   ├── Migrations/
│   │   └── Seeders/
│   └── Routes/
│       └── v1.php
├── Content/
├── Subscription/
└── User/
```

### Creating New Modules

```bash
php artisan make:module ModuleName
```

This generates the complete folder structure plus a base controller.

Additional commands:
- `make:module-controller` - Create controller
- `make:module-model` - Create model + migration
- `make:module-request` - Create request validator
- `make:module-resource` - Create API resource
- `make:module-action` - Create action class
- `make:module-migration` - Create migration
- `make:module-seeder` - Create seeder

---

## Database Schema

### Users & Auth
- `users` - Core user data
- `user_profiles` - Profile details (bio, avatar, etc)
- `roles`, `permissions`, `role_has_permissions`, `model_has_roles` - Spatie permissions

### Content Module
- `contents` - Articles and videos (supports free/premium access)
- `topics` - Categories, topics, conditions (hierarchical)
- `content_topics` - Pivot table for content ↔ topics
- `user_preferences` - Topic preferences per user
- `content_interactions` - Views, likes, bookmarks, shares

### Subscription Module
- `subscription_plans` - Available plans (Free, Basic, Premium, Annual)
- `subscriptions` - User subscriptions with status tracking

---

## Premium Access Control

The `CheckPremiumAccess` middleware checks:
1. Is the content premium?
2. If yes, does the user have an active subscription?
3. If not, return `403 PREMIUM_REQUIRED`


Access flow:
- Free user → free content ✅
- Free user → premium content ❌ (403)
- Subscribed user → all content ✅

---

## Testing

Run the test suite:

```bash
docker-compose exec app php artisan test
```

**Note:** Tests currently use SQLite in-memory database which doesn't support PostgreSQL extensions. For comprehensive testing, configure PostgreSQL in `phpunit.xml` or use manual API testing.

Available feature tests:
- `ContentApiTest` - List content, premium access checks, interactions
- `SubscriptionApiTest` - Subscribe, cancel, admin assignment

---

## Trade-offs & Decisions

### 1. Why Modular Monolith instead of Microservices?

**Choice:** Modular monolith

**Rationale:**
- Small team (2-5 devs) needs fast delivery
- Simpler deployment and debugging
- Can extract to microservices later if needed

**Trade-off:** Can't scale services independently yet, but that's fine for 0-100K users. When we need it, modules are already cleanly separated for easy extraction.

### 2. Why Laravel instead of Node.js or Go?

**Choice:** Laravel 12

**Rationale:**
- Batteries included (auth, queues, ORM) means faster development
- Well-suited for content management systems
- Large Laravel developer pool in APAC
- Performance of 1K-2K req/sec is sufficient for our target users

**Trade-off:** Not as fast as Go, but speed isn't our bottleneck. Our read-heavy pattern with aggressive caching handles load well.

### 3. Why PostgreSQL instead of MongoDB?

**Choice:** PostgreSQL 16

**Rationale:**
- ACID transactions critical for subscriptions/payments
- JSONB still provides flexibility for dynamic schemas
- Built-in full-text search
- Better for complex relational queries

**Trade-off:** Less flexible than MongoDB, but our data is clearly relational (users → subscriptions, content → topics, etc).

### 4. What We're NOT Building (Intentionally)

Features deliberately deferred:
- Native mobile apps → PWA is sufficient initially
- AI personalization → need user behavior data first
- Video transcoding → YouTube embeds work fine
- Multi-language support → focusing on English first
- Real-time features → email notifications are adequate
- Audit trail → nice to have, not yet critical

**Why?** Small team must focus on core value proposition: content + subscriptions. Additional features can be added iteratively after achieving product-market fit.

---

## Answers to Leadership Questions

### 1. What would you build first in 30 days?

Realistic timeline:

**Week 1:** Auth system, database schema, Docker setup
**Week 2:** Content API (CRUD, topics, interactions), subscription logic
**Week 3:** Frontend integration, Stripe payment, email notifications
**Week 4:** Testing suite, production deployment, monitoring setup

Approach: auth → content → payments → deploy. Ship something usable as quickly as possible, then iterate from there.

### 2. What would you NOT build yet?

Explicitly NOT building (for at least 6 months):
- Advanced personalization → need user behavior data
- Native apps → PWA covers mobile use cases
- Video transcoding pipeline → YouTube embeds work
- Social features → moderation overhead too high
- Advanced analytics → Google Analytics is sufficient
- Audit trail → compliance not yet a requirement

MVP focus: user signs up → browses content → subscribes if interested. This loop is what's critical.

### 3. Top 3 Technical Risks?

**Risk 1: Subscription state inconsistency**
Scenario: User pays but can't access content, or vice versa (access without payment).

Mitigation:
- Idempotent webhook handlers
- Daily reconciliation job against Stripe
- Comprehensive test coverage for payment flows
- Grace period for payment failures

**Risk 2: Database performance degradation**
Scenario: N+1 queries, missing indexes, table bloat.

Mitigation:
- Eager loading on all list queries
- Strategic indexing (slug, foreign keys, status columns)
- Query monitoring with alerts for >100ms
- Mandatory pagination on all lists

**Risk 3: CMS sync failures**
Scenario: Webhook doesn't arrive, content never synced.

Mitigation:
- Fallback cron job every 15 minutes
- Sync status logging with alerting
- Manual resync command available
- Queue retry mechanism

### 4. How would you onboard a junior dev?

Progression path:

**Day 1:** Environment setup, codebase tour with senior dev
**Week 1:** Pair programming, reading existing code, fixing 1-2 simple bugs
**Week 2-3:** Build small feature with guidance (e.g., add bookmark API)
**Week 4+:** Tackle features independently with daily check-ins

Key principle: Don't overwhelm with big tasks immediately. Build confidence first.

### 5. How to ensure quality while moving fast?

**Automated checks:**
- All PRs must pass: tests, code style checks, no debug code
- CI runtime under 5 minutes
- Can't merge if tests fail

**Process:**
- Small PRs (under 300 lines) for easier review
- Code review turnaround within 2 hours
- Ship to staging daily

**Monitoring:**
- Sentry for error tracking
- Performance monitoring (alert if avg >500ms)
- Error rate alerts at >1%

**Culture:**
- Tests are non-negotiable
- "Works on my machine" is not acceptable
- Blameless post-mortems for incidents

---

## Project Structure

```
healthfulforyou-v2-api/
├── app/
│   ├── Console/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Middleware/
│   └── Modules/
│       ├── Auth/
│       ├── Content/
│       ├── Subscription/
│       └── User/
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── routes/
│   └── api.php
├── tests/
│   ├── Feature/
│   └── Unit/
├── docker/
├── docker-compose.yml
└── .env.example
```

---

## Contributing

1. Create feature branch from `develop`
2. Write tests for new features
3. Ensure `php artisan test` passes
4. Submit PR with clear description

---

## License

Proprietary - All rights reserved
