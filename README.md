# 🎓 Adaptive LMS Platform

A team-based **Learning Management System (LMS)** designed to support online learning through course management, quizzes, certificates, and learning analytics.

The project was developed as a collaborative software engineering project using **Laravel** and a REST API architecture.

---

## 📋 Project Overview

The platform provides core functionality for managing digital learning environments, including:

* Course and learning content management
* User authentication
* Quizzes and assessments
* Certificates
* Learning analytics
* REST API integration

The project focuses on building a structured backend for an adaptive learning platform.

---

## 👥 Team Project

This project was developed collaboratively as part of a team.

### My Contribution

**Backend Development — Laravel**

My main responsibilities included:

* Backend development with **Laravel**
* REST API development
* Authentication and authorization
* Database integration
* API testing and validation
* Quality assurance
* Supporting project delivery and integration

---

## 🛠️ Technologies

| Category       | Technologies              |
| -------------- | ------------------------- |
| Backend        | Laravel 13.x, PHP 8.3+    |
| Database       | MySQL / PostgreSQL        |
| Authentication | Laravel Sanctum           |
| API            | REST API                  |
| Development    | Composer, Laravel Artisan |

---

## 🏗️ Backend Architecture

The backend follows Laravel's application architecture and exposes functionality through REST APIs.

```text
Client / Frontend
       │
       ▼
   REST API
       │
       ▼
 Laravel Backend
       │
 ┌─────┼─────────────┐
 ▼     ▼             ▼
Auth  Business     Data
      Logic        Access
       │             │
       └──────┬──────┘
              ▼
        MySQL / PostgreSQL
```

Authentication is handled using **Laravel Sanctum**.

---

## 🚀 Installation

### Prerequisites

Make sure the following are installed:

* PHP 8.3+
* Composer
* MySQL or PostgreSQL

### 1. Clone the repository

```bash
git clone <repository-url>
cd <project-folder>
```

### 2. Install dependencies

```bash
composer install
```

### 3. Configure environment variables

Create your `.env` file:

```bash
cp .env.example .env
```

Generate the Laravel application key:

```bash
php artisan key:generate
```

Configure your database connection in `.env`.

### 4. Set up the database

Run migrations and seed the database:

```bash
php artisan migrate --seed
```

### 5. Start the development server

```bash
php artisan serve
```

The application will be available at:

```text
http://127.0.0.1:8000
```

---

## 📌 Main Concepts

The project provided practical experience with:

* Laravel backend development
* REST API design
* Authentication with Sanctum
* Database management
* Backend testing
* Team-based development
* Software quality and delivery

---

## 🎯 Project Objective

The objective of the project was to develop the backend foundation of an LMS platform while applying modern web development practices and collaborative software engineering workflows.

---

## 👩‍💻 Author

**Khadija Sayoukh**

Engineering Student — Digital Transformation & Artificial Intelligence

[GitHub](https://github.com/khadija-sk) · [LinkedIn](https://www.linkedin.com/in/khadija-sayoukh-1a1a94288)
