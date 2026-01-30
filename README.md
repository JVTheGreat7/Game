HEAD
# PC Parts E-Commerce System

A complete, fully functional PC Parts E-Commerce platform built with PHP, MySQL, Bootstrap, and JavaScript. This system is designed for classroom demonstration and can be graded live.

## Features

### User Management
- **User Registration**: Customers can create new accounts
- **User Login**: Secure authentication with password hashing
- **Role-Based Access**: Three user roles - Customer, Staff, Admin
- **Session Management**: Persistent sessions with proper authentication checks

### Customer Features
- **Product Browsing**: View all products with filtering by category
- **Search**: Search products by name and brand
- **Shopping Cart**: Session-based shopping cart (add, update, remove items)
- **Checkout**: Complete checkout process with order creation
- **Payment Simulation**: Multiple payment methods (Cash, Card, GCash, PayPal)
- **Order Tracking**: View order history and order details
- **Product Reviews**: Leave ratings and comments on purchased products
- **Customer Dashboard**: Profile and order management

### Staff Features
- **Order Processing**: Update order status (pending → paid → shipped → delivered)
- **Inventory Management**: Monitor stock levels
- **Low Stock Alerts**: Get notified of items with low stock
- **Order List**: View all orders and customer details

### Admin Features
- **Product Management**: Add, edit, delete products
- **Category Management**: Add and manage product categories
- **Inventory Control**: Update stock quantities and track changes
- **User Management**: View all users and delete accounts
- **Order Reports**: View all orders and revenue data
- **Dashboard Analytics**: See key statistics (users, products, orders, revenue)

### System Features
- **Automatic Inventory Updates**: Stock decreases when orders are created
- **Inventory Logs**: Track all inventory changes
- **Payment Processing**: Simulated payment system with status tracking
- **Responsive Design**: Mobile-friendly Bootstrap UI
- **Database Relationships**: Fully normalized database with foreign keys

## Project Structure

```
PC/
├── config/
│   └── db.php                 # Database connection and helper functions
├── includes/
│   ├── auth.php              # Authentication and session functions
│   ├── header.php            # Navigation and header template
│   └── footer.php            # Footer template
├── auth/
│   ├── login.php             # Login page
│   ├── register.php          # Registration page
│   └── logout.php            # Logout handler
├── pages/
│   ├── products.php          # Product listing with filters
│   ├── product-detail.php    # Product detail and reviews
│   ├── cart.php              # Shopping cart
│   ├── checkout.php          # Order checkout
│   ├── payment.php           # Payment simulation
│   ├── dashboard.php         # Customer dashboard
│   ├── orders.php            # Customer orders list
│   └── order-detail.php      # Order details
├── admin/
│   ├── dashboard.php         # Admin dashboard
│   ├── products.php          # Manage products
│   ├── categories.php        # Manage categories
│   ├── inventory.php         # Manage inventory
│   ├── users.php             # Manage users
│   ├── orders.php            # View all orders
│   └── api/
│       └── get-product.php   # API for product data
├── staff/
│   ├── dashboard.php         # Staff dashboard
│   ├── orders.php            # Process orders
│   └── inventory.php         # Check inventory
├── api/
│   ├── cart-actions.php      # Shopping cart API
│   └── add-review.php        # Add review API
├── assets/
│   ├── css/
│   │   └── style.css         # Bootstrap customizations
│   ├── js/
│   │   └── script.js         # JavaScript functions
│   └── images/               # Product images
├── index.php                 # Home page
└── setup.sql                 # Database schema and sample data
```

## Installation & Setup

### Prerequisites
- XAMPP (Apache + MySQL + PHP)
- Modern web browser
- Text editor or IDE

### Step 1: Create Database

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Click "SQL" tab
3. Copy and paste the contents of `setup.sql`
4. Click "Go" to execute

Or use command line:
```bash
mysql -u root -p < setup.sql
```

### Step 2: Place Files in XAMPP

Copy the entire `PC` folder to:
```
C:\xampp\htdocs\PC\
```

### Step 3: Start XAMPP Services

1. Open XAMPP Control Panel
2. Start **Apache** server
3. Start **MySQL** server

### Step 4: Access the Application

Open your browser and go to:
```
http://localhost/PC/
```

## Database Schema

### Users Table
- `user_id` - Primary key
- `full_name` - User's full name
- `email` - Unique email address
- `password_hash` - Bcrypt hashed password
- `role` - ENUM (customer, staff, admin)
- `created_at` - Account creation timestamp

### Products Table
- `product_id` - Primary key
- `category_id` - Foreign key to categories
- `product_name` - Product name
- `brand` - Brand/manufacturer
- `price` - DECIMAL(10,2)
- `specifications` - Technical details
- `status` - ENUM (available, out_of_stock)
- `created_at` - Creation timestamp

### Inventory Table
- `inventory_id` - Primary key
- `product_id` - Foreign key to products (unique)
- `quantity` - Current stock quantity
- `last_updated` - Auto-update timestamp

### Orders Table
- `order_id` - Primary key
- `user_id` - Foreign key to users
- `order_date` - Order creation timestamp
- `total_amount` - DECIMAL(10,2)
- `status` - ENUM (pending, paid, shipped, delivered, cancelled)

### Order Items Table
- `order_item_id` - Primary key
- `order_id` - Foreign key to orders
- `product_id` - Foreign key to products
- `quantity` - Item quantity
- `price` - Price at time of purchase

### Payments Table
- `payment_id` - Primary key
- `order_id` - Foreign key to orders
- `payment_method` - ENUM (cash, card, gcash, paypal)
- `payment_status` - ENUM (pending, completed, failed)
- `paid_at` - Payment timestamp

### Reviews Table
- `review_id` - Primary key
- `product_id` - Foreign key to products
- `user_id` - Foreign key to users
- `rating` - INT (1-5)
- `comment` - Review text
- `review_date` - Review creation timestamp

### Inventory Logs Table
- `log_id` - Primary key
- `product_id` - Foreign key to products
- `change_quantity` - Quantity changed
- `reason` - Reason for change
- `log_date` - Log timestamp

## Test Accounts

### Admin Account
- **Email**: admin@pcparts.local
- **Password**: admin123
- **Access**: Full system access

### Staff Account
- **Email**: staff@pcparts.local
- **Password**: staff123
- **Access**: Order processing, inventory management

### Customer Accounts
- **Email**: john@example.com
- **Password**: customer123

- **Email**: jane@example.com
- **Password**: customer123

## Key Features Implementation

### Authentication System
- Password hashing using PHP's `password_hash()` (Bcrypt)
- Session-based authentication
- Role-based access control (requireRole() function)
- Secure logout

### Shopping Cart
- Session-based (not database stored)
- Add/update/remove items via AJAX
- Real-time cart updates
- Stock validation

### Order Processing
1. User adds items to cart
2. User proceeds to checkout
3. Order created in database
4. Inventory automatically decremented
5. Inventory logs created for tracking
6. Payment simulated
7. Order status updated

### Inventory Management
- Automatic updates on order creation
- Manual updates by admin/staff
- Low stock tracking
- Complete audit logs

### Reviews System
- Only customers who purchased can review
- One review per customer per product
- 1-5 star rating system
- Product average rating calculation

## Technology Stack

### Backend
- **PHP 7.4+**: Server-side logic
- **MySQL 5.7+**: Relational database
- **PDO/MySQLi**: Database interaction

### Frontend
- **HTML5**: Semantic markup
- **CSS3**: Custom styling + Bootstrap 5
- **JavaScript (Vanilla)**: Form validation, AJAX calls
- **Bootstrap 5**: Responsive framework

### Database
- **Normalization**: 3NF (Third Normal Form)
- **Foreign Keys**: Referential integrity
- **Indexes**: Performance optimization
- **Transactions**: Data consistency

## Security Features

- Password hashing with Bcrypt
- SQL injection prevention (sanitization)
- XSS prevention (htmlspecialchars)
- CSRF protection (session validation)
- Role-based access control
- Secure session management

## API Endpoints

### Cart API
- `POST /api/cart-actions.php` - Add to cart
- `GET /api/cart-actions.php?action=get_cart` - Get cart items
- `GET /api/cart-actions.php?action=get_count` - Get cart count

### Review API
- `POST /api/add-review.php` - Submit product review

### Admin API
- `GET /admin/api/get-product.php?id=X` - Get product details

## Common Tasks

### Adding a New Product
1. Go to Admin Dashboard
2. Click "Manage Products"
3. Click "Add Product"
4. Fill in details and submit
5. Product automatically gets inventory record

### Processing an Order
1. Go to Staff Dashboard
2. Click "Process Orders"
3. Select new status from dropdown
4. Click "Update"
5. Order status changes immediately

### Managing Inventory
1. Go to Admin → Inventory
2. Click "Update" on any product
3. Enter new quantity
4. Add reason for change
5. Inventory log is created automatically

### Viewing Reports
1. Go to Admin Dashboard
2. Click on "View All Orders" or "Sales Report"
3. View all orders and analytics

## Troubleshooting

### Database Connection Error
- Check MySQL is running in XAMPP
- Verify database name is `pc_parts_store`
- Confirm user is `root` with no password

### White Screen / 500 Error
- Check PHP error logs in `C:\xampp\php\logs`
- Enable error reporting in PHP
- Verify all required tables exist

### Cart Not Working
- Check if sessions are enabled in PHP
- Verify `session_start()` is called in header.php
- Clear browser cookies and try again

### Login Not Working
- Make sure database has sample data from setup.sql
- Verify password hashing matches stored values
- Check if cookies are enabled in browser

## Performance Optimizations

- Database indexes on frequently queried columns
- Session-based cart (faster than database)
- Lazy loading of product images
- CSS/JS minification possible
- Database query optimization

## Future Enhancements

- Email notifications for orders
- Payment gateway integration (Stripe, PayPal)
- Product recommendations
- Advanced search/filters
- Customer wish list
- Product quantity on product pages
- Multi-image gallery for products
- Order tracking with status timeline
- Customer support tickets
- Analytics dashboard

## Grading Checklist

- ✅ Complete database schema implemented
- ✅ User authentication system (login/register)
- ✅ Role-based dashboards (customer/staff/admin)
- ✅ Product browsing with categories
- ✅ Shopping cart (session-based)
- ✅ Checkout and order creation
- ✅ Payment simulation
- ✅ Automatic inventory updates
- ✅ Order processing (staff)
- ✅ Product reviews system
- ✅ Admin management (products, categories, users)
- ✅ Responsive Bootstrap UI
- ✅ Clear code comments
- ✅ Working in XAMPP
- ✅ Sample data included

## Support

For issues or questions, contact the development team or refer to the inline code comments for detailed explanations.

---

**Version**: 1.0
**Last Updated**: January 2026
**Status**: Production Ready

# Game
ohaha
 e28909a520e26f769ecc284b260792b427c1f6fd
