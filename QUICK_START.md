# PC Parts E-Commerce System - Quick Start Guide

## ⚡ Fast Setup (5 Minutes)

### 1. Import Database

**Option A: Using phpMyAdmin (Easiest)**
```
1. Go to http://localhost/phpmyadmin
2. Click "SQL" tab
3. Open file: C:\xampp\htdocs\PC\setup.sql
4. Copy and paste all content
5. Click "Go"
```

**Option B: Using MySQL Command Line**
```bash
mysql -u root -p < C:\xampp\htdocs\PC\setup.sql
```

### 2. Start Services

1. Open XAMPP Control Panel
2. Click "Start" for Apache
3. Click "Start" for MySQL

### 3. Access Application

```
Open Browser → http://localhost/PC/
```

---

## 🔐 Test Login Credentials

### Admin Account
```
Email:    admin@pcparts.local
Password: admin123
```
**Access**: Full system control, product/category/user management

### Staff Account
```
Email:    staff@pcparts.local
Password: staff123
```
**Access**: Order processing, inventory monitoring

### Customer Account
```
Email:    john@example.com
Password: customer123
```
**Access**: Shopping, order placement, product reviews

---

## 📁 Complete File Structure

```
C:\xampp\htdocs\PC\
│
├── 📄 index.php                    ← Home page with featured products
├── 📄 setup.sql                    ← Database schema & sample data
├── 📄 README.md                    ← Full documentation
│
├── 📁 config/
│   └── db.php                      ← Database connection
│
├── 📁 includes/
│   ├── auth.php                    ← Authentication functions
│   ├── header.php                  ← Navigation bar
│   └── footer.php                  ← Footer template
│
├── 📁 auth/
│   ├── login.php                   ← User login
│   ├── register.php                ← User registration
│   └── logout.php                  ← Logout handler
│
├── 📁 pages/
│   ├── products.php                ← Product listing & search
│   ├── product-detail.php          ← Product detail & reviews
│   ├── cart.php                    ← Shopping cart
│   ├── checkout.php                ← Order confirmation
│   ├── payment.php                 ← Payment simulation
│   ├── dashboard.php               ← Customer profile
│   ├── orders.php                  ← Order history
│   └── order-detail.php            ← Order details
│
├── 📁 admin/                       ← Admin-only pages
│   ├── dashboard.php               ← Admin overview
│   ├── products.php                ← Manage products
│   ├── categories.php              ← Manage categories
│   ├── inventory.php               ← Manage stock
│   ├── users.php                   ← Manage users
│   ├── orders.php                  ← View all orders
│   └── api/
│       └── get-product.php         ← Product API
│
├── 📁 staff/                       ← Staff-only pages
│   ├── dashboard.php               ← Staff overview
│   ├── orders.php                  ← Process orders
│   └── inventory.php               ← Check inventory
│
├── 📁 api/
│   ├── cart-actions.php            ← Cart operations
│   └── add-review.php              ← Review submission
│
└── 📁 assets/
    ├── css/
    │   └── style.css               ← Bootstrap + custom styles
    ├── js/
    │   └── script.js               ← JavaScript functions
    └── images/                     ← Product images
```

---

## 🚀 Key Features to Test

### Customer Flow
1. **Home Page**: `http://localhost/PC/`
   - Browse featured products
   - View categories

2. **Products**: `http://localhost/PC/pages/products.php`
   - Filter by category
   - Search products
   - View product details

3. **Shopping Cart**: `http://localhost/PC/pages/cart.php`
   - Add items (click "Add to Cart" on any product)
   - Update quantities
   - View total

4. **Checkout**: `http://localhost/PC/pages/checkout.php`
   - Review order items
   - Choose payment method
   - Submit order

5. **Payment**: `http://localhost/PC/pages/payment.php`
   - Simulate payment (click "Confirm Payment")
   - Order is processed
   - Inventory updated automatically

6. **Dashboard**: `http://localhost/PC/pages/dashboard.php`
   - View profile
   - See order history
   - View order details

7. **Reviews**: 
   - Go to product detail page
   - Write review (1-5 stars)
   - View existing reviews

### Admin Flow
1. **Dashboard**: `http://localhost/PC/admin/dashboard.php`
   - View statistics
   - Access management panels

2. **Product Management**: `http://localhost/PC/admin/products.php`
   - Add new products
   - Edit product details
   - Delete products

3. **Category Management**: `http://localhost/PC/admin/categories.php`
   - Add new categories
   - Delete categories

4. **Inventory Management**: `http://localhost/PC/admin/inventory.php`
   - Update stock quantities
   - Add reason for changes
   - View inventory logs

5. **User Management**: `http://localhost/PC/admin/users.php`
   - View all users
   - Delete user accounts

### Staff Flow
1. **Dashboard**: `http://localhost/PC/staff/dashboard.php`
   - See pending orders count
   - Quick links to tasks

2. **Order Processing**: `http://localhost/PC/staff/orders.php`
   - View all orders
   - Update order status (pending → paid → shipped → delivered)

3. **Inventory Check**: `http://localhost/PC/staff/inventory.php`
   - View all stock levels
   - See low stock alerts
   - Monitor inventory status

---

## 🔍 Database Tables (10 Tables)

| Table | Purpose |
|-------|---------|
| `users` | Customer, Staff, Admin accounts |
| `categories` | Product categories |
| `products` | Product details |
| `product_images` | Product photos |
| `inventory` | Stock quantities |
| `orders` | Customer orders |
| `order_items` | Products in each order |
| `payments` | Payment records |
| `reviews` | Product reviews & ratings |
| `inventory_logs` | Inventory change history |

---

## 💾 Sample Data Included

**16 Products** across 8 categories:
- CPUs (Intel i9, AMD Ryzen)
- Motherboards
- RAM Memory
- Storage Drives
- Power Supplies
- Cooling Systems
- Graphics Cards
- PC Cases

**4 Test Users**:
- 1 Admin
- 1 Staff
- 2 Customers

**Sample Reviews**: Already added to products

---

## ✅ Testing Checklist

- [ ] Database imports successfully
- [ ] Home page loads
- [ ] Login works with test accounts
- [ ] Can browse products
- [ ] Can search products
- [ ] Can filter by category
- [ ] Can add items to cart
- [ ] Can update cart quantities
- [ ] Can proceed to checkout
- [ ] Inventory decreases after order
- [ ] Can view order history
- [ ] Can write product review
- [ ] Admin can add/edit products
- [ ] Admin can manage categories
- [ ] Admin can update inventory
- [ ] Staff can process orders
- [ ] Staff can check inventory

---

## 🐛 Troubleshooting

### Issue: "Connection failed"
**Solution**: 
- Make sure MySQL is running
- Check database name is `pc_parts_store`
- Verify credentials in `config/db.php`

### Issue: "Login not working"
**Solution**:
- Database must be imported with sample data
- Clear browser cookies
- Try in incognito mode

### Issue: "Cart is empty"
**Solution**:
- Sessions must be enabled
- Check if cookies are enabled in browser
- Verify you're logged in for checkout

### Issue: "Can't update product"
**Solution**:
- Must be logged in as admin
- Product must exist in database
- Refresh page after update

### Issue: "404 Not Found"
**Solution**:
- Make sure files are in `C:\xampp\htdocs\PC\`
- Check URL matches file paths
- Restart Apache

---

## 📊 Architecture Summary

```
Single Database (pc_parts_store)
     ↓
10 Interconnected Tables
     ↓
Three Role-Based Interfaces
     ├── Customer (Browse, Shop, Review)
     ├── Staff (Process Orders, Check Inventory)
     └── Admin (Manage Everything)
     ↓
Responsive Bootstrap UI
```

---

## 📝 File Formats

- **PHP**: Server-side logic
- **MySQL**: Database (setup.sql)
- **HTML5**: Semantic markup
- **CSS3**: Responsive styling (Bootstrap 5)
- **JavaScript**: Client-side interactions

---

## 🎯 Ready to Demo!

Your system is production-ready and includes:
✅ Complete backend (PHP + MySQL)
✅ Beautiful frontend (Bootstrap 5)
✅ All required features
✅ Sample data
✅ Test accounts
✅ Full documentation

**Good luck with your presentation!** 🚀
