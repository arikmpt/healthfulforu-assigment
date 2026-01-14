
# Architecture Overview

## Project Summary

**HealthfulForYou v2** is a subscription‑based health education platform designed for the Asia‑Pacific market. This document explains how the system is structured, why certain technical decisions were made, and how the application is expected to grow over time.

This project was built as part of a technical interview assignment and is intended to showcase architectural reasoning, Laravel best practices, and production‑ready thinking.

----------

## High‑Level System Design

### Architecture Diagram

```
                    Cloudflare CDN
                   (Static Assets)
                         |
                         v
                   Load Balancer
                         |
                         v
                 ┌────────────────┐
                 │  Laravel API   │
                 │   (Docker)     │
                 └────────────────┘
                         |
        ┌────────────────┼────────────────┐
        v                v                v
   PostgreSQL          Redis              S3
   (Primary DB)        (Cache)          (Media)

```

The system follows a fairly classic web architecture: a CDN for static assets, a containerized Laravel API as the core backend, and managed services for data storage, caching, and media delivery.

----------

## Technology Stack

### Backend

-   Laravel 12 (PHP 8.3)
-   Modular monolith architecture
-   Laravel Sanctum for API authentication
-   Spatie Laravel Permission for role‑based access control (RBAC)
    

### Infrastructure

-   PostgreSQL 16 as the primary database
-   Redis 7 for caching and session storage
-   Docker & Docker Compose for local and production builds
-   Nginx as the HTTP server
    

### Application Modules

```
app/Modules/
├── Auth/          # Authentication, authorization, permissions
├── Content/       # Articles, videos, topics, user interactions
├── Subscription/  # Plans, billing, user subscriptions
└── User/          # Profiles, preferences, account settings

```

Each module is structured to be as independent as possible while still living in a single codebase.

----------

## Key Architectural Decisions

### 1. Why Laravel (Instead of Node.js or Go)

Laravel was chosen primarily for productivity and maintainability:

-   Rich built‑in features (auth, queues, ORM, validation) 
-   Excellent fit for CRUD‑heavy, content‑driven applications
-   Strong ecosystem and mature community
-   Easier onboarding for developers in the APAC region
    

This platform is largely read‑heavy, so performance concerns are handled through caching. For use cases like real‑time messaging or ultra‑high concurrency, Node.js or Go would be stronger candidates, but they would add unnecessary complexity here.

----------

### 2. Modular Monolith vs Microservices

The application is built as a **modular monolith**.

**Why this approach works well:**

-   Faster development and iteration
-   Single deployment pipeline
-   Simpler infrastructure and operations
-   No distributed system overhead
-   Ideal for small teams (2–5 developers)
    

**How modules interact:**  
Each module owns its own controllers, services, models, and migrations. Direct cross‑module access is avoided; shared logic is exposed through service interfaces.

**When microservices would make sense:**

-   High‑volume notification delivery
-   Video processing or transcoding
-   Analytics and reporting workloads
    

Those components can be extracted later without rewriting the entire system.

----------

### 3. PostgreSQL Instead of MongoDB

PostgreSQL was selected because:

-   The data model is clearly relational
-   Transactions are critical for subscriptions and payments
-   JSONB provides flexibility for metadata and features
-   Built‑in full‑text search capabilities
-   Strong support for complex queries and joins
    

MongoDB would add little value here, as the domain does not rely on deeply nested or schema‑less data.

----------

## Database Design

### Users & Authentication

```sql
users
- id, uuid, email, password
- email_verified_at, status
- last_login_at, last_login_ip
- timestamps, soft deletes

user_profiles
- id, user_id (FK)
- first_name, last_name, avatar_url
- bio, date_of_birth, country, phone
- timestamps, soft deletes

-- Spatie permission tables
roles, permissions,
model_has_roles, model_has_permissions,
role_has_permissions

```

----------

### Content Module

```sql
contents
- id, uuid, title, slug
- summary, body, thumbnail_url, video_url
- type (article/video)
- access_level (free/premium)
- status (draft/published/archived)
- author_id, published_at
- views_count, likes_count, shares_count, bookmarks_count
- metadata (JSONB)
- timestamps, soft deletes

topics
- id, uuid, name, slug, description
- type (topic/category/condition)
- parent_id
- icon_url, sort_order, is_active
- timestamps, soft deletes

content_topics
- content_id, topic_id
- is_primary
- timestamps

user_preferences
- user_id, topic_id
- interest_level (1–10)

content_interactions
- user_id, content_id
- type (view/like/bookmark/share)
- interacted_at
- metadata (JSONB)

```

----------

### Subscriptions

```sql
subscription_plans
- id, uuid, name, slug, description
- price, currency
- billing_period
- billing_cycle_days
- features (JSONB)
- is_active, sort_order
- timestamps, soft deletes

subscriptions
- id, uuid, user_id, subscription_plan_id
- status (active/cancelled/expired/suspended)
- starts_at, expires_at, cancelled_at
- payment_method, payment_reference
- auto_renew
- timestamps, soft deletes

```

----------

## Premium Content Access

Premium access is enforced through middleware:

1.  Check whether the content is marked as premium
    
2.  If so, verify that the user has an active subscription
    
3.  If not, return a 403 response with a `PREMIUM_REQUIRED` error code
    

```php
public function hasActiveSubscription(): bool
{
    return $this->subscriptions()
        ->where('status', 'active')
        ->where('expires_at', '>', now())
        ->exists();
}

```

**Access rules:**

-   Free user + free content → allowed
-   Free user + premium content → denied
-   Subscribed user → full access
    

----------

## Scalability Roadmap

### Phase 1: MVP (0–10K Users)

-   2 application servers
-   Single PostgreSQL instance
-   Single Redis node
-   Cloudflare CDN
    

Target: <200ms p95 API latency, 99.5% uptime

----------

### Phase 2: Growth (10K–100K Users)

-   Auto‑scaling app servers
-   PostgreSQL read replicas
-   Redis cluster
-   Dedicated search engine (Meilisearch)
    

Target: <150ms p95 latency, 99.9% uptime

----------

### Phase 3: Scale (100K+ Users)

-   Multi‑region deployment
-   Sharded PostgreSQL
-   Event‑driven architecture
-   Dedicated analytics storage
    

Target: <100ms p95 latency, 99.95% uptime

----------

## Security Considerations

-   Token‑based authentication via Sanctum
-   RBAC using Spatie Permissions
-   Rate limiting on auth and APIs
-   HTTPS‑only production traffic
-   Encrypted passwords and sensitive fields
-   Soft deletes for auditability
-   GDPR‑ready data handling
    

----------

## Caching Strategy

**Cached data includes:**

-   Content listings
-   Content details
-   User profiles
-   Subscription status
-   Topic hierarchies
    

Redis is used due to its speed, Laravel integration, and ability to also serve as a queue backend.

----------

## Deployment & CI/CD

```
Git Push
 → GitHub Actions
 → Automated Tests
 → Docker Build
 → Push to Registry
 → Deploy to Staging
 → Manual Approval
 → Blue‑Green Production Deploy

```

Backups are performed daily with point‑in‑time recovery enabled.

----------

## Testing Strategy

Current coverage focuses on:

-   API feature tests
-   Subscription lifecycle
-   Premium access rules
    

Planned additions include:

-   Service‑level unit tests
-   Payment webhook integration tests
-   Load and stress testing
-   Security audits
    

----------

## Final Notes

This architecture is intentionally pragmatic. It prioritizes clarity, maintainability, and real‑world scalability without introducing unnecessary complexity too early.

The system comfortably supports early‑stage growth while leaving clear paths for future extraction into services if and when scale demands it.

----------

**Project Status:** Interview Assignment (MVP Complete)