# System Design & Architecture - Summary for Main Report

## Overview

This section provides a condensed summary of the System Design & Architecture for the Second-Hand Boys Clothing E-Commerce Marketplace. The complete detailed design document is available separately.

---

## 1. System Architectural Design

### Architectural Pattern: Model-View-Controller (MVC)

**Justification:**
The MVC pattern was selected for its separation of concerns, which directly supports:
- **NFR32 (Scalability):** Modular structure allows independent development and maintenance
- **NFR7-NFR16 (Security):** Centralized data access in Model layer with prepared statements
- **NFR31-NFR34 (Scalability):** Easy feature additions without modifying existing code
- **Team Collaboration:** Enables parallel development across 4-member team
- **NFR1-NFR6 (Performance):** Efficient database query handling and optimized view rendering

### Architectural Components

**Client Layer:** Web browsers (desktop, tablet, mobile)

**Web Server Layer:** Apache (XAMPP)

**Application Layer (MVC):**
- **Controller:** UserController, ProductController, OrderController, Authentication
- **Model:** User Model, Product Model, Order Model, Database Access Layer (PDO)
- **View:** Templates (Header, Footer, Pages), Responsive Design (Bootstrap)

**Data Layer:**
- MySQL Database (11 tables)
- File Storage (Uploads/)

**External Systems:** Payment Gateway, Email Service (future)

---

## 2. Data and Process Flow Design

### Data Flow Diagram (DFD) - Level 0

The system interacts with three external entities:
- **Buyers:** Submit product requests, orders, returns, messages
- **Sellers:** Submit product listings, order updates, payout requests
- **Admins:** Make verification and approval decisions

External systems:
- Payment Gateway (external)
- Email Service (external, future)

### Key Processes (DFD Level 1)

1. User Management
2. Product Management
3. Order Management
4. Shopping & Cart
5. Return Management
6. Messaging System
7. Database Management
8. File Storage

### Flowcharts

**Flowchart 1: User Registration Process**
- User selects role → Fills form → Validation → Database insertion → Success/Error

**Flowchart 2: Product Purchase Workflow**
- Browse → Add to Cart → Checkout → Address Entry → Order Creation → Notification

**Flowchart 3: Admin Product Approval Process**
- Seller submits → Admin reviews → Approve/Reject → Notification to seller

*(See Appendix for all flowcharts)*

---

## 3. Initial Database Design (ERD)

### Database Structure

**11 Main Entities:**
1. users (PK: id)
2. products (PK: id, FK: seller_id)
3. orders (PK: id, FK: buyer_id, product_id)
4. wishlist (PK: id, FK: buyer_id, product_id)
5. cart (PK: id, FK: buyer_id, product_id)
6. messages (PK: id, FK: sender_id, receiver_id, product_id)
7. returns (PK: id, FK: order_id)
8. notifications (PK: id, FK: user_id, product_id)
9. seller_payout_requests (PK: id, FK: seller_id)
10. addresses (PK: id, FK: user_id)

### Key Relationships

- **users → products:** 1:* (One seller, many products)
- **users → orders:** 1:* (One buyer, many orders)
- **products → orders:** 1:* (One product, many orders until sold)
- **orders → returns:** 1:1 (One order, one return request)
- **users → messages:** 1:* (One user sends/receives many messages)

### Cardinality Summary

All relationships follow standard normalization (Third Normal Form) with proper foreign key constraints ensuring referential integrity.

---

## 4. UI/UX Design (Mature Wireframes)

### Wireframes Created (7 Screens)

1. **Login Page:** Authentication interface with email/password, register link
2. **Homepage/Product Listing:** Product grid, search, filters, pagination
3. **Product Detail Page:** Large image, details, seller info, action buttons
4. **Buyer Dashboard:** Orders, wishlist, messages summary
5. **Seller Dashboard:** Products, orders, payouts, returns management
6. **Admin Dashboard:** Platform statistics, pending approvals, analytics
7. **Checkout Page:** Shipping address form, order summary, payment

### Design Principles

- **Consistency:** Uniform header/footer, button styles
- **Responsiveness:** Desktop (4-col), Tablet (2-col), Mobile (1-col)
- **Usability:** Clear navigation, prominent CTAs
- **Accessibility:** High contrast, keyboard navigation, screen reader support

*(See Appendix for detailed wireframes)*

---

## 5. Technology Stack

| Layer | Technology | Justification |
|-------|-----------|---------------|
| **Frontend** | HTML5, CSS3, JavaScript | Universal browser support (NFR35-NFR37) |
| **CSS Framework** | Bootstrap 5.x | Mobile responsiveness (NFR18), consistent UI (NFR19) |
| **JavaScript** | jQuery 3.x | AJAX functionality, cross-browser compatibility |
| **Backend** | PHP 7.4+ / 8.x | PDO for security (NFR9), session management (NFR10) |
| **Database** | MySQL 8.0+ | Relational model, performance (NFR2), scalability (NFR31) |
| **Database Access** | PDO | Prepared statements (NFR8, NFR9), security |
| **Web Server** | Apache (XAMPP) | Production-ready, HTTPS support (NFR13) |

### Technology Justification Summary

**Frontend Technologies:**
- Address NFR18 (Mobile Responsiveness), NFR35-NFR37 (Browser Compatibility)
- Bootstrap ensures consistent UI (NFR19) and accessibility (NFR24)

**Backend Technologies:**
- PHP with PDO addresses NFR8-NFR9 (SQL Injection Prevention)
- Session management supports NFR10 (Secure Sessions)
- File upload handling supports FR14, FR16

**Database:**
- MySQL supports NFR2 (Query Performance <500ms)
- Relational structure supports NFR31 (Scalable Design)
- Foreign keys ensure data integrity

---

## Design Decisions Summary

### Architecture
- **MVC Pattern:** Chosen for separation of concerns, security, maintainability
- **Monolithic Structure:** Single application (appropriate for project scope)

### Database
- **Relational Database:** MySQL for structured data and relationships
- **Normalized Schema:** Third normal form to prevent redundancy
- **Foreign Key Constraints:** Ensure referential integrity

### Security
- **Prepared Statements:** PDO for all database queries
- **Session Management:** PHP sessions with secure configuration
- **Input Validation:** Server-side validation for all inputs

### UI/UX
- **Responsive Design:** Mobile-first approach using Bootstrap
- **Consistent Navigation:** Header/footer templates
- **Progressive Enhancement:** Core functionality without JavaScript

---

## Summary Statistics

- **Architectural Components:** 4 layers (Client, Web Server, Application, Data)
- **Database Tables:** 11 entities
- **Wireframes:** 7 key screens
- **Flowcharts:** 3 major processes
- **Technology Stack:** 7 core technologies

---

**Note:** This is a summary for the main Overleaf report. The complete detailed System Design document with full diagrams, ERD, and all wireframes is available in the separate System Design Template document.

