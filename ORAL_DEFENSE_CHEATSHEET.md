# Oral Defense Cheat Sheet - radji e-shopping

Use this page during your presentation for fast speaking support.

## 1) 30-second introduction

My project is **radji e-shopping**, a full-stack e-commerce platform built with **PHP, MySQL, HTML, CSS, and JavaScript**.
It supports **three roles**: Admin, Seller, and Customer.
The system handles real workflows: authentication, product management, orders, reviews, and file uploads/downloads.

## 2) Core objective

I built this system to solve a practical problem:
- sellers need an easy way to publish and manage products,
- customers need a simple way to browse, buy, review, and download product files,
- admins need control over users and platform activity.

## 3) Architecture in simple words

- **public/**: visible pages (home, products, login/register)
- **src/includes/**: backend handlers (login, register, order, review, uploads)
- **src/admin, src/seller, src/customer/**: role-specific dashboards
- **database/schema.sql**: all tables and relationships
- **config/config.php**: database connection and app URL setup

## 4) Key database tables

- **users**: account data + role
- **products**: items created by sellers
- **product_images**: multiple images per product
- **orders**: purchase history
- **comments**: ratings + quality reviews
- **downloads**: file download tracking
- **categories**: product grouping

## 5) What each role can do

### Admin
- View dashboard
- Manage users (activate/suspend)

### Seller
- Add product with images/documents
- View orders, uploads, statistics, profile
- Edit/delete own products only

### Customer
- Register/login
- Browse and order products
- Leave reviews
- Access downloads and account pages

## 6) Security points to say confidently

- Passwords are hashed using `password_hash()`.
- SQL uses prepared statements to reduce injection risks.
- Sessions control authentication.
- Role checks protect dashboards.
- Seller update/delete actions verify product ownership.

## 7) Demo flow (fast and safe)

1. Open home page and explain project goal.
2. Login/register quickly and show role behavior.
3. Customer: open product, place order, show orders/review.
4. Seller: open My Products, edit one product, show delete action.
5. Seller: open orders/uploads/statistics/profile pages.
6. Admin: open dashboard and manage users.
7. End with database and security summary.

## 8) Short answers for common questions

**Q: Why this tech stack?**
A: It is simple, reliable, and perfect for demonstrating full-stack fundamentals.

**Q: How do you protect private pages?**
A: Every protected page checks session and role before loading.

**Q: Can a seller edit another seller’s product?**
A: No, server-side queries include both `product_id` and current `seller_id`.

**Q: How are relationships handled?**
A: Foreign keys link users, products, orders, and reviews.

**Q: What can be improved next?**
A: Payment gateway, email notifications, and advanced analytics.

## 9) Final closing sentence

This project proves practical full-stack capability: secure authentication, role-based authorization, relational database design, CRUD operations, and real e-commerce workflows in one integrated system.
