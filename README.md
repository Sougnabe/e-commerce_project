# radji e-shopping website

A comprehensive, user-friendly e-commerce platform built with HTML, CSS, JavaScript, and PHP/MySQL. This platform supports multiple user roles (Admin, Seller, and Customer) with features for product management, uploads, downloads, and community reviews.

## Project Features

### User Roles
- **Admin**: Manage users, products, categories, and view reports
- **Seller**: Add and manage products, upload images and documents, track orders
- **Customer**: Browse products, make purchases, leave reviews, download documents

### Key Features
- User authentication and authorization
- Product catalog with filtering and search
- Photo upload for products (multiple images)
- Document download functionality
- Customer review and rating system
- Quality rating for products
- Location-based product discovery
- Shopping cart system
- Order management
- Responsive design for mobile and desktop

## Project Structure

```
radji/
├── .github/
│   └── copilot-instructions.md          # Project instructions
├── public/
│   ├── index.html                        # Home page
│   ├── login.php                         # Login page
│   ├── register.php                      # Registration page
│   ├── products.php                      # Product listing
│   ├── product-detail.php                # Product detail view
│   ├── cart.php                          # Shopping cart
│   ├── contact.php                       # Contact page
│   ├── privacy.php                       # Privacy policy
│   ├── terms.php                         # Terms of service
│   ├── about.php                         # About page
│   ├── css/
│   │   └── style.css                     # Main stylesheet
│   ├── js/
│   │   └── script.js                     # Main JavaScript file
│   └── images/                           # Image assets
├── src/
│   ├── admin/
│   │   ├── dashboard.php                 # Admin dashboard
│   │   └── manage-users.php              # User management
│   ├── seller/
│   │   ├── dashboard.php                 # Seller dashboard
│   │   └── add-product.php               # Add product form
│   ├── customer/
│   │   ├── dashboard.php                 # Customer account
│   │   └── orders.php                    # Customer orders
│   └── includes/
│       ├── header.php                    # Common header
│       ├── footer.php                    # Common footer
│       ├── logout.php                    # Logout handler
│       ├── process_login.php             # Login processing
│       ├── process_register.php          # Registration processing
│       ├── process_product.php           # Product processing
│       ├── process_review.php            # Review processing
│       └── process_contact.php           # Contact form processing
├── database/
│   └── schema.sql                        # Database schema
├── config/
│   └── config.php                        # Database configuration
├── uploads/                              # User uploads directory
└── README.md                             # This file
```

## Requirements

- **Server**: Apache with PHP 7.4 or higher
- **Database**: MySQL 5.7 or higher
- **PHP Extensions**: MySQLi, PDO (optional)
- **Node**: Optional (for local development)

## Installation

### Step 1: Set Up Database

1. Open your MySQL client or phpMyAdmin
2. Execute the SQL script to create the database and tables:

```bash
mysql -u root -p < database/schema.sql
```

Or manually import the `database/schema.sql` file into your MySQL database and execute it.

### Step 2: Configure Database Connection

Edit `config/config.php` and update the database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'radji_eshopping');
```

### Step 3: Set Up Web Server

If using Apache, ensure your document root points to the `public/` directory:

```apache
DocumentRoot "/path/to/radji/public"
```

Or access the project via: `http://localhost/radji/public/`

### Step 4: Create Upload Directories

Create these directories with write permissions (0777):

```bash
mkdir -p uploads/products
mkdir -p uploads/documents
chmod -R 777 uploads/
```

## Usage

### For Customers

1. **Register**: Go to `register.php` and create an account as a customer
2. **Browse Products**: View products on the products page with filtering options
3. **View Details**: Click on a product to view full details, images, and reviews
4. **Leave Reviews**: Rate products and leave comments about quality
5. **Download Documents**: Download product-related documents
6. **Manage Orders**: Track and manage purchases from the customer dashboard

### For Sellers

1. **Register**: Go to `register.php` and create an account as a seller
2. **Add Products**: Use the seller dashboard to upload products
3. **Upload Images**: Add multiple product images during product creation
4. **Upload Documents**: Attach relevant documents to products
5. **Set Location**: Specify product location for location-based discovery
6. **Manage Orders**: View and manage customer orders from your dashboard
7. **View Statistics**: Track sales and earnings

### For Admins

1. **Access Dashboard**: Log in with admin credentials to access the admin dashboard
2. **Manage Users**: View and manage all user accounts (activate/suspend)
3. **Manage Products**: Monitor and approve/reject product listings
4. **Manage Categories**: Create and organize product categories
5. **View Reports**: Analyze sales data and user activity
6. **System Settings**: Configure site-wide settings

## Key Pages

| Page | Purpose | Accessible By |
|------|---------|---------------|
| index.html | Home page with featured products | Everyone |
| login.php | User authentication | Everyone |
| register.php | New user registration | Everyone |
| products.php | Browse all products | Everyone |
| product-detail.php | View product details and reviews | Everyone |
| cart.php | Shopping cart management | Everyone |
| contact.php | Contact form for inquiries | Everyone |
| privacy.php | Privacy policy | Everyone |
| terms.php | Terms of service | Everyone |
| about.php | About the platform | Everyone |
| admin/dashboard.php | Admin overview | Admin Only |
| admin/manage-users.php | User management | Admin Only |
| seller/dashboard.php | Seller overview | Seller Only |
| seller/add-product.php | Add new products | Seller Only |
| customer/dashboard.php | Customer account | Customer Only |
| customer/orders.php | Order history | Customer Only |

## Database Schema

### Users Table
Stores information about all users (Admin, Seller, Customer)
- username, email, password hash
- user_type (admin, seller, customer)
- profile information (name, phone, location)
- account status (active, inactive, suspended)

### Products Table
Stores product information
- seller_id (who listed the product)
- product details (name, description, price)
- quantity_available
- location (for location-based discovery)
- status (active, inactive, archived)

### Product Images Table
Stores multiple images per product

### Orders Table
Tracks customer purchases
- customer_id, seller_id, product_id
- quantity, total_price
- order_status (pending, completed, cancelled)
- delivery information

### Comments Table
Customer reviews and ratings
- rating (1-5 stars)
- quality_rating (product quality 1-5)
- comment text

### Downloads Table
Tracks document downloads for analytics

## Security Features

- Password hashing using PHP's password_hash()
- SQL prepared statements to prevent SQL injection
- Session-based authentication
- User role-based access control
- Input validation on all forms
- CSRF protection ready (can be implemented)

## Responsive Design

The website is fully responsive and optimized for:
- Desktop browsers (1920px and above)
- Tablets (768px to 1024px)
- Mobile devices (320px to 767px)

## Future Enhancements

- Payment gateway integration (Stripe, PayPal)
- Email notifications for orders and reviews
- Advanced search with Elasticsearch
- Product recommendations
- Wishlist feature
- Messaging system between sellers and buyers
- Admin, Seller, and Customer mobile apps
- Multi-language support
- Two-factor authentication

## API Endpoints (Future)

Reference for API development:

```
POST /api/auth/login
POST /api/auth/register
GET /api/products
GET /api/products/{id}
POST /api/products (Seller)
PUT /api/products/{id} (Seller)
DELETE /api/products/{id} (Seller)
POST /api/reviews
GET /api/reviews/{productId}
POST /api/orders
GET /api/orders/{id}
```

## File Upload Limits

- Maximum image file size: 5MB
- Maximum document file size: 10MB
- Allowed image formats: jpg, jpeg, png, gif
- Allowed document formats: pdf, doc, docx, xls, xlsx

## Logging & Debugging

For development purposes, errors are displayed in the browser. In production, disable this:

```php
// In config.php
error_reporting(0);
ini_set('display_errors', 0);
```

## Support

For issues, feature requests, or questions:
- Email: radjieshopping@gmail.com
- Phone: +250795690051

## License

This project is provided as-is for educational and commercial use.

## Contributors

- Project Owner: Richard
- Created: March 2026
- Version: 1.0

---

**Last Updated**: March 9, 2026
