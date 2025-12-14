# System Design & Architecture Document

## Second-Hand Boys Clothing E-Commerce Marketplace

**Version:** 1.0  
**Date:** Fall 2025  
**Prepared by:** [Group Name]  
**Project:** Software Engineering Course Project

---

## Table of Contents

1. [System Architectural Design](#1-system-architectural-design)
2. [Data and Process Flow Design](#2-data-and-process-flow-design)
3. [Initial Database Design (ERD)](#3-initial-database-design-erd)
4. [UI/UX Design (Mature Wireframes)](#4-uiux-design-mature-wireframes)
5. [Technology Stack Backend Design](#5-technology-stack-backend-design)

---

## 1. System Architectural Design

### 1.1 Architectural Pattern Selection

**Selected Pattern: Model-View-Controller (MVC) Architecture**

The Second-Hand Boys Clothing E-Commerce Marketplace will be designed using the **Model-View-Controller (MVC)** architectural pattern. This pattern separates the application into three interconnected components:

- **Model:** Represents data and business logic (database interactions, data validation)
- **View:** Represents the user interface (HTML, CSS, JavaScript templates)
- **Controller:** Handles user input, processes requests, and coordinates between Model and View

### 1.2 Justification

The MVC pattern is the optimal choice for this project for several reasons:

**1. Separation of Concerns (NFR32 - Scalability)**
MVC provides clear separation between data management, business logic, and presentation. This modular structure supports NFR32 (Scalable Code Structure) by allowing independent development and maintenance of each component. Changes to the database schema won't affect the UI, and UI updates won't impact business logic.

**2. Security (NFR7-NFR16)**
The MVC pattern enhances security by centralizing data access in the Model layer. All database queries go through prepared statements (NFR9), input validation occurs in the Controller (NFR8), and the View layer is isolated from direct database access, preventing SQL injection attacks.

**3. Maintainability and Scalability (NFR31-NFR34)**
As the platform grows, MVC allows for easy feature additions without modifying existing code. New controllers can be added for new features, models can be extended, and views can be updated independently. This directly supports NFR31 (Database Design for Future Features) and NFR32 (Maintainable Code Structure).

**4. Team Collaboration**
With a 4-member team, MVC enables parallel development. One member can work on models, another on controllers, and another on views without conflicts, improving development efficiency.

**5. Performance (NFR1-NFR6)**
MVC supports performance optimization through efficient database query handling in the Model layer, caching strategies, and optimized view rendering, addressing NFR1 (Page Load Time <3 seconds) and NFR2 (Database Queries <500ms).

**6. Usability (NFR17-NFR24)**
The separation allows for consistent UI/UX across all views (NFR19 - Consistent Navigation) and enables responsive design implementation (NFR18 - Mobile Responsiveness) without affecting business logic.

### 1.3 Architectural Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                         CLIENT LAYER                             │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐         │
│  │   Browser    │  │   Browser    │  │   Browser    │         │
│  │  (Desktop)   │  │   (Tablet)   │  │   (Mobile)   │         │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘         │
│         │                 │                 │                  │
│         └─────────────────┴─────────────────┘                  │
│                           │                                     │
│                    HTTP/HTTPS Requests                          │
└───────────────────────────┼─────────────────────────────────────┘
                           │
┌───────────────────────────┼─────────────────────────────────────┐
│                    WEB SERVER LAYER                             │
│                    (Apache/XAMPP)                               │
│                           │                                     │
│                    PHP Request Handler                          │
└───────────────────────────┼─────────────────────────────────────┘
                           │
┌───────────────────────────┼─────────────────────────────────────┐
│              APPLICATION LAYER (MVC Architecture)                │
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │                    CONTROLLER LAYER                       │  │
│  │  ┌────────────┐  ┌────────────┐  ┌────────────┐         │  │
│  │  │   User    │  │  Product   │  │   Order    │         │  │
│  │  │Controller │  │Controller │  │Controller │         │  │
│  │  └─────┬─────┘  └─────┬─────┘  └─────┬─────┘         │  │
│  │        │               │               │              │  │
│  │  ┌─────┴───────────────┴───────────────┴─────┐         │  │
│  │  │      Authentication & Authorization       │         │  │
│  │  │      Session Management                   │         │  │
│  │  │      Input Validation & Sanitization      │         │  │
│  │  └──────────────────────────────────────────┘         │  │
│  └───────────────────────┬───────────────────────────────┘  │
│                          │                                    │
│  ┌───────────────────────┼───────────────────────────────┐  │
│  │                    MODEL LAYER                         │  │
│  │  ┌────────────┐  ┌────────────┐  ┌────────────┐       │  │
│  │  │   User     │  │  Product   │  │   Order    │       │  │
│  │  │   Model    │  │   Model   │  │   Model   │       │  │
│  │  └─────┬─────┘  └─────┬─────┘  └─────┬─────┘       │  │
│  │        │               │               │              │  │
│  │  ┌─────┴───────────────┴───────────────┴─────┐       │  │
│  │  │      Database Access Layer (PDO)          │       │  │
│  │  │      Business Logic                        │       │  │
│  │  │      Data Validation                      │       │  │
│  │  └───────────────────────┬───────────────────┘       │  │
│  └──────────────────────────┼────────────────────────────┘  │
│                             │                                │
│  ┌──────────────────────────┼────────────────────────────┐  │
│  │                    VIEW LAYER                          │  │
│  │  ┌────────────┐  ┌────────────┐  ┌────────────┐        │  │
│  │  │  Header   │  │   Pages    │  │  Footer   │        │  │
│  │  │ Template  │  │ Templates  │  │ Template  │        │  │
│  │  └───────────┘  └────────────┘  └───────────┘        │  │
│  │                                                       │  │
│  │  HTML + CSS + JavaScript (Bootstrap)                 │  │
│  │  Responsive Design                                    │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────┼───────────────────────────────┘
                              │
┌─────────────────────────────┼───────────────────────────────┐
│                    DATA LAYER                                │
│  ┌──────────────────────────────────────────────────────┐ │
│  │              MySQL DATABASE                            │ │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐            │ │
│  │  │  users   │  │ products│  │  orders  │            │ │
│  │  └──────────┘  └──────────┘  └──────────┘            │ │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐            │ │
│  │  │ wishlist │  │messages │  │ returns │            │ │
│  │  └──────────┘  └──────────┘  └──────────┘            │ │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐            │ │
│  │  │  cart    │  │notifications│payouts│            │ │
│  │  └──────────┘  └──────────┘  └──────────┘            │ │
│  └──────────────────────────────────────────────────────┘ │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐ │
│  │           FILE STORAGE (Uploads/)                     │ │
│  │  Product Images, Certificates, Laundry Memos         │ │
│  └──────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│              EXTERNAL SYSTEMS (Future)                       │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │   Payment    │  │    Email    │  │   Shipping  │      │
│  │   Gateway    │  │   Service   │  │   Service    │      │
│  │ (bKash/Rocket)│ │  (SMTP)     │  │  (API)      │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
```

### 1.4 Component Description

**Client Layer:**
- Web browsers (Chrome, Firefox, Safari, Edge)
- Responsive design supports desktop, tablet, and mobile devices
- JavaScript for AJAX functionality and dynamic interactions

**Web Server Layer:**
- Apache web server (via XAMPP)
- Handles HTTP/HTTPS requests
- Routes requests to PHP application

**Application Layer (MVC):**

**Controller Layer:**
- **UserController:** Handles registration, login, logout, profile management
- **ProductController:** Manages product CRUD operations, approval workflows
- **OrderController:** Processes orders, order status updates
- **Authentication & Authorization:** Session management, role-based access control
- **Input Validation:** Sanitizes and validates all user inputs

**Model Layer:**
- **User Model:** User data access, authentication logic
- **Product Model:** Product data operations, business rules
- **Order Model:** Order processing, status management
- **Database Access Layer:** PDO-based database interactions with prepared statements

**View Layer:**
- **Templates:** Reusable header, footer, navigation components
- **Page Templates:** Role-specific pages (buyer, seller, admin)
- **Responsive Design:** Bootstrap framework for mobile-first design

**Data Layer:**
- **MySQL Database:** Relational database storing all application data
- **File Storage:** Local file system for uploaded images and documents

**External Systems:**
- Payment processors (bKash, Rocket) - handled externally
- Email service (future) - for notifications
- Shipping service (future) - for delivery tracking

---

## 2. Data and Process Flow Design

### 2.1 Data Flow Diagram (DFD) - Level 0 (Context Diagram)

```
┌─────────────────────────────────────────────────────────────┐
│                                                               │
│                    EXTERNAL ENTITIES                         │
│                                                               │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐               │
│  │  Buyer   │    │  Seller  │    │  Admin  │               │
│  └────┬─────┘    └────┬─────┘    └────┬─────┘               │
│       │                │                │                     │
│       │                │                │                     │
│       │  Product       │  Product       │  Verification      │
│       │  Requests      │  Listings      │  Requests          │
│       │  Order         │  Order         │  Approval           │
│       │  Requests      │  Updates       │  Decisions          │
│       │                │                │                     │
│       └────────────────┴────────────────┘                     │
│                        │                                      │
│                        │                                      │
│        ┌───────────────▼───────────────┐                     │
│        │                               │                     │
│        │   SECOND-HAND CLOTHING       │                     │
│        │   E-COMMERCE MARKETPLACE     │                     │
│        │         SYSTEM                │                     │
│        │                               │                     │
│        └───────────────┬───────────────┘                     │
│                        │                                      │
│                        │                                      │
│       ┌────────────────┴────────────────┐                     │
│       │                                │                     │
│  ┌────▼────┐                    ┌─────▼────┐               │
│  │ Payment │                    │  Email   │               │
│  │Gateway  │                    │ Service  │               │
│  │(External)│                   │(External)│               │
│  └─────────┘                    └──────────┘               │
│                                                               │
└─────────────────────────────────────────────────────────────┘

Data Flows:
- Buyer → System: Product search requests, order requests, return requests, messages
- System → Buyer: Product listings, order confirmations, notifications
- Seller → System: Product listings, order updates, payout requests
- System → Seller: Order notifications, approval status, payout confirmations
- Admin → System: Verification decisions, approval decisions, payout processing
- System → Admin: Verification requests, approval requests, analytics data
- System → Payment Gateway: Payment processing requests
- System → Email Service: Notification emails
```

### 2.2 Data Flow Diagram (DFD) - Level 1

```
┌─────────────────────────────────────────────────────────────────┐
│                    SECOND-HAND CLOTHING                          │
│                   E-COMMERCE MARKETPLACE                        │
│                                                                  │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐     │
│  │  1.0 User    │    │ 2.0 Product  │    │ 3.0 Order    │     │
│  │  Management  │    │  Management  │    │  Management   │     │
│  │              │    │              │    │              │     │
│  │ Registration │    │  Product     │    │  Order       │     │
│  │ Login        │    │  Listing     │    │  Processing  │     │
│  │ Verification │    │  Approval     │    │  Tracking    │     │
│  └──────┬───────┘    └──────┬───────┘    └──────┬───────┘     │
│         │                    │                    │              │
│         │                    │                    │              │
│  ┌──────┴───────┐    ┌──────┴───────┐    ┌──────┴───────┐     │
│  │ 4.0 Shopping  │    │ 5.0 Return   │    │ 6.0 Messaging │     │
│  │  & Cart       │    │  Management  │    │  System      │     │
│  │               │    │              │    │              │     │
│  │ Cart          │    │  Return      │    │  Real-time   │     │
│  │ Wishlist      │    │  Requests    │    │  Chat        │     │
│  │ Checkout      │    │  Approval    │    │              │     │
│  └──────┬───────┘    └──────┬───────┘    └──────┬───────┘     │
│         │                    │                    │              │
│         └────────────────────┴────────────────────┘              │
│                            │                                     │
│                    ┌───────▼────────┐                            │
│                    │  7.0 Database  │                            │
│                    │   Management   │                            │
│                    │                │                            │
│                    │  Data Storage │                            │
│                    │  Retrieval     │                            │
│                    └───────┬────────┘                            │
│                            │                                     │
│                    ┌───────▼────────┐                            │
│                    │  8.0 File      │                            │
│                    │  Storage       │                            │
│                    │                │                            │
│                    │  Images        │                            │
│                    │  Documents     │                            │
│                    └────────────────┘                            │
└──────────────────────────────────────────────────────────────────┘

Process Descriptions:
1.0 User Management: Registration, authentication, seller verification
2.0 Product Management: Product listing, approval workflow, status management
3.0 Order Management: Order placement, status updates, order tracking
4.0 Shopping & Cart: Cart management, wishlist, checkout process
5.0 Return Management: Return requests, approval workflow
6.0 Messaging System: Real-time communication between users
7.0 Database Management: All data persistence operations
8.0 File Storage: Image and document storage
```

### 2.3 Flowcharts

#### Flowchart 1: User Registration Process

```
                    START
                      │
                      ▼
            ┌─────────────────────┐
            │  User Accesses      │
            │  Registration Page   │
            └──────────┬──────────┘
                       │
                       ▼
            ┌─────────────────────┐
            │  User Selects Role  │
            │  (Buyer/Seller)     │
            └──────────┬──────────┘
                       │
                       ▼
            ┌─────────────────────┐
            │  User Fills Form:   │
            │  - Username         │
            │  - Email            │
            │  - Password          │
            │  - [If Seller: NID  │
            │     & Certificate]   │
            └──────────┬──────────┘
                       │
                       ▼
            ┌─────────────────────┐
            │  Validate Input      │
            │  - Password strength │
            │  - Email format     │
            │  - Unique username  │
            └──────────┬──────────┘
                       │
                  ┌────┴────┐
                  │ Valid?  │
                  └────┬────┘
                  NO   │   YES
                  │    │    │
                  ▼    │    │
         ┌─────────────┘    │
         │                  │
         ▼                  ▼
┌────────────────┐  ┌──────────────────┐
│ Display Error  │  │ Check if Seller? │
│ Message        │  └────────┬───────────┘
└────────────────┘          │
                  NO         │ YES
                  │          │
                  ▼          ▼
         ┌──────────────────┴──────────────┐
         │                                  │
         ▼                                  ▼
┌──────────────────┐            ┌──────────────────┐
│ Set verified =    │            │ Set verified =   │
│ 'approved'        │            │ 'pending'        │
│ (Buyer)           │            │ (Seller)         │
└────────┬──────────┘            └────────┬──────────┘
         │                                │
         └────────────┬───────────────────┘
                      │
                      ▼
            ┌─────────────────────┐
            │  Insert User into    │
            │  Database            │
            └──────────┬──────────┘
                       │
                  ┌────┴────┐
                  │Success? │
                  └────┬────┘
                  NO   │   YES
                  │    │    │
                  ▼    │    │
         ┌─────────────┘    │
         │                  │
         ▼                  ▼
┌────────────────┐  ┌──────────────────┐
│ Display Error  │  │ Display Success  │
│ Message        │  │ Message          │
└────────────────┘  └────────┬─────────┘
                              │
                              ▼
                    ┌─────────────────────┐
                    │  Redirect to Login  │
                    │  (or Dashboard if   │
                    │   auto-login)       │
                    └──────────┬───────────┘
                              │
                              ▼
                            END
```

#### Flowchart 2: Product Purchase Workflow

```
                    START
                      │
                      ▼
            ┌─────────────────────┐
            │  Buyer Browses       │
            │  Products           │
            └──────────┬──────────┘
                       │
                       ▼
            ┌─────────────────────┐
            │  Buyer Views        │
            │  Product Details    │
            └──────────┬──────────┘
                       │
                  ┌────┴────┐
                  │ Add to  │
                  │ Cart?   │
                  └────┬────┘
                  YES  │  NO
                  │    │    │
                  ▼    │    │
         ┌─────────────┘    │
         │                  │
         ▼                  ▼
┌────────────────┐  ┌──────────────────┐
│ Add Product to │  │ Continue         │
│ Shopping Cart  │  │ Browsing         │
└────────┬───────┘  └──────────────────┘
         │
         ▼
┌─────────────────────┐
│  Buyer Views Cart   │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│  Buyer Clicks       │
│  "Checkout"         │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│  Check if Logged In │
└──────────┬──────────┘
           │
      ┌────┴────┐
      │Logged In?│
      └────┬────┘
      NO   │   YES
      │    │    │
      ▼    │    │
┌──────────┘    │
│               │
▼               ▼
┌──────────────────┐  ┌──────────────────┐
│ Redirect to      │  │ Display Checkout │
│ Login Page       │  │ Form             │
└──────────────────┘  └────────┬─────────┘
                                │
                                ▼
                    ┌─────────────────────┐
                    │  Buyer Enters        │
                    │  Shipping Address    │
                    └──────────┬──────────┘
                               │
                               ▼
                    ┌─────────────────────┐
                    │  Validate Address   │
                    │  Information        │
                    └──────────┬──────────┘
                               │
                          ┌────┴────┐
                          │ Valid?  │
                          └────┬────┘
                          NO   │   YES
                          │    │    │
                          ▼    │    │
                 ┌─────────────┘    │
                 │                  │
                 ▼                  ▼
        ┌────────────────┐  ┌──────────────────┐
        │ Display Error  │  │ Create Order     │
        │ Message        │  │ in Database      │
        └────────────────┘  └────────┬─────────┘
                                     │
                                     ▼
                        ┌─────────────────────┐
                        │  Update Product     │
                        │  Status to 'sold'   │
                        └──────────┬──────────┘
                                   │
                                   ▼
                        ┌─────────────────────┐
                        │  Clear Shopping     │
                        │  Cart               │
                        └──────────┬──────────┘
                                   │
                                   ▼
                        ┌─────────────────────┐
                        │  Send Notification   │
                        │  to Seller           │
                        └──────────┬──────────┘
                                   │
                                   ▼
                        ┌─────────────────────┐
                        │  Display Order      │
                        │  Confirmation       │
                        └──────────┬──────────┘
                                   │
                                   ▼
                                 END
```

#### Flowchart 3: Admin Product Approval Process

```
                    START
                      │
                      ▼
            ┌─────────────────────┐
            │  Seller Submits     │
            │  New Product        │
            └──────────┬──────────┘
                       │
                       ▼
            ┌─────────────────────┐
            │  System Sets Status  │
            │  to 'pending'        │
            └──────────┬──────────┘
                       │
                       ▼
            ┌─────────────────────┐
            │  Admin Logs In      │
            │  to Dashboard       │
            └──────────┬──────────┘
                       │
                       ▼
            ┌─────────────────────┐
            │  Admin Views        │
            │  Pending Products   │
            └──────────┬──────────┘
                       │
                       ▼
            ┌─────────────────────┐
            │  Admin Selects       │
            │  Product to Review  │
            └──────────┬──────────┘
                       │
                       ▼
            ┌─────────────────────┐
            │  Admin Reviews:      │
            │  - Product Info      │
            │  - Images            │
            │  - Seller Info       │
            │  - [If Hygiene:      │
            │     Laundry Memo]    │
            └──────────┬──────────┘
                       │
                  ┌────┴────┐
                  │ Meets   │
                  │ Quality │
                  │ Standards?│
                  └────┬────┘
                  NO   │   YES
                  │    │    │
                  ▼    │    │
         ┌─────────────┘    │
         │                  │
         ▼                  ▼
┌────────────────┐  ┌──────────────────┐
│ Admin Rejects  │  │ Admin Approves │
│ Product        │  │ Product        │
└────────┬───────┘  └────────┬────────┘
         │                  │
         ▼                  ▼
┌────────────────┐  ┌──────────────────┐
│ Update Status  │  │ Update Status   │
│ to 'rejected'  │  │ to 'approved'   │
└────────┬───────┘  └────────┬────────┘
         │                  │
         ▼                  ▼
┌────────────────┐  ┌──────────────────┐
│ Create         │  │ Create          │
│ Rejection      │  │ Approval        │
│ Notification   │  │ Notification    │
└────────┬───────┘  └────────┬────────┘
         │                  │
         └────────┬──────────┘
                   │
                   ▼
        ┌─────────────────────┐
        │  Send Notification   │
        │  to Seller           │
        └──────────┬──────────┘
                   │
                   ▼
        ┌─────────────────────┐
        │  [If Approved]       │
        │  Product Visible to  │
        │  Buyers              │
        └──────────┬──────────┘
                   │
                   ▼
                 END
```

---

## 3. Initial Database Design (ERD)

### 3.1 Entity-Relationship Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                      DATABASE ERD                                │
│                                                                  │
│  ┌──────────────┐                                               │
│  │    users     │                                               │
│  ├──────────────┤                                               │
│  │ PK id        │                                               │
│  │    role      │                                               │
│  │    username  │                                               │
│  │    password  │                                               │
│  │    email     │                                               │
│  │    nid       │                                               │
│  │    certificate│                                              │
│  │    verified  │                                               │
│  │    created_at│                                               │
│  └──────┬───────┘                                               │
│         │                                                       │
│         │ 1                                                     │
│         │                                                       │
│         │                                                       │
│    ┌────┴────┐                                            ┌────┴────┐
│    │    *    │                                            │    *     │
│    └────┬────┘                                            └────┬─────┘
│         │                                                       │
│         │                                                       │
│  ┌──────▼───────┐                                       ┌──────▼───────┐
│  │   products   │                                       │    orders    │
│  ├──────────────┤                                       ├──────────────┤
│  │ PK id        │                                       │ PK id        │
│  │ FK seller_id │◄──────┐                              │ FK buyer_id │
│  │    title     │       │                              │ FK product_id│
│  │    description│      │                              │    status    │
│  │    category  │       │                              │    created_at│
│  │    size      │       │                              └──────────────┘
│  │    condition │       │                                       │
│  │    price     │       │                                       │ 1
│  │    images    │       │                                       │
│  │    laundry_memo│     │                                       │
│  │    hygiene_verified│ │                                       │
│  │    status    │       │                                       │
│  │    created_at│       │                                       │
│  └──────────────┘       │                                       │
│                          │                                       │
│                          │                                       │
│  ┌───────────────────────┴───────┐                              │
│  │                                 │                              │
│  │                                 │                              │
│  │  ┌──────────────┐      ┌───────▼───────┐                    │
│  │  │   wishlist   │      │    returns     │                    │
│  │  ├──────────────┤      ├───────────────┤                    │
│  │  │ PK id        │      │ PK id         │                    │
│  │  │ FK buyer_id  │      │ FK order_id   │                    │
│  │  │ FK product_id│      │    reason     │                    │
│  │  │    created_at│      │    status     │                    │
│  │  └──────────────┘      │    admin_decision│                 │
│  │                        │    created_at │                    │
│  │                        └────────────────┘                    │
│  │                                                               │
│  │  ┌──────────────┐      ┌──────────────┐                     │
│  │  │     cart     │      │   messages   │                     │
│  │  ├──────────────┤      ├──────────────┤                     │
│  │  │ PK id        │      │ PK id        │                     │
│  │  │ FK buyer_id  │      │ FK sender_id │                     │
│  │  │ FK product_id│      │ FK receiver_id│                    │
│  │  │    quantity  │      │ FK product_id │                     │
│  │  │    added_at  │      │    message   │                     │
│  │  └──────────────┘      │    created_at │                    │
│  │                        └──────────────┘                     │
│  │                                                               │
│  │  ┌──────────────┐      ┌──────────────┐                     │
│  │  │ notifications│      │ seller_payout_│                     │
│  │  ├──────────────┤      │   requests    │                     │
│  │  │ PK id        │      ├──────────────┤                     │
│  │  │ FK user_id   │      │ PK id        │                     │
│  │  │ FK product_id│      │ FK seller_id │                     │
│  │  │    message   │      │    amount     │                     │
│  │  │    type      │      │    method     │                     │
│  │  │    is_read   │      │    phone_number│                   │
│  │  │    created_at│      │    status     │                     │
│  │  └──────────────┘      │    requested_at│                   │
│  │                        │    processed_at│                    │
│  │                        └──────────────┘                     │
│  │                                                               │
│  │  ┌──────────────┐                                            │
│  │  │   addresses  │                                            │
│  │  ├──────────────┤                                            │
│  │  │ PK id        │                                            │
│  │  │ FK user_id   │                                            │
│  │  │    full_name │                                            │
│  │  │    address_line│                                         │
│  │  │    city      │                                            │
│  │  │    postal_code│                                          │
│  │  │    phone     │                                            │
│  │  │    created_at│                                            │
│  │  └──────────────┘                                            │
│  └──────────────────────────────────────────────────────────────┘
└─────────────────────────────────────────────────────────────────┘

Relationship Legend:
- ──── : One-to-Many (1:*)
- PK   : Primary Key
- FK   : Foreign Key
```

### 3.2 Entity Descriptions

#### users
- **Primary Key:** id
- **Attributes:** role, username, password, email, nid, certificate, verified, created_at
- **Relationships:**
  - One-to-Many with products (seller_id)
  - One-to-Many with orders (buyer_id)
  - One-to-Many with wishlist (buyer_id)
  - One-to-Many with cart (buyer_id)
  - One-to-Many with messages (sender_id, receiver_id)
  - One-to-Many with returns (via orders)
  - One-to-Many with notifications (user_id)
  - One-to-Many with seller_payout_requests (seller_id)
  - One-to-Many with addresses (user_id)

#### products
- **Primary Key:** id
- **Foreign Keys:** seller_id → users(id)
- **Attributes:** title, description, category, size, item_condition, price, images, laundry_memo, hygiene_verified, status, created_at
- **Relationships:**
  - Many-to-One with users (seller)
  - One-to-Many with orders
  - One-to-Many with wishlist
  - One-to-Many with cart
  - One-to-Many with messages
  - One-to-Many with notifications

#### orders
- **Primary Key:** id
- **Foreign Keys:** buyer_id → users(id), product_id → products(id)
- **Attributes:** status, created_at
- **Relationships:**
  - Many-to-One with users (buyer)
  - Many-to-One with products
  - One-to-One with returns

#### wishlist
- **Primary Key:** id
- **Foreign Keys:** buyer_id → users(id), product_id → products(id)
- **Attributes:** created_at
- **Relationships:**
  - Many-to-One with users (buyer)
  - Many-to-One with products

#### cart
- **Primary Key:** id
- **Foreign Keys:** buyer_id → users(id), product_id → products(id)
- **Attributes:** quantity, added_at
- **Relationships:**
  - Many-to-One with users (buyer)
  - Many-to-One with products

#### messages
- **Primary Key:** id
- **Foreign Keys:** sender_id → users(id), receiver_id → users(id), product_id → products(id)
- **Attributes:** message, created_at
- **Relationships:**
  - Many-to-One with users (sender)
  - Many-to-One with users (receiver)
  - Many-to-One with products

#### returns
- **Primary Key:** id
- **Foreign Keys:** order_id → orders(id)
- **Attributes:** reason, status, admin_decision, created_at
- **Relationships:**
  - Many-to-One with orders

#### notifications
- **Primary Key:** id
- **Foreign Keys:** user_id → users(id), product_id → products(id)
- **Attributes:** message, type, is_read, created_at
- **Relationships:**
  - Many-to-One with users
  - Many-to-One with products

#### seller_payout_requests
- **Primary Key:** id
- **Foreign Keys:** seller_id → users(id)
- **Attributes:** amount, method, phone_number, status, requested_at, processed_at
- **Relationships:**
  - Many-to-One with users (seller)

#### addresses
- **Primary Key:** id
- **Foreign Keys:** user_id → users(id)
- **Attributes:** full_name, address_line, city, postal_code, phone, created_at
- **Relationships:**
  - Many-to-One with users

### 3.3 Cardinality Summary

| Relationship | Cardinality | Description |
|--------------|-------------|-------------|
| users → products | 1:* | One user (seller) can have many products |
| users → orders | 1:* | One user (buyer) can have many orders |
| users → wishlist | 1:* | One user (buyer) can have many wishlist items |
| users → cart | 1:* | One user (buyer) can have many cart items |
| users → messages (sender) | 1:* | One user can send many messages |
| users → messages (receiver) | 1:* | One user can receive many messages |
| products → orders | 1:* | One product can be ordered many times (until sold) |
| products → wishlist | 1:* | One product can be in many wishlists |
| products → cart | 1:* | One product can be in many carts |
| products → messages | 1:* | One product can have many messages |
| orders → returns | 1:1 | One order can have one return request |

---

## 4. UI/UX Design (Mature Wireframes)

*(Note: Detailed wireframes are provided in separate files. See `docs/Wireframes_Detailed.md`)*

### 4.1 Wireframe Overview

The following key screens have been designed with mature wireframes:

1. **Login Page** - User authentication interface
2. **Homepage/Product Listing** - Main product browsing page
3. **Product Detail Page** - Individual product view
4. **Buyer Dashboard** - Buyer's personal dashboard
5. **Seller Dashboard** - Seller's management dashboard
6. **Admin Dashboard** - Administrator control panel
7. **Checkout Page** - Order placement interface

Each wireframe includes:
- Complete layout structure
- Navigation elements
- Form fields and buttons
- Content placement
- Responsive design considerations

*(See Appendix for detailed wireframes)*

---

## 5. Technology Stack Backend Design

### 5.1 Selected Technology Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| **Frontend** | HTML5, CSS3, JavaScript (ES6+) | Latest |
| **CSS Framework** | Bootstrap | 5.x |
| **JavaScript Library** | jQuery | 3.x |
| **Backend** | PHP | 7.4+ / 8.x |
| **Database** | MySQL | 8.0+ |
| **Web Server** | Apache (XAMPP) | 2.4+ |
| **Database Access** | PDO (PHP Data Objects) | Built-in |
| **Version Control** | Git/GitHub | Latest |

### 5.2 Technology Justification

#### Frontend: HTML5, CSS3, JavaScript

**Justification:**
- **NFR18 (Mobile Responsiveness):** HTML5 and CSS3 provide native responsive design capabilities. Modern CSS Grid and Flexbox enable responsive layouts without additional frameworks.
- **NFR35-NFR37 (Browser Compatibility):** HTML5 and CSS3 are universally supported across all major browsers (Chrome, Firefox, Safari, Edge), ensuring compatibility.
- **NFR17 (Usability):** Semantic HTML5 elements improve accessibility and user experience.
- **Simplicity:** For a web application, native web technologies provide the best performance and compatibility without the overhead of complex frameworks.

#### CSS Framework: Bootstrap 5.x

**Justification:**
- **NFR18 (Mobile Responsiveness):** Bootstrap's responsive grid system ensures the application works seamlessly on desktop, tablet, and mobile devices (320px+), directly addressing NFR18 and NFR37.
- **NFR19 (Consistent Navigation):** Bootstrap provides consistent UI components (navbar, buttons, forms) ensuring uniform navigation and interface elements across all pages.
- **NFR17 (Intuitive Interface):** Bootstrap's pre-designed components are familiar to users, reducing learning curve and improving usability.
- **Development Speed:** Pre-built components accelerate development, important for semester timeline constraints.
- **Accessibility:** Bootstrap includes ARIA attributes and keyboard navigation support, supporting NFR24 (Keyboard Navigation).

#### JavaScript Library: jQuery 3.x

**Justification:**
- **AJAX Functionality:** jQuery simplifies AJAX requests for real-time features like messaging (FR69-FR74) and wishlist updates (FR45-FR48).
- **DOM Manipulation:** Simplifies dynamic content updates without page reloads, improving NFR1 (Page Load Performance).
- **Cross-Browser Compatibility:** jQuery handles browser differences, supporting NFR35 (Browser Compatibility).
- **Lightweight:** jQuery is lightweight compared to full frameworks, maintaining fast page loads (NFR1).

#### Backend: PHP 7.4+ / 8.x

**Justification:**
- **NFR9 (SQL Injection Prevention):** PHP's PDO extension provides prepared statements, essential for preventing SQL injection attacks.
- **NFR10 (Session Management):** PHP has built-in session management capabilities, supporting secure authentication (FR5-FR7).
- **Server-Side Processing:** PHP handles server-side logic, file uploads (FR14, FR16), and database operations efficiently.
- **Development Environment:** XAMPP provides easy local development setup, supporting rapid development.
- **Wide Adoption:** PHP is widely used for web applications, with extensive documentation and community support.

#### Database: MySQL 8.0+

**Justification:**
- **Relational Data:** MySQL's relational model supports complex relationships between users, products, orders, and other entities (ERD requirements).
- **NFR2 (Database Performance):** MySQL provides indexing, query optimization, and efficient data retrieval, supporting <500ms query execution requirement.
- **Data Integrity:** Foreign key constraints ensure referential integrity, preventing orphaned records.
- **Scalability:** MySQL supports future growth (NFR31, NFR33), with ability to handle increasing data volumes.
- **XAMPP Integration:** MySQL is included in XAMPP, simplifying development setup.

#### Database Access: PDO (PHP Data Objects)

**Justification:**
- **NFR8 (SQL Injection Prevention):** PDO's prepared statements are the industry standard for preventing SQL injection attacks.
- **NFR9 (Prepared Statements):** PDO enforces use of prepared statements, directly addressing security requirement.
- **Database Abstraction:** PDO provides database-agnostic interface, allowing potential future database migration.
- **Error Handling:** PDO provides robust error handling and exception management.

#### Web Server: Apache (XAMPP)

**Justification:**
- **Development Environment:** XAMPP provides integrated Apache, MySQL, and PHP, simplifying local development.
- **Production Ready:** Apache is production-ready and widely used, supporting NFR25 (System Uptime).
- **HTTPS Support:** Apache supports SSL/TLS for HTTPS in production (NFR13).
- **Performance:** Apache handles concurrent requests efficiently, supporting NFR4 (100+ concurrent users).

### 5.3 Key APIs/Services (Planned)

#### Payment Gateway (Future)
- **bKash API / Rocket API:** For payment processing (FR75-FR82)
- **Status:** External service, not integrated in initial version
- **Justification:** Payment processing requires third-party integration. Manual payout processing in initial version.

#### Email Service (Future)
- **SMTP Service (e.g., SendGrid, Mailgun):** For email notifications (FR83-FR88)
- **Status:** Planned for future implementation
- **Justification:** Email notifications enhance user experience but are not critical for initial version.

### 5.4 Technology Stack Summary

The selected technology stack directly addresses all non-functional requirements:

- **Performance (NFR1-NFR6):** Lightweight stack ensures fast page loads
- **Security (NFR7-NFR16):** PDO, PHP session management, input validation
- **Usability (NFR17-NFR24):** Bootstrap ensures responsive, accessible interface
- **Reliability (NFR25-NFR30):** Proven technologies with wide adoption
- **Scalability (NFR31-NFR34):** MySQL and modular PHP structure support growth
- **Compatibility (NFR35-NFR38):** Universal browser support

---

## 6. Design Decisions Summary

### 6.1 Architecture Decisions

1. **MVC Pattern:** Chosen for separation of concerns, security, and maintainability
2. **Monolithic Structure:** Single web application (not microservices) due to project scope and team size
3. **Role-Based Directories:** Separate directories for admin, buyer, seller for clear organization

### 6.2 Database Decisions

1. **Relational Database:** MySQL chosen for structured data and relationships
2. **Normalized Schema:** Third normal form to prevent data redundancy
3. **Foreign Key Constraints:** Ensure referential integrity

### 6.3 Security Decisions

1. **Prepared Statements:** PDO for all database queries
2. **Session Management:** PHP sessions with secure configuration
3. **Input Validation:** Server-side validation for all user inputs
4. **File Upload Validation:** Type, size, and content validation

### 6.4 UI/UX Decisions

1. **Responsive Design:** Mobile-first approach using Bootstrap
2. **Consistent Navigation:** Header/footer templates across all pages
3. **Progressive Enhancement:** Core functionality works without JavaScript

---

**Document End**

