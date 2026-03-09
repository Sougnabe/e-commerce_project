# Full Presentation Speech - radji e-shopping

Good morning everyone.

My name is [Your Name], and today I am presenting my project called **radji e-shopping**.
This is a full e-commerce web application that I built using **PHP, MySQL, HTML, CSS, and JavaScript**.

---

## 1. Introduction

The goal of this project is to create a practical online shopping platform where different users can do different tasks.

In this system, there are **three user roles**:
1. **Admin**
2. **Seller**
3. **Customer**

Each role has its own dashboard and permissions.
This makes the system organized, secure, and easy to manage.

---

## 2. Problem Statement

Before building this project, I identified a common problem:
- Small sellers need a simple way to put products online.
- Customers need a simple way to browse products, buy, and review quality.
- Admins need to control users and monitor platform activity.

So, I built one system that solves these needs in one place.

---

## 3. Main Objectives

The main objectives of my project were:
- Build a secure login and registration system.
- Implement role-based access control.
- Allow sellers to add and manage products.
- Allow customers to order products and leave reviews.
- Allow admins to manage users.
- Store all data correctly in a relational database.

---

## 4. Technologies Used

For this project, I used:
- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP
- **Database**: MySQL
- **Environment**: XAMPP
- **Version Control**: Git and GitHub

Why this stack?
Because it is reliable, widely used, and very good for full-stack learning.

---

## 5. Project Structure

The project is divided into clear folders:
- **public/** for user-visible pages like home, login, register, products
- **src/includes/** for backend processing files like login, register, order, review
- **src/admin/** for admin pages
- **src/seller/** for seller pages
- **src/customer/** for customer pages
- **database/** for schema.sql
- **config/** for configuration
- **uploads/** for product images and documents

This structure makes maintenance and debugging easier.

---

## 6. Database Design

The database name is **radji_eshopping**.

Main tables are:
- **users**: stores login and role data
- **products**: seller products
- **product_images**: multiple images per product
- **orders**: customer purchases
- **comments**: ratings and quality reviews
- **downloads**: file download history
- **categories**: product categories

Relations are connected with foreign keys.
For example, each product belongs to a seller, and each order links customer, seller, and product.

---

## 7. System Features by Role

### 7.1 Admin Features
- Admin can log in to admin dashboard.
- Admin can view platform data.
- Admin can activate or suspend users.

### 7.2 Seller Features
- Seller can register and log in.
- Seller can add products with images and optional document files.
- Seller can view own orders.
- Seller can open file uploads page.
- Seller can open statistics page.
- Seller can edit profile.
- Seller can edit and delete only own products.

### 7.3 Customer Features
- Customer can register and log in.
- Customer can browse products and product details.
- Customer can place orders.
- Customer can view order history.
- Customer can download files when allowed.
- Customer can leave reviews and quality ratings.
- Customer can manage account/profile pages.

---

## 8. Security and Validation

In this project, I applied important security practices:
- Passwords are hashed using `password_hash()`.
- SQL prepared statements are used to reduce SQL injection risk.
- Session-based authentication is used after login.
- Role checks prevent unauthorized access to dashboards.
- Seller actions verify ownership before edit or delete.
- Form inputs are validated before saving.

---

## 9. Workflow Example

Let me explain one simple workflow:

1. Seller logs in.
2. Seller adds a new product with image.
3. Product appears on public product list.
4. Customer logs in and places an order.
5. Order appears in customer and seller dashboards.
6. Customer can leave a review after purchase.

This shows end-to-end interaction between users and database.

---

## 10. Improvements Done During Development

During development, I improved many things:
- Fixed missing pages that caused “not found” errors.
- Made seller pages fully functional (orders, uploads, statistics, profile).
- Added seller product edit/delete permissions.
- Updated contact information (email, phone, address).
- Changed branding to **radji e-shopping**.
- Redirected successful login/register to home page.
- Pushed code updates to GitHub.

---

## 11. Challenges and How I Solved Them

I faced some technical challenges:
- Route and page-not-found issues
- Database connection issues when MySQL is offline
- Broken links between role dashboards and pages

How I solved them:
- Added missing pages and corrected links
- Added database safety checks
- Verified role-based navigation and handlers

These improvements made the project stable and usable.

---

## 12. Future Improvements

In the future, this project can be upgraded with:
- Online payment integration (Stripe/PayPal)
- Email notifications
- Better analytics charts
- Full wishlist persistence
- Recommendation engine
- REST API for mobile app

---

## 13. Conclusion

To conclude,
this project is a complete and practical e-commerce system.
It demonstrates my skills in:
- full-stack web development,
- relational database design,
- authentication and authorization,
- CRUD operations,
- and secure backend logic.

Thank you for listening.
I am ready for your questions.

---

## Optional Short Closing (if time is finished)

In short, radji e-shopping is a secure multi-role online shopping platform where admins manage users, sellers manage products, and customers buy and review products.
