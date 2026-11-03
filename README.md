# Zivo Backend

**The REST API powering Zivo, an AI-powered e-commerce platform.**

Zivo Backend is the server-side application responsible for managing the platform's core e-commerce functionality, including authentication, product catalog, shopping cart, order processing, payments, and customer addresses.

Built with Laravel 12, the backend follows a Service Layer Architecture to separate business logic from HTTP request handling. It also integrates with external services such as Midtrans, RajaOngkir, n8n, and Zivo's AI Service.

## Related Repositories

| Repository | Description |
|---|---|
| [Zivo Frontend](https://github.com/DefanZp/Zivo-frontEnd-next) | Next.js frontend application |
| [Zivo Backend](https://github.com/DefanZp/Zivo-backEnd) | Laravel REST API |
| [Zivo AI Service](https://github.com/DefanZp/Zivo-ai) | AI shopping assistant powered by Python and LangGraph |
| [Zivo n8n](https://github.com/DefanZp/Zivo-n8n) | Workflow automation |

## Features

### Authentication & User Management
- User registration and login.
- Token-based authentication using Laravel Sanctum.
- Email verification.
- Customer profile management.
- Role-based access control for administrators.

### Product Management
- Retrieve and search products.
- Filter products by category, price, and stock availability.
- Sort and paginate product listings.
- Admin product creation, updates, and deletion.
- Product image processing and WebP conversion.
- Low-stock product monitoring.

### Shopping Cart & Orders
- Add products to the shopping cart.
- Increase and decrease item quantities.
- Remove individual items or clear the cart.
- Manage customer shipping addresses.
- Create and retrieve customer orders.
- Validate product availability during checkout.
- Automatically update product stock when an order is created.
- Restore product stock when an eligible order is cancelled.

### Payment Integration
- Midtrans Snap transaction creation.
- Payment notification handling through webhooks.
- Payment status synchronization.
- Order status updates based on payment results.

### Shipping & Regional Data
- Retrieve provinces, cities, districts, and subdistricts through RajaOngkir.
- Cache regional data to reduce repeated external API requests.

### AI Shopping Assistant Integration
- Connect the backend to Zivo AI Service.
- Provide product data for AI-powered product discovery.
- Forward authenticated customer messages to the AI Service.
- Store and retrieve AI agent state.

### Workflow Automation
- Send order-created events to n8n.
- Send payment-settled events to n8n.
- Process webhook requests using Laravel queued jobs.
- Retry failed webhook jobs.

### Admin Management
- Dashboard data.
- Low-stock product monitoring.
- Product management.
- Order management and status updates.

## Technology Stack

| Technology | Purpose |
|---|---|
| PHP 8.2+ | Backend programming language |
| Laravel 12 | Backend framework |
| PostgreSQL | Relational database |
| Laravel Sanctum | API authentication |
| Eloquent ORM | Database interaction |
| Laravel Queue | Background job processing |
| Midtrans | Payment gateway |
| RajaOngkir | Regional and shipping data |
| n8n | Workflow automation |
| FastAPI / LangGraph | AI Service integration |
| Intervention Image | Product image processing |
| PHPUnit | Automated testing |
| Docker | Containerized application build |

## Architecture

The backend uses a Service Layer Architecture to keep controllers focused on handling HTTP requests and responses while business logic is managed by dedicated services.

### Request Flow

```text
Client Applications
        |
        v
    API Routes
        |
        v
   Middleware
        |
        v
   Controllers
        |
        v
  Service Layer
        |
        +--------------------+
        |                    |
        v                    v
   Eloquent ORM       External Services
        |                    |
        v                    +---- Midtrans
    PostgreSQL               +---- RajaOngkir
                             +---- AI Service
                             +---- n8n
```

### Service Layer

Business logic is organized into dedicated service classes.

```text
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   └── Middleware/
│
├── Jobs/
│   ├── SendOrderCreatedWebhook.php
│   └── SendPaymentSettledWebhook.php
│
├── Models/
│
└── Services/
    ├── AddressService.php
    ├── AgentStateService.php
    ├── AiService.php
    ├── AuthService.php
    ├── CartService.php
    ├── CategoryService.php
    ├── DashboardService.php
    ├── OrderService.php
    ├── PaymentService.php
    ├── ProductService.php
    └── RegionService.php
```

Each service is responsible for a specific part of the application:

| Service | Responsibility |
|---|---|
| `AuthService` | Registration, login, and profile updates |
| `ProductService` | Product retrieval, filtering, and management |
| `CategoryService` | Product category management |
| `CartService` | Shopping cart operations and stock validation |
| `AddressService` | Customer address management |
| `OrderService` | Order creation, cancellation, stock management, and status transitions |
| `PaymentService` | Midtrans transactions and payment status handling |
| `RegionService` | RajaOngkir regional data retrieval and caching |
| `AiService` | Communication with the AI Service |
| `AgentStateService` | AI agent state persistence |
| `DashboardService` | Admin dashboard data |

## API Endpoints

All API endpoints are prefixed with `/api`.

### Public Endpoints

| Method | Endpoint | Description |
|---|---|---|
| GET | `/products` | Retrieve products |
| GET | `/products/{id}` | Retrieve product details |
| GET | `/categories` | Retrieve product categories |
| POST | `/register` | Register a customer |
| POST | `/login` | Authenticate a user |
| GET | `/ai/products` | Retrieve product data for AI |
| GET | `/ai/state` | Retrieve AI agent state |
| POST | `/ai/state` | Store AI agent state |
| GET | `/regions/provinces` | Retrieve provinces |
| GET | `/regions/cities/{provinceId}` | Retrieve cities |
| GET | `/regions/districts/{cityId}` | Retrieve districts |
| GET | `/regions/subdistricts/{districtId}` | Retrieve subdistricts |
| POST | `/payments/midtrans/notification` | Handle Midtrans notifications |
| GET | `/email/verify/{id}/{hash}` | Verify customer email |

### Customer Endpoints

These endpoints require authentication using Laravel Sanctum.

| Method | Endpoint | Description |
|---|---|---|
| POST | `/logout` | Log out |
| GET | `/user/profile` | Retrieve user profile |
| PUT | `/user/profile` | Update user profile |
| GET | `/user/addresses` | Retrieve customer addresses |
| POST | `/user/addresses` | Create an address |
| PUT | `/user/addresses/{id}` | Update an address |
| PATCH | `/user/addresses/{id}/default` | Set default address |
| DELETE | `/user/addresses/{id}` | Delete an address |
| GET | `/cart` | Retrieve shopping cart |
| POST | `/cart/items` | Add an item to cart |
| POST | `/cart/items/{productId}/increase` | Increase item quantity |
| POST | `/cart/items/{productId}/decrease` | Decrease item quantity |
| DELETE | `/cart/items/{productId}` | Remove an item |
| DELETE | `/cart` | Clear shopping cart |
| GET | `/orders` | Retrieve customer orders |
| GET | `/orders/{id}` | Retrieve order details |
| POST | `/orders` | Create an order |
| POST | `/payments/{paymentId}/snap-token` | Generate Midtrans Snap token |
| GET | `/payments/{gatewayOrderId}` | Retrieve payment information |
| POST | `/email/verification-notification` | Resend verification email |
| POST | `/ai/chat` | Send a message to the AI assistant |

**Note:** Order creation and Snap token generation additionally require the customer's email to be verified.

### Admin Endpoints

These endpoints require both Laravel Sanctum authentication and the `admin` middleware.

| Method | Endpoint | Description |
|---|---|---|
| GET | `/admin/dashboard` | Retrieve dashboard data |
| GET | `/admin/products/low-stock` | Retrieve low-stock products |
| POST | `/admin/products` | Create a product |
| PUT | `/admin/products/{id}` | Update a product |
| DELETE | `/admin/products/{id}` | Delete a product |
| GET | `/admin/orders` | Retrieve all orders |
| GET | `/admin/orders/{id}` | Retrieve order details |
| PATCH | `/admin/orders/{id}` | Update order status |

## Order & Payment Flow

The backend manages order creation and payment processing through dedicated services.

### Order Creation

1. The customer selects a shipping address and submits checkout.
2. The backend validates the address ownership.
3. Product records are retrieved and locked during the database transaction.
4. Product stock is validated and the order total is calculated.
5. The order and order items are created.
6. Product stock is reduced.
7. A payment record is created.
8. An order-created webhook job is dispatched after the database transaction commits.

### Payment Processing

1. The customer requests a Midtrans Snap token.
2. The backend creates the Midtrans transaction.
3. The customer completes payment through Midtrans.
4. Midtrans sends a payment notification to the backend.
5. The backend validates the notification signature.
6. Payment and order statuses are updated according to the transaction result.
7. A payment-settled webhook job is dispatched after the database transaction commits when payment succeeds.

The backend handles payment states such as `pending`, `settlement`, `capture`, `expire`, `cancel`, and `deny`.

## External Integrations

### Midtrans

Used for payment processing through Midtrans Snap and payment notification webhooks.

### RajaOngkir

Used to retrieve regional data, including provinces, cities, districts, and subdistricts. Regional responses are cached for seven days.

### AI Service

The Laravel backend communicates with the separate Zivo AI Service through HTTP requests.

The AI chat endpoint forwards the authenticated user's access token and chat information to the AI Service.

### n8n

The backend sends two types of events to n8n:

| Event | Trigger |
|---|---|
| `order.created` | After an order has been successfully created |
| `payment.settled` | After a payment has successfully settled or been captured |

Both events are dispatched through Laravel queued jobs instead of blocking the main request.

## Getting Started

### Prerequisites

Make sure the following tools are installed:

- PHP 8.2 or higher
- Composer
- Node.js and npm
- PostgreSQL
- Git

### 1. Clone the Repository

```bash
git clone https://github.com/DefanZp/Zivo-backEnd.git

cd Zivo-backEnd
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Configure Environment Variables

Create your environment file:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Update your `.env` configuration according to your local environment.

### 4. Configure PostgreSQL

Update the database configuration:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=zivo
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

Make sure the PostgreSQL database exists before running migrations.

### 5. Configure External Services

Add the required integration variables to your `.env` file:

```env
# Frontend
FRONTEND_URL=http://localhost:3000

# Midtrans
MIDTRANS_CLIENT_KEY=
MIDTRANS_SERVER_KEY=
MIDTRANS_IS_PRODUCTION=false

# RajaOngkir
RAJAONGKIR_BASE_URL=
RAJAONGKIR_API_KEY=

# n8n
N8N_ORDER_WEBHOOK_URL=
N8N_PAYMENT_WEBHOOK_URL=
N8N_WEBHOOK_USERNAME=
N8N_WEBHOOK_PASSWORD=

# AI Service
AI_SERVICE_URL=http://127.0.0.1:8001
```

Use the appropriate credentials and service URLs for your environment.

### 6. Run Database Migrations

```bash
php artisan migrate
```

To populate the database with the included seed data:

```bash
php artisan db:seed
```

The database seeder runs the following seeders:

- `AdminSeeder`
- `DemoUserSeeder`
- `CategorySeeder`
- `ProductSeeder`

Review the seeder files before running them in an environment containing important data.

### 7. Configure Public Storage

Create the storage symbolic link:

```bash
php artisan storage:link
```

This allows publicly stored product images to be accessed through Laravel's public storage path.

### 8. Build Frontend Assets

The backend repository includes Vite and Tailwind CSS dependencies for its frontend assets.

For production:

```bash
npm run build
```

For local development:

```bash
npm run dev
```

### 9. Run the Application

Start the Laravel development server:

```bash
php artisan serve
```

The API will be available at:

```text
http://127.0.0.1:8000/api
```

### 10. Run the Queue Worker

The application uses queued jobs to process n8n webhooks.

Start a queue worker:

```bash
php artisan queue:work
```

Make sure your configured queue backend is available and the worker is running when testing background webhook processing.

## Composer Scripts

The repository includes several Composer scripts to simplify development.

| Command | Description |
|---|---|
| `composer setup` | Install dependencies, prepare the environment, generate the application key, run migrations, and build frontend assets |
| `composer dev` | Run the Laravel server, queue listener, log viewer, and Vite development server |
| `composer test` | Clear configuration cache and execute Laravel tests |

For example:

```bash
composer dev
```

This command runs the following processes concurrently:

- Laravel development server
- Laravel queue listener
- Laravel Pail log viewer
- Vite development server

## Testing

The project uses PHPUnit for automated testing.

Run the test suite:

```bash
php artisan test
```

Or use the Composer script:

```bash
composer test
```

The PHPUnit configuration uses an in-memory SQLite database for testing.

## Docker

The repository includes a `Dockerfile` for building the application.

The Docker build uses a multi-stage approach:

1. A Node.js stage installs dependencies and builds Vite assets.
2. A PHP 8.3-FPM stage installs PHP dependencies and PostgreSQL extensions.
3. Composer installs production dependencies.
4. The compiled frontend assets are copied into the Laravel application.
5. Storage and bootstrap cache permissions are configured.

Build the Docker image:

```bash
docker build -t zivo-backend .
```

Run the container:

```bash
docker run -p 9000:9000 zivo-backend
```

The container exposes PHP-FPM on port `9000`. A web server or reverse proxy is required to serve HTTP requests to the application.

Database connectivity, environment variables, storage, and queue workers must also be configured for the deployment environment.

## Environment Variables Reference

The following table summarizes the main application and integration variables.

| Variable | Description |
|---|---|
| `APP_NAME` | Application name |
| `APP_ENV` | Application environment |
| `APP_KEY` | Laravel application encryption key |
| `APP_DEBUG` | Enable or disable debug mode |
| `APP_URL` | Backend application URL |
| `FRONTEND_URL` | Frontend URL used for email verification redirects |
| `DB_CONNECTION` | Database driver |
| `DB_HOST` | Database host |
| `DB_PORT` | Database port |
| `DB_DATABASE` | Database name |
| `DB_USERNAME` | Database username |
| `DB_PASSWORD` | Database password |
| `QUEUE_CONNECTION` | Queue backend |
| `MAIL_MAILER` | Mail transport |
| `MIDTRANS_CLIENT_KEY` | Midtrans client key |
| `MIDTRANS_SERVER_KEY` | Midtrans server key |
| `MIDTRANS_IS_PRODUCTION` | Midtrans production mode |
| `RAJAONGKIR_BASE_URL` | RajaOngkir API base URL |
| `RAJAONGKIR_API_KEY` | RajaOngkir API key |
| `N8N_ORDER_WEBHOOK_URL` | n8n order-created webhook URL |
| `N8N_PAYMENT_WEBHOOK_URL` | n8n payment-settled webhook URL |
| `N8N_WEBHOOK_USERNAME` | n8n webhook basic-auth username |
| `N8N_WEBHOOK_PASSWORD` | n8n webhook basic-auth password |
| `AI_SERVICE_URL` | Base URL of the Zivo AI Service |

## Security Considerations

- Keep API keys, passwords, and application secrets in environment variables.
- Never commit the `.env` file or production credentials.
- Use HTTPS in production environments.
- Keep `APP_DEBUG=false` in production.
- Protect administrative endpoints using authentication and role-based middleware.
- Keep payment notification signature validation enabled.
- Run queue workers in the production environment to process background jobs.

## License

This project is licensed under the MIT License, as declared in the Composer project configuration.