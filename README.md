# 🛒 Laravel E-Commerce REST API

![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)
![MySQL](https://img.shields.io/badge/MySQL-8.0-orange.svg)
![JWT](https://img.shields.io/badge/Auth-Sanctum-green.svg)
![Postman](https://img.shields.io/badge/Postman-Collection-orange)

A robust, feature-complete backend REST API for an E-Commerce platform built with **Laravel 11**. This API handles product management, category system, user authentication (using Laravel Sanctum), order processing with stock validation, and role-based access control (Admin/User).

## ✨ Key Features

- **🔐 Authentication & Authorization**
    - User Registration & Login (Password hashing)
    - API Token generation via Laravel Sanctum
    - Role-based middleware (Admin vs Regular User)
- **📦 Product Management**
    - CRUD operations for products (Admin only)
    - Product categorization
    - Stock quantity management (Auto-decrease on order)
- **📁 Category System**
    - Nested or standalone categories
    - Slug generation for SEO-friendly URLs
- **🛒 Ordering System**
    - Create orders with multiple items
    - Automatic total price calculation
    - Database transaction to ensure data integrity
    - Order status workflow: `pending -> processing -> completed/cancelled`
    - Stock restoration when order is cancelled
- **⚡ Performance**
    - Efficient caching with Laravel Cache (Products, Categories)
    - Pagination for product listing
    - Eager loading relationships (Product -> Category, Order -> Items)

## 🏗️ Architecture & Design Patterns

This project follows **Repository Pattern** (implicitly via Eloquent) and **MVC** architecture.
- **Controllers**: Handle HTTP requests and responses.
- **Models**: Eloquent ORM for database interaction.
- **Resources (JSON)**: Standardized API responses (`ProductResource`, `OrderResource`).
- **Middleware**: Custom `AdminMiddleware` for role checks.
- **Migrations**: Fully version-controlled database schema.

## 🚀 Getting Started

### Prerequisites
- PHP >= 8.2
- Composer
- MySQL / PostgreSQL
- Postman (for testing)

### Installation

1.  **Clone the repository**
    ```bash
    git clone https://github.com/<your-username>/<your-repo-name>.git
    cd <your-repo-name>
