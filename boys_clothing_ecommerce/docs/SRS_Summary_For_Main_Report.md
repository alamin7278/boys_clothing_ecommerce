# SRS Summary for Main Overleaf Report

## Software Requirements Specification - Summary

### Overview

This section provides a condensed summary of the Software Requirements Specification for the Second-Hand Boys Clothing E-Commerce Marketplace. The complete detailed SRS document is available in the separate SRS Template document.

---

## 1. Functional Requirements

The system provides comprehensive functionality across ten major areas:

### User Management (FR1-FR12)
- User registration with role selection (Buyer, Seller, Admin)
- Secure authentication and session management
- Seller verification workflow with NID and certificate requirements
- Admin approval/rejection of seller verification requests

### Product Management (FR13-FR28)
- Product listing with images, categories, sizes, and conditions
- Product approval workflow
- Hygiene product verification (laundry memo requirement)
- Product status tracking (available/sold)

### Product Browsing and Search (FR29-FR39)
- Product browsing on homepage
- Keyword search functionality
- Filtering by category, size, condition, and price range
- Detailed product view pages

### Shopping Features (FR40-FR49)
- Shopping cart management
- Wishlist functionality
- Checkout process

### Order Management (FR50-FR60)
- Order placement with shipping address
- Order tracking (pending, shipped, delivered)
- Order history for buyers and sellers

### Return Management (FR61-FR68)
- Return request submission
- Return approval/rejection workflow
- Return status tracking

### Communication (FR69-FR74)
- Real-time messaging between buyers and sellers
- Product-linked conversations

### Payment and Payouts (FR75-FR82)
- Payout request submission
- Payout processing and tracking
- Multiple payment methods (bKash, Rocket, Bank)

### Notifications (FR83-FR88)
- Product approval/rejection notifications
- Order placement notifications
- Notification management

### Admin Dashboard (FR89-FR95)
- Platform analytics and statistics
- Seller verification management
- Product approval management
- Return dispute resolution
- Payout processing

**Total Functional Requirements: 95 (FR1-FR95)**

---

## 2. Non-Functional Requirements

### Performance (NFR1-NFR6)
- Page load time: <3 seconds for 95% of requests
- Database queries: <500ms execution time
- Support for 100+ concurrent users
- Image uploads: up to 5MB per file

### Security (NFR7-NFR16)
- Password hashing (bcrypt/Argon2)
- SQL injection prevention (prepared statements)
- Secure session management
- File upload validation
- Role-based access control
- HTTPS for production
- CSRF protection

### Usability (NFR17-NFR24)
- Intuitive user interface
- Mobile-responsive design (320px minimum)
- Consistent navigation
- Clear error messages
- Form validation feedback

### Reliability (NFR25-NFR30)
- 99% uptime during business hours
- Regular database backups
- Error logging
- Graceful failure handling

### Scalability (NFR31-NFR34)
- Modular code structure
- Scalable database design
- Organized file storage

### Compatibility (NFR35-NFR38)
- Browser compatibility (Chrome, Firefox, Safari, Edge)
- Responsive design (desktop, tablet, mobile)
- JavaScript-enabled functionality

**Total Non-Functional Requirements: 38 (NFR1-NFR38)**

---

## 3. User Stories

**US1:** As a buyer, I want to browse available products on the homepage so that I can quickly see what clothing items are available for purchase.

**US2:** As a buyer, I want to add products to my wishlist so that I can save items I'm interested in for later purchase.

**US3:** As a buyer, I want to search for products by keywords so that I can find specific items I'm looking for.

**US4:** As a buyer, I want to view my order history and track order status so that I know when my purchases will be delivered.

**US5:** As a seller, I want to upload product images and details so that buyers can see what I'm selling and make informed decisions.

**US6:** As a seller, I want to receive notifications when my products are approved so that I know when my listings go live.

**US7:** As a seller, I want to request payouts for my completed orders so that I can receive payment for my sales.

**US8:** As a buyer, I want to message sellers about products so that I can ask questions before making a purchase.

**US9:** As a buyer, I want to request returns for delivered orders so that I can return items that don't meet my expectations.

**US10:** As an administrator, I want to approve or reject seller verification requests so that only legitimate sellers can list products on the platform.

**US11:** As an administrator, I want to view platform analytics so that I can monitor the health and growth of the marketplace.

**Total User Stories: 11**

---

## 4. Requirement Analysis Methodology

### Methods Used

**Option A: Questionnaires and Interviews**
- Developed 10-question structured questionnaire
- Distributed to 25 potential users (buyers, sellers, e-commerce users)
- Key findings:
  - 85% emphasized seller verification importance
  - 72% wanted direct messaging with sellers
  - 80% considered return policy critical
  - 88% preferred mobile-responsive design

**Option C: Self-Research and Peer Comparison**
- Analyzed similar platforms: Facebook Marketplace, Daraz, OLX
- Compared with peer SE projects
- Identified feature gaps and best practices
- Adopted: Product approval workflow, real-time messaging, admin dashboard structure

### Key Insights

1. **Trust Mechanisms:** Seller verification and product approval are essential
2. **Communication:** Direct messaging is highly valued
3. **Mobile-First:** Responsive design is critical
4. **Return Policy:** Critical for user trust
5. **Hygiene Verification:** Special handling needed for hygiene products

*(See Appendix for detailed questionnaire responses, comparative analysis, and peer discussion summaries)*

---

## 5. Ethical and Legal Considerations

### Data Privacy and User Consent
- Explicit consent for data collection
- Secure password hashing and encrypted storage
- No third-party data sharing without consent
- User data used solely for platform functionality

### Fairness and Bias Prevention
- Equal access for all users
- Standardized seller verification process
- Unbiased product listing display
- Fair content moderation

### Accessibility
- WCAG 2.1 Level AA compliance (color contrast, text readability)
- Keyboard navigation support
- Clear error messages and form labels
- Responsive design for various screen sizes

### Intellectual Property
- User ownership of uploaded content
- Platform license for display purposes
- Compliance with open-source licenses
- Copyright infringement handling

### Legal Compliance
- E-commerce regulations compliance
- Consumer protection measures
- Data protection law compliance
- Transparent terms and policies

---

## Summary Statistics

- **Functional Requirements:** 95
- **Non-Functional Requirements:** 38
- **User Stories:** 11
- **Questionnaire Respondents:** 25
- **Systems Analyzed:** 5 (Facebook Marketplace, Daraz, OLX, 2 peer projects)
- **Peer Groups Consulted:** 4

---

**Note:** This is a summary for the main Overleaf report. The complete detailed SRS document with full descriptions, rationale, and appendices is available in the separate SRS Template document.

