# VCNexus 🚀

## 🌟 Badges

| Build Status | Version | License |
| :----------: | :-----: | :-----: |
| ![Build Status](https://img.shields.io/badge/build-passing-brightgreen) | ![Version](https://img.shields.io/badge/version-1.0.0-blue) | ![License](https://img.shields.io/badge/license-MIT-green) |

## 📚 Table of Contents

- [About VCNexus](#about-vcnexus)
- [🚀 Features](#features)
- [💻 Tech Stack](#tech-stack)
- [📦 Dependencies](#dependencies)
- [🐳 Docker Setup](#docker-setup)
- [🔧 Installation](#installation)
- [🚀 Usage](#usage)
- [📂 Project Structure](#project-structure)
- [🛠️ API Reference](#api-reference)
- [🤝 Contributing](#contributing)
- [📜 License](#license)
- [🔗 Important Links](#important-links)

---

## ℹ️ About VCNexus

VCNexus is a robust, multi-tenant ERP system tailored for comprehensive business management. It aims to provide a unified platform for managing crucial organizational aspects including users, tasks, time tracking, payments, and performance metrics. The system is designed with a modern architecture, separating concerns between a powerful PHP backend and a dynamic Vue.js frontend.

## ✨ Features

- **Multi-Tenancy:** Designed to support multiple organizations within a single instance, ensuring data isolation and management.
- **User Management:** Comprehensive handling of users, roles, and permissions.
- **Task Management:** Functionality for creating, assigning, and tracking tasks.
- **Time Tracking:** Integrated system for logging and managing time spent on tasks.
- **Payment Processing:** Modules for managing financial transactions and payments.
- **Performance Tracking:** Tools for monitoring and analyzing organizational and individual performance.
- **API-Driven:** A well-defined API facilitates communication between the frontend and backend.
- **Dockerized Environment:** Streamlined development and deployment using Docker and Docker Compose.

## 💻 Tech Stack

| Category | Technologies |
|---|---|
| **Backend** | PHP, PostgreSQL, Redis, Memcached |
| **Frontend** | Vue.js, Vite, Tailwind CSS, Bootstrap, Pinia, Vue Router, Axios |
| **Infrastructure** | Docker, Nginx |
| **Development Tools** | Composer, npm, Vite |

## 🔗 Dependencies

### Backend Dependencies (via Composer)

- `php: ^8.1`
- `ext-pdo`
- `ext-json`
- `doctrine/orm`
- `symfony/validator`
- `symfony/var-dumper`
- `adodb/adodb-php`
- `ext-zip`
- `phpmailer/phpmailer`
- `phpoffice/phpspreadsheet`
- `vlucas/phpdotenv`

### Frontend Dependencies (via npm)

- `@tailwindcss/vite`
- `@vitejs/plugin-vue`
- `axios`
- `bootstrap`
- `bootstrap-icons`
- `pinia`
- `tailwindcss`
- `vue`
- `vue-router`
- `vite`

## 🐳 Docker Setup

This project utilizes Docker and Docker Compose for setting up the development and production environments. All necessary services (PostgreSQL, Redis, Memcached, PHP-FPM, Nginx, Node.js) are defined in `docker-compose.yml`.

**Key Services:**

- **`db`**: PostgreSQL database service.
- **`redis`**: Redis caching service.
- **`cache`**: Memcached caching service.
- **`php`**: PHP-FPM service for the backend.
- **`nginx`**: Nginx web server for serving frontend and backend requests.
- **`node-dev`**: Node.js service for frontend development (Vite).
- **`node-build`**: Node.js service for frontend builds.

## 🔧 Installation

1.  **Prerequisites:**
    *   [Docker](https://docs.docker.com/get-docker/) and [Docker Compose](https://docs.docker.com/compose/install/) installed.
    *   [PHP](https://www.php.net/manual/en/install.php) (version 8.1 or higher) and [Composer](https://getcomposer.org/download/) installed locally (optional, as Docker will handle dependencies).
    *   [Node.js](https://nodejs.org/) and [npm](https://docs.npmjs.com/cli/v8/commands/npm-install) installed locally (optional, as Docker will handle dependencies).

2.  **Clone the Repository:**
    ```bash
    git clone https://github.com/vinicius-g-cordeiro/vcnexus.git
    cd vcnexus
    ```

3.  **Configure Environment Variables:**
    Create a `.env` file in the root directory (or specific service directories as needed) and populate it with your environment-specific settings. For database and secret configurations, you might need to create files referenced in `docker-compose.yml` (e.g., `./secrets/db_password`).

    `Note: There is also the PHP settings .env that sould be placed on infrastructure/php/.env, this one contains most app related varibles, there is no use of passwords or secrets on docker, it's used only on runtime.` 

    Example `.env` (root level, adjust as needed):
    ```env
    DB_DRIVER=pgsql
    DB_HOST=db
    DB_PORT=5432
    DB_USERNAME=app_user
    DB_PASSWORD_FILE=./secrets/db_password
    DB_DATABASE=app_db
    
    REDIS_PORT=6379
    REDIS_PASSWORD_FILE=./secrets/redis_password
    
    CACHE_PORT=11211
    
    JWT_SECRET_FILE=./secrets/jwt_secret
    APP_KEY_FILE=./secrets/app_key
    
    NGINX_PORT=80
    VITE_PORT=5173
    APP_PORT=5173
    APP_HOST=http://localhost
    ```

4.  **Build and Run Docker Containers:**
    ```bash
    docker-compose up --build
    ```
    This command will build the necessary Docker images and start all the defined services.

5.  **Install Backend Dependencies (if not using Docker for build):**
    ```bash
    cd backend
    composer install
    ```

6.  **Install Frontend Dependencies:**
    ```bash
    cd frontend
    npm install
    ```

## 🚀 Usage

### Development Mode

To run the application in development mode, ensure the Docker containers are running. The frontend development server will be accessible at `http://localhost:5173` and the backend API will be served via Nginx at `http://localhost:80`.

-   **Start Frontend Development Server:**
    ```bash
    docker-compose up node-dev
    ```
    Navigate to `http://localhost:5173` in your browser.

-   **Accessing the Backend API:**
    The backend API is typically proxied through Nginx. You can interact with the API endpoints as defined in the `backend/app/Controller` directory.

### Accessing the Application

Once the containers are running and dependencies are installed, you can access the application through your browser. The frontend will be served by the `node-dev` service (or the `nginx` service for production builds), and the backend API will be available via the `nginx` service, which proxies requests to the `php` service.

**Frontend URL:** `http://localhost:5173` (default VITE_PORT)

**Backend API Base URL:** `http://localhost:80` (default NGINX_PORT)

### Example API Endpoints (from `UserController`):

-   **Get all users:** `GET /users/list/`
-   **Get user by ID:** `GET /users/{id}/`
-   **Create user:** `POST /users/create/`
-   **Update user:** `PUT /users/{id}/update/`
-   **Deactivate user:** `DELETE /users/{id}/delete/`

## 📂 Project Structure

```
vcnexus/
├── backend/
│   ├── app/
│   │   ├── Controller/
│   │   ├── DTOs/
│   │   ├── Database/Schema/
│   │   ├── Events/
│   │   ├── Exceptions/
│   │   ├── Middleware/
│   │   ├── Model/
│   │   ├── Service/
│   │   ├── Shared/
│   │   │   ├── Attributes/
│   │   │   ├── Helpers/
│   │   │   ├── Interfaces/
│   │   │   └── RateLimiting/
│   │   ├── bootstrap.php
│   │   └── ...
│   ├── config/
│   ├── public/
│   ├── tests/
│   ├── vendor/
│   ├── .env (example)
│   ├── composer.json
│   └── composer.lock
├── frontend/
│   ├── public/
│   ├── src/
│   │   ├── App.vue
│   │   ├── components/
│   │   ├── routes/
│   │   ├── services/
│   │   └── stores/
│   ├── index.html
│   ├── package.json
│   ├── tailwind.config.js
│   └── vite.config.js
├── infrastructure/
│   ├── nginx/
│   │   └── default.conf
│   ├── node/
│   │   └── Dockerfile
│   └── php/
│       ├── Dockerfile
│       ├── php.ini
│       └── xdebug.ini
├── .dockerignore
├── docker-compose.yml
└── README.md
```

## 🛠️ API Reference

The backend provides a RESTful API. Key routes are defined in `backend/app/Controller/UserController.php` and other controller files.

### User Endpoints

-   **`GET /users/`**: (This route seems to be defined as a base path in `UserController` but might not have a specific action mapped to it.)
-   **`GET /users/list/`**: Retrieves a list of users. Internally calls `Model->createTable()`, suggesting it might be used for initial setup or table creation verification.
-   **`GET /users/{id}/`**: Retrieves a specific user by ID. Returns placeholder data.
-   **`POST /users/create/`**: Creates a new user. Applies a rate limit of 5 attempts per 60 seconds.
-   **`PUT /users/{id}/update/`**: Updates a specific user by ID.
-   **`DELETE /users/{id}/delete/`**: Deactivates a user.
-   **`PUT|GET|PATCH /users/{id}/activate/`**: Activates a user.
-   **`PUT|GET|PATCH /users/{id}/block/`**: Blocks a user.

**Middleware:**

-   `LoggingMiddleware` is applied globally.
-   `CorsMiddleware` is included in `bootstrap.php`.
-   `RateLimitMiddleware` is applied to specific routes (e.g., user creation).

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1.  Fork the repository.
2.  Create a new branch for your feature (`git checkout -b feature/AmazingFeature`).
3.  Commit your changes (`git commit -m 'Add some AmazingFeature'`).
4.  Push to the branch (`git push origin feature/AmazingFeature`).
5.  Open a Pull Request.

Please ensure your code adheres to the project's coding standards and includes relevant tests.

## 📜 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🔗 Important Links

-   **Repository:** [https://github.com/vinicius-g-cordeiro/vcnexus](https://github.com/vinicius-g-cordeiro/vcnexus)
-   **Author Profile:** [https://github.com/vinicius-g-cordeiro](https://github.com/vinicius-g-cordeiro)

---

## Footer

© 2026 VCNexus | Made with ❤️ by [Vinicius Goncalves Cordeiro](https://github.com/vinicius-g-cordeiro)

[![Star](https://img.shields.io/github/stars/vinicius-g-cordeiro/vcnexus?style=social)](https://github.com/vinicius-g-cordeiro/vcnexus/stargazers)
[![Fork](https://img.shields.io/github/forks/vinicius-g-cordeiro/vcnexus?style=social)](https://github.com/vinicius-g-cordeiro/vcnexus/forks)

[Report issues](https://github.com/vinicius-g-cordeiro/vcnexus/issues) or [Suggest features](https://github.com/vinicius-g-cordeiro/vcnexus/issues).


---
**<p align="center">Generated by [ReadmeCodeGen](https://www.readmecodegen.com/)</p>**