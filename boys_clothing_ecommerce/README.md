# Second-Hand Boys Clothing E-Commerce Marketplace

A comprehensive web-based marketplace platform for buying and selling second-hand boys' clothing items. This project is developed as part of the Software Engineering course (Fall 2025).

## 📋 Project Overview

The Second-Hand Boys Clothing E-Commerce Marketplace connects buyers seeking affordable, quality second-hand clothing with sellers looking to monetize unused items. The platform provides a secure, user-friendly environment with comprehensive features for product management, order processing, communication, and administration.

## 👥 Group Members

- [Member 1 Name] - [Role/Skills]
- [Member 2 Name] - [Role/Skills]
- [Member 3 Name] - [Role/Skills]
- [Member 4 Name] - [Role/Skills]

## 🎯 Key Features

### For Buyers
- Browse and search products
- Shopping cart and wishlist
- Order placement and tracking
- Return request management
- Real-time messaging with sellers

### For Sellers
- Product listing with images
- Seller verification system
- Order management
- Payout request system
- Product approval workflow

### For Administrators
- Seller verification management
- Product approval system
- Return dispute resolution
- Payout processing
- Platform analytics dashboard

## 🛠️ Technology Stack

- **Backend:** PHP
- **Database:** MySQL
- **Frontend:** HTML, CSS, JavaScript, Bootstrap
- **Server:** XAMPP (Development)
- **Version Control:** Git/GitHub

## 📁 Project Structure

```
boys_clothing_ecommerce/
├── admin/              # Admin dashboard and management pages
├── buyer/              # Buyer-specific pages (dashboard, orders, wishlist)
├── seller/             # Seller-specific pages (dashboard, products, orders)
├── chat/               # Messaging system
├── css/                # Stylesheets
├── js/                 # JavaScript files
├── includes/           # Common includes (header, footer, config)
├── Uploads/            # User-uploaded files (images, documents)
├── docs/               # Documentation (SRS, reports, wireframes)
├── database.sql        # Database schema
├── index.php           # Homepage
├── login.php           # Login page
├── register.php        # Registration page
└── README.md           # This file
```

## 🚀 Getting Started

### Prerequisites
- XAMPP (or similar PHP/MySQL environment)
- Web browser (Chrome, Firefox, Safari, Edge)
- Git (for version control)

### Installation

1. **Clone the repository**
   ```bash
   git clone [repository-url]
   cd boys_clothing_ecommerce
   ```

2. **Set up the database**
   - Start XAMPP and ensure MySQL is running
   - Import `database.sql` into phpMyAdmin
   - Database will be created as `boys_clothing`

3. **Configure database connection**
   - Update `includes/config.php` with your database credentials if needed

4. **Set up file permissions**
   - Ensure `Uploads/` directory has write permissions

5. **Access the application**
   - Navigate to `http://localhost/boys_clothing_ecommerce/`

### Default Admin Account
- **Email:** admin@boysclothing.com
- **Password:** admin123
- **Role:** Admin

*(Note: Change default password in production)*

## 📚 Documentation

- **Project Proposal:** `PROJECT_PROPOSAL.md`
- **Requirements Analysis:** `REQUIREMENTS_ANALYSIS.md`
- **Project Wireframe:** `PROJECT_WIREFRAME.md`
- **SRS Document:** `docs/SRS_Document.md`
- **Appendices:** `docs/Appendix_*.md`

## 🔐 Security Notes

- Passwords are currently stored in plain text for development/testing purposes
- **IMPORTANT:** Implement password hashing (bcrypt/Argon2) before production deployment
- All user inputs are sanitized to prevent SQL injection
- File uploads are validated for type and size
- Session management is implemented for secure authentication

## 📊 Database Schema

Key tables:
- `users` - User accounts (buyers, sellers, admins)
- `products` - Product listings
- `orders` - Order records
- `wishlist` - Buyer wishlists
- `cart` - Shopping cart items
- `messages` - Chat messages
- `returns` - Return requests
- `payouts` - Payout requests
- `notifications` - User notifications

See `database.sql` for complete schema.

## 🎨 User Roles

### Buyer
- Browse and search products
- Add to cart and wishlist
- Place orders
- Track orders
- Request returns
- Message sellers

### Seller
- Register and get verified
- List products
- Manage inventory
- Process orders
- Request payouts
- Handle returns

### Administrator
- Verify sellers
- Approve products
- Manage returns
- Process payouts
- View analytics

## 🔄 Development Workflow

1. **Requirement Analysis** ✅
2. **SRS Documentation** ✅
3. **System Design** (In Progress)
4. **Implementation** (In Progress)
5. **Testing**
6. **Deployment**
7. **Final Presentation**

## 📝 Project Status

- ✅ Project Initiation and Planning
- ✅ Software Requirements Specification (SRS)
- 🔄 System Design & Architecture (Next Phase)
- ⏳ Implementation
- ⏳ Testing
- ⏳ Final Report & Presentation

## 🤝 Contributing

This is an academic project. For questions or contributions, please contact the project team.

## 📄 License

This project is developed for educational purposes as part of the Software Engineering course.

## 📧 Contact

For inquiries about this project, please contact:
- [Group Contact Information]

---

**Last Updated:** Fall 2025  
**Version:** 1.0

