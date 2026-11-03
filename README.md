# Zivo Backend

The Laravel REST API and business-logic layer for **Zivo**, an e-commerce platform with an integrated AI Shopping Assistant.

This service is responsible for user identity, authorization, product catalog data, carts, checkout, orders, payment state, delivery addresses, region lookup, and administrator operations. Laravel remains the source of truth for transactional data used by the frontend and AI service.

## Related Services

- **Frontend:** https://zivo-front-end-next-5uvw.vercel.app/
- **Backend API:** https://api.zivoapp.cloud
- [Frontend Repository](https://github.com/DefanZp/Zivo-frontEnd-next)
- [AI Service Repository](https://github.com/DefanZp/Zivo-ai)
- [n8n Repository](https://github.com/DefanZp/Zivo-n8n)

## Features

- Registration, login, logout, and email verification.
- Sanctum token authentication and role-based administrator authorization.
- Product catalog, search/filter/pagination, categories, and administrator product CRUD.
- Product image upload, resizing, and WebP conversion through Intervention Image.
- User-scoped shopping carts and cart item operations.
- Address management and default delivery address selection.
- Region lookup through RajaOngkir endpoints.
- Transactional checkout, stock validation, and product row locking.
- Customer order history and administrator order management.
- Midtrans Snap transaction creation and payment notification handling.
- Dashboard statistics and low-stock product retrieval.
- AI-oriented product catalog endpoint and authenticated AI chat endpoint.
- n8n webhook jobs for order-created and payment-settled events.

## Architecture

The backend follows a Service Layer structure:

```text
API Route
    |
    v
Controller
    |
    v
Service
    |
    v
Eloquent Model
    |
    v
PostgreSQL
```

Controllers handle HTTP input, validation, and JSON responses. Services contain application and business operations. Eloquent models define data relationships, casts, and persistence behavior.

Key services include `AuthService`, `ProductService`, `CategoryService`, `CartService`, `OrderService`, `PaymentService`, `AddressService`, `RegionService`, `AiService`, `AgentStateService`, and `DashboardService`.

## Core Business Logic

### Checkout and stock consistency

The checkout endpoint receives an address ID and an item list. The backend verifies address ownership, locks the selected product rows with `lockForUpdate()`, validates stock, calculates totals using current product data, creates the order and order items, reduces stock, and creates the payment record inside a database transaction. Order-created webhook work is dispatched after the transaction commits.

The checkout service uses the submitted item IDs and quantities; it does not read the cart table as the source of checkout items. The frontend currently builds the order payload from the cart.

### Order lifecycle

The inspected backend accepts these order statuses:

- `pending`
- `processing`
- `completed`
- `cancelled`

A successful Midtrans `settlement` or `capture` marks the payment as paid and moves the order to `processing`. Expired payments use the cancellation flow, which restores stock. Admin cancellation also restores stock, subject to the service's transition rules.

### Payment integration

The backend creates Midtrans Snap transactions and processes payment notifications. Notification signatures are calculated with SHA-512 and compared using `hash_equals()`. Payment records are locked while sensitive notification state changes are processed.

**Known payment gap:** in the inspected source, Midtrans `cancel` and `deny` mark the payment as `failed` but do not cancel the order or restore stock. Snap creation accepts `unpaid` payments, so the current flow does not provide a retry path for a failed payment. Resolve this lifecycle before describing all payment failure paths as fully handled.

## API Reference

All routes below are registered in `routes/api.php`. Laravel's API route prefix is `/api`.

### Public endpoints

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/products` | List/search/filter products |
| GET | `/api/products/{id}` | Product detail |
| GET | `/api/categories` | List categories |
| POST | `/api/register` | Register a customer |
| POST | `/api/login` | Login and issue a token |
| GET | `/api/ai/products` | Product data for AI retrieval |
| GET | `/api/regions/provinces` | List provinces |
| GET | `/api/regions/cities/{provinceId}` | List cities |
| GET | `/api/regions/districts/{cityId}` | List districts |
| GET | `/api/regions/subdistricts/{districtId}` | List subdistricts |
| POST | `/api/payments/midtrans/notification` | Midtrans notification webhook |
| GET | `/api/email/verify/{id}/{hash}` | Signed email verification link; redirects to frontend |

### Authenticated customer endpoints

These routes require `auth:sanctum`, and some operations additionally require a verified email.

| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/logout` | Revoke current token |
| GET | `/api/user/profile` | Get current user |
| PUT | `/api/user/profile` | Update profile |
| GET, POST | `/api/user/addresses` | List/create addresses |
| PUT | `/api/user/addresses/{id}` | Update address |
| PATCH | `/api/user/addresses/{id}/default` | Set default address |
| DELETE | `/api/user/addresses/{id}` | Delete address |
| GET | `/api/cart` | Get cart |
| POST | `/api/cart/items` | Add item to cart |
| POST | `/api/cart/items/{productId}/increase` | Increase item quantity |
| POST | `/api/cart/items/{productId}/decrease` | Decrease item quantity |
| DELETE | `/api/cart/items/{productId}` | Remove cart item |
| DELETE | `/api/cart` | Clear cart |
| GET | `/api/orders` | List current user's orders |
| GET | `/api/orders/{id}` | Get order detail |
| POST | `/api/orders` | Create order; verified email required |
| POST | `/api/payments/{paymentId}/snap-token` | Create Snap token; verified email required |
| GET | `/api/payments/{gatewayOrderId}` | Get payment by gateway order ID |
| POST | `/api/email/verification-notification` | Resend verification email; throttled |
| POST | `/api/ai/chat` | Authenticated AI chat endpoint |

### Administrator endpoints

These routes require both `auth:sanctum` and the `admin` middleware.

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/admin/dashboard` | Dashboard statistics |
| GET | `/api/admin/products/low-stock` | Low-stock products |
| POST | `/api/admin/products` | Create product |
| PUT | `/api/admin/products/{id}` | Update product |
| DELETE | `/api/admin/products/{id}` | Delete product |
| GET | `/api/admin/orders` | List orders |
| GET | `/api/admin/orders/{id}` | Order detail |
| PATCH | `/api/admin/orders/{id}` | Update order status |

> The `/api/ai/state` routes in the inspected source do not currently have authentication or user-ownership middleware. They are intentionally omitted from the supported endpoint list here and should be secured before being exposed as a public API contract.

## Technology Stack

| Area | Technology |
|---|---|
| Framework | Laravel 12.62.0 |
| Language | PHP `^8.2` |
| Authentication | Laravel Sanctum 4.3.2 |
| Database | PostgreSQL 16 in Docker Compose |
| Payment | Midtrans PHP SDK 2.6.2 |
| Image processing | Intervention Image 3.11.8 |
| Web server | Nginx |
| Runtime | PHP-FPM 8.3 in Docker |
| Queue | Laravel Queue Worker |
| Automation | n8n webhooks |
| Shipping provider | RajaOngkir region lookup |

Versions are based on the uploaded Composer lockfile and Docker configuration.

## Getting Started

### Requirements

- PHP 8.2 or later.
- Composer.
- PostgreSQL 16 or a compatible PostgreSQL server.
- Required PHP extensions for the application and image processing.
- Node.js/npm only when rebuilding the Vite frontend assets included by the backend Dockerfile.

### Local setup

```bash
git clone https://github.com/DefanZp/Zivo-backEnd.git
cd Zivo-backEnd
composer install
cp .env.example .env
php artisan key:generate
```

Configure the database and integration settings in `.env`, then run:

```bash
php artisan migrate
php artisan storage:link
php artisan serve
```

Run the queue worker in a separate terminal when testing queued webhook jobs:

```bash
php artisan queue:work --sleep=3 --tries=3 --timeout=60
```

The current `.env.example` needs to be corrected before it can serve as a reliable PostgreSQL setup guide: it selects `pgsql` but currently contains MySQL-style port/database/user defaults. The Docker Compose service uses PostgreSQL 16, database `zivo`, and user `zivo_user`. Align the sample with the intended local or container-based workflow before publishing finalized setup instructions.

### Environment configuration

The application reads the following integration variables through `config/services.php`:

| Variable | Purpose |
|---|---|
| `FRONTEND_URL` | Frontend URL for email verification redirects |
| `MIDTRANS_CLIENT_KEY` | Midtrans client key |
| `MIDTRANS_SERVER_KEY` | Midtrans server key |
| `MIDTRANS_IS_PRODUCTION` | Select Midtrans environment |
| `RAJAONGKIR_BASE_URL` | RajaOngkir API base URL |
| `RAJAONGKIR_API_KEY` | RajaOngkir API key |
| `N8N_ORDER_WEBHOOK_URL` | Order-created webhook URL |
| `N8N_PAYMENT_WEBHOOK_URL` | Payment-settled webhook URL |
| `N8N_WEBHOOK_USERNAME` | Basic Auth username for n8n webhooks |
| `N8N_WEBHOOK_PASSWORD` | Basic Auth password for n8n webhooks |
| `AI_SERVICE_URL` | AI service URL; source default is `http://127.0.0.1:8001` |

Laravel's standard application, database, mail, queue, filesystem, and cache settings are also required for the selected environment. Never commit `.env` or real credentials.

## Docker Deployment

The provided Compose configuration defines:

- `app` — PHP-FPM application container.
- `queue` — Laravel queue worker using the application image.
- `postgres` — PostgreSQL 16 database.
- `nginx` — reverse proxy for HTTP/HTTPS traffic.

It also uses persistent volumes for PostgreSQL data and public product images, and an external Docker network named `zivo-proxy`.

The current Compose file mounts host Let's Encrypt certificate files for the API and n8n domains and expects the external `zivo-proxy` network to exist. It is therefore deployment-specific; it is not a self-contained local development Compose file. Prepare the network and certificate paths before using it.

## Testing and Verification

Run the Laravel test command with:

```bash
php artisan test
```

The uploaded repository contains only the two Laravel scaffold example tests; domain tests for checkout, payment lifecycle, cart ownership, and order transitions are still needed. In the source audit, 86 PHP files passed `php -l` under PHP 8.4.24. PHPUnit, database migrations, integrations, and deployed endpoints were not executed as part of that audit.

## Known Limitations and Follow-ups

- The `.env.example` PostgreSQL settings do not match the configured PostgreSQL 16 Compose service.
- The Dockerfile uses Intervention Image's GD driver in application code but does not install/enable the PHP GD extension; verify and fix the image-processing runtime dependency.
- Midtrans `cancel`/`deny` currently leave the order pending and stock deducted, with no failed-payment retry path.
- Deleting a product may cascade to historical order items; preserve order history before allowing hard deletion.
- Agent state routes are not scoped to an authenticated user.
- The admin seeder contains fixed default credentials; do not use those credentials in production.
- The inspected RegionService performs region lookup; it does not implement shipping-rate calculation.

## Related Repositories

- [Zivo Frontend](https://github.com/DefanZp/Zivo-frontEnd-next)
- [Zivo AI Service](https://github.com/DefanZp/Zivo-ai)
- [Zivo n8n](https://github.com/DefanZp/Zivo-n8n)

## Author

**Defan Zain Pratama** — Full-Stack Developer interested in web applications, backend systems, and AI-powered applications.

- GitHub: [@DefanZp](https://github.com/DefanZp)
