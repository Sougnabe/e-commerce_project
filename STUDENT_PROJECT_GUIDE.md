# Student Project Guide - radji e-shopping

This guide is written to help a student explain the project clearly in class, during a defense, or in an interview.

## 1) Project in one minute (quick pitch)

radji e-shopping is a multi-user e-commerce web application built with PHP, MySQL, HTML, CSS, and JavaScript.

It supports 3 roles:
- Admin: manages users and monitors system activity.
- Seller: publishes products, tracks orders, uploads product files, and manages product information.
- Customer: browses products, places orders, downloads product documents, and leaves reviews.

Main value:
- It is not just a static website; it is a complete dynamic platform with authentication, role-based access, database operations, and file uploads/downloads.

## 2) Problem and solution

Problem:
- Small online sellers need a simple platform to list products and connect with customers.
- Customers need a way to compare products, buy safely, and review quality.

Solution:
- A role-based e-commerce system where each actor sees only what they need.
- Product lifecycle from creation to order to review is handled inside one system.

## 3) Technology stack and why

Frontend:
- HTML/CSS/JavaScript for UI and interactions.

Backend:
- PHP for server logic and page rendering.

Database:
- MySQL for persistent storage (users, products, orders, comments, downloads).

Why this stack:
- Easy to deploy with XAMPP.
- Good for learning full-stack fundamentals.
- Clear separation between pages, handlers, and database.

## 4) High-level architecture

Flow:
1. User opens a page in public.
2. Form submits to a process file in src/includes.
3. Process file validates input and updates MySQL.
4. User is redirected with success/error feedback.
5. Dashboard pages load role-specific data from DB.

Structure overview:
- public: public-facing pages (home, login, register, product browsing).
- src/includes: shared UI and business logic handlers.
- src/admin, src/seller, src/customer: role-specific dashboards and tools.
- config: environment and DB connection.
- database: SQL schema.
- uploads: uploaded images/documents.

## 5) Database design (important for explanation)

Key tables:
- users: login credentials + profile + role.
- products: product info linked to seller.
- product_images: multiple images per product.
- orders: purchase records between customer and seller.
- comments: customer ratings and quality reviews.
- downloads: tracks document downloads.
- categories: product categories.

Main relationships:
- One seller can have many products.
- One customer can place many orders.
- One product can have many reviews and images.

## 6) Role-based features

### Admin
- Views admin dashboard.
- Manages users (activate/suspend).
- Monitors platform-level metrics.

### Seller
- Adds products with images/documents.
- Views and filters own orders.
- Uses statistics page for performance.
- Manages own profile.
- Can edit and delete only own products.

### Customer
- Registers and logs in.
- Browses and filters products.
- Places orders.
- Downloads eligible files.
- Writes reviews for purchased products.
- Manages profile and account pages.

## 7) Security and correctness decisions

- Passwords are hashed with password_hash.
- Authentication uses PHP sessions.
- SQL queries use prepared statements (reduces SQL injection risk).
- Role checks protect admin/seller/customer pages.
- Seller product actions verify ownership before update/delete.
- Input validation exists in process handlers.

## 8) What happens in key use cases

### A) User registration
1. User submits register form.
2. System validates required fields, email format, and password confirmation.
3. System checks duplicate username/email.
4. Password is hashed and stored.
5. User session starts and user is redirected to home.

### B) Seller adds product
1. Seller submits product form.
2. Product saved in products table.
3. Images are uploaded and stored in product_images.
4. Optional document is uploaded and linked.
5. Product becomes visible in product listing.

### C) Customer places order
1. Customer opens product details and submits order.
2. System validates stock and inserts order.
3. Quantity updates accordingly.
4. Order appears in customer and seller dashboards.

### D) Customer leaves review
1. System checks customer purchased the product.
2. Review is inserted/updated with rating and quality score.
3. Review appears in product detail and customer review history.

## 9) Demo script for presentation (5-8 minutes)

Use this sequence during class:
1. Home page: show featured products and navigation.
2. Register/Login: show role-based access behavior.
3. Customer flow: browse product -> place order -> view orders -> add review.
4. Seller flow: add product -> open My Products -> edit product -> delete product.
5. Seller stats/uploads/profile pages: show they are functional.
6. Admin flow: open dashboard and manage users.
7. Conclude with DB tables and security points.

Tip:
- Keep one browser window per role (or use incognito tabs) to switch fast during demo.

## 10) Questions a teacher may ask (with short answers)

Q1: Why use separate folders for admin/seller/customer?
- To enforce role separation and improve maintainability.

Q2: How do you prevent unauthorized page access?
- Every protected page checks session and user role before rendering.

Q3: How do you secure passwords?
- Passwords are never stored as plain text; they are hashed.

Q4: How do you connect product and seller?
- products.seller_id is a foreign key to users.user_id.

Q5: How do you avoid SQL injection?
- Prepared statements with parameter binding.

Q6: Can one seller edit another seller's product?
- No. Update/delete queries include both product_id and current seller_id.

## 11) Known limitations and future improvements

Current limitations:
- No online payment gateway yet.
- No advanced recommendation engine.
- Wishlist can be expanded with full DB persistence.

Possible improvements:
- Stripe/PayPal integration.
- Email notifications.
- Better analytics and charts.
- API layer for mobile app.

## 12) Local setup summary (quick)

1. Import database/schema.sql into MySQL.
2. Confirm config/config.php DB credentials.
3. Start PHP server from project root.
4. Open public/index.php in browser.

Default admin account:
- Username: admin
- Password: admin123

## 13) Final conclusion sentence for student

This project demonstrates full-stack web development skills: authentication, role-based authorization, CRUD operations, file handling, relational database design, and secure backend processing in a real e-commerce workflow.
