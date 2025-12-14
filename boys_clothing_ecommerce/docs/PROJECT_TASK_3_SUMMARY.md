# Project Task #3: System Design & Architecture - Completion Summary

## ✅ Completed Deliverables

### 1. System Architectural Design

**Location:** `docs/System_Design_Document.md` (Section 1)

**Contents:**
- ✅ Architectural Pattern Selected: Model-View-Controller (MVC)
- ✅ Justification (2 paragraphs) linking to NFRs
- ✅ Architectural Diagram (Component Diagram)
- ✅ Component descriptions for all layers

**Key Points:**
- MVC pattern chosen for separation of concerns, security, scalability
- 4-layer architecture: Client, Web Server, Application (MVC), Data
- Justification references NFR32, NFR7-NFR16, NFR31-NFR34

---

### 2. Data and Process Flow Design

**Location:** `docs/System_Design_Document.md` (Section 2)

**Contents:**
- ✅ DFD Level 0 (Context Diagram) - System and external entities
- ✅ DFD Level 1 - Breakdown of main processes
- ✅ Flowchart 1: User Registration Process
- ✅ Flowchart 2: Product Purchase Workflow
- ✅ Flowchart 3: Admin Product Approval Process

**Key Points:**
- 3 external entities: Buyers, Sellers, Admins
- 8 main processes identified
- 3 detailed flowcharts for key workflows

---

### 3. Initial Database Design (ERD)

**Location:** `docs/System_Design_Document.md` (Section 3)

**Contents:**
- ✅ Entity-Relationship Diagram (ERD)
- ✅ 11 entities with all attributes listed
- ✅ Primary Keys (PK) and Foreign Keys (FK) identified
- ✅ Relationships and cardinality specified
- ✅ Entity descriptions provided

**Key Entities:**
- users, products, orders, wishlist, cart, messages, returns, notifications, seller_payout_requests, addresses

**Key Relationships:**
- users → products: 1:*
- users → orders: 1:*
- products → orders: 1:*
- orders → returns: 1:1

---

### 4. UI/UX Design (Mature Wireframes)

**Location:** `docs/Wireframes_Detailed.md`

**Contents:**
- ✅ 7 Key Screens with detailed wireframes:
  1. Login Page
  2. Homepage/Product Listing
  3. Product Detail Page
  4. Buyer Dashboard
  5. Seller Dashboard
  6. Admin Dashboard
  7. Checkout Page

**Key Features:**
- Complete layout structure
- Navigation elements
- Form fields and buttons
- Content placement
- Responsive design considerations
- Design principles applied

---

### 5. Technology Stack Backend Design

**Location:** `docs/System_Design_Document.md` (Section 5)

**Contents:**
- ✅ Frontend: HTML5, CSS3, JavaScript, Bootstrap 5.x, jQuery 3.x
- ✅ Backend: PHP 7.4+ / 8.x
- ✅ Database: MySQL 8.0+
- ✅ Database Access: PDO
- ✅ Web Server: Apache (XAMPP)
- ✅ Justification for each technology linking to NFRs

**Justification Summary:**
- Frontend: Browser compatibility (NFR35-NFR37), mobile responsiveness (NFR18)
- Backend: Security (NFR8-NFR9), session management (NFR10)
- Database: Performance (NFR2), scalability (NFR31)

---

### 6. Trello Board Update

**Location:** `docs/TRELLO_DESIGN_PHASE_GUIDE.md`

**Contents:**
- ✅ Trello board setup guide for Design Phase
- ✅ List structure (Backlog, To Do, In Progress, Review, Done)
- ✅ Sample cards for all design tasks
- ✅ Team member task assignments
- ✅ Checklist items
- ✅ Screenshot instructions

**Next Steps:**
- Team to create Trello board: "Second-Hand Boys Clothing E-Commerce - SE Design Phase"
- Populate with tasks from guide
- Take screenshots for Appendix D

---

### 7. Summary Document for Main Report

**Location:** `docs/System_Design_Summary_For_Main_Report.md`

**Contents:**
- ✅ Condensed version for Overleaf main report
- ✅ Architecture summary
- ✅ DFD summary
- ✅ ERD summary
- ✅ Wireframes summary
- ✅ Technology stack summary

---

## 📊 Statistics

- **Architectural Components:** 4 layers
- **Database Entities:** 11 tables
- **Wireframes:** 7 screens
- **Flowcharts:** 3 major processes
- **DFD Levels:** 2 (Level 0 and Level 1)
- **Technology Stack Items:** 7 core technologies
- **Design Principles:** 5 applied

---

## 📋 Checklist for Final Submission

### Main Overleaf Report
- [x] System Design & Architecture section
- [x] Architectural Diagram and justification
- [x] Technology Stack justification
- [x] DFD Level 0 (Context Diagram)
- [x] One example Flowchart (Product Purchase)
- [x] Initial ERD

### Appendix
- [x] All other Flowcharts (User Registration, Admin Approval)
- [x] All UI/UX wireframes (7 screens)
- [ ] Trello board screenshot for Design Phase - *Team to add*

### Separate Design Document
- [x] Complete System Design Document
- [x] All diagrams included
- [x] All wireframes included
- [x] Complete technology stack documentation

---

## 📝 Next Steps for Team

### Immediate Actions Required:

1. **Trello Board Setup**
   - Create board: "Second-Hand Boys Clothing E-Commerce - SE Design Phase"
   - Follow guide in `docs/TRELLO_DESIGN_PHASE_GUIDE.md`
   - Add all design tasks
   - Assign to team members
   - Take screenshots for Appendix D

2. **Review Design Documents**
   - Team review of System Design Document
   - Validate all diagrams
   - Check wireframes for completeness
   - Ensure technology stack is appropriate

3. **Create Visual Diagrams**
   - Use tools (draw.io, Figma, etc.) to create visual versions of:
     - Architectural Diagram
     - DFD Level 0 and Level 1
     - Flowcharts
     - ERD
   - Export as images for documentation

4. **Wireframe Creation**
   - Use Figma, Balsamiq, or draw.io to create visual wireframes
   - Export as images
   - Include in documentation

5. **Documentation Finalization**
   - Compile all design artifacts
   - Add Trello screenshots
   - Finalize design document
   - Prepare summary for main report

---

## 📁 File Structure

```
docs/
├── System_Design_Document.md              # Complete design document
├── System_Design_Summary_For_Main_Report.md  # Summary for main report
├── Wireframes_Detailed.md                 # Detailed wireframes
├── TRELLO_DESIGN_PHASE_GUIDE.md          # Trello setup guide
└── PROJECT_TASK_3_SUMMARY.md              # This file
```

---

## 🎯 Design Decisions Summary

### Architecture
- **MVC Pattern:** Chosen for separation of concerns, security, maintainability
- **Monolithic Structure:** Single web application (appropriate for scope)

### Database
- **Relational Database:** MySQL for structured data
- **Normalized Schema:** Third normal form
- **Foreign Key Constraints:** Referential integrity

### Security
- **Prepared Statements:** PDO for all queries
- **Session Management:** PHP secure sessions
- **Input Validation:** Server-side validation

### UI/UX
- **Responsive Design:** Mobile-first with Bootstrap
- **Consistent Navigation:** Header/footer templates
- **Progressive Enhancement:** Core functionality without JS

---

## 📝 Notes

1. **Visual Diagrams:** Create visual versions using diagramming tools (draw.io recommended)
2. **Wireframes:** Create visual wireframes using Figma, Balsamiq, or similar
3. **Team Collaboration:** Use Trello for task management and progress tracking
4. **Documentation:** Keep design documents updated as design evolves
5. **Next Phase:** Prepare for Implementation phase after design completion

---

**Status:** ✅ System Design & Architecture Phase Documentation Complete  
**Next Phase:** Implementation  
**Last Updated:** Fall 2025

