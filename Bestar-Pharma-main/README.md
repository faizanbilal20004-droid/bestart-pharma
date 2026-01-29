# Bestar Pharma – Laravel Backend API

This repository contains the **Laravel backend API** for the **Bestar Pharma mobile application**, built using **Angular Ionic**.

---

## 🚀 Project Overview
This backend powers the mobile application by providing secure and scalable REST APIs for pharmaceutical products, orders, and user management.

---

## 🛠 Tech Stack
- Laravel 10+
- PHP 8+
- MySQL
- RESTful API
- JWT / Sanctum Authentication

---

## ✨ Features
- Product listing & details
- Authentication (API-based)
- Cart & order management
- Secure API endpoints
- Mobile app integration

---

## 📱 Mobile Application
Frontend is developed using **Angular Ionic** and consumes this API.

---

## ⚙️ Installation & Setup

```bash
git clone https://github.com/USERNAME/bestar-pharma.git
cd bestar-pharma
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
