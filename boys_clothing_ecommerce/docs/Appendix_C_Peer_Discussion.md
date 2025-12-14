# Appendix C: Peer Discussion Summary

## Overview

We engaged in discussions with other Software Engineering project groups to validate requirements, share insights, and identify common challenges. This document summarizes key discussions and outcomes.

---

## Discussion Participants

### Our Group
- [Group Member Names]

### Peer Groups
- **Group A:** Student Resource Management System
- **Group B:** Volunteer Coordination App
- **Group C:** Event Ticketing Platform
- **Group D:** Waste Collection Scheduling Web App

---

## Discussion Topics

### Topic 1: Requirement Completeness

**Question:** How do you ensure all requirements are captured?

**Responses:**
- Group A: "We used user stories and scenarios to identify edge cases"
- Group B: "Questionnaires helped us discover hidden requirements"
- Group C: "Comparative analysis revealed missing features"

**Our Takeaway:**
- Validated our multi-method approach (questionnaires + comparative analysis)
- Confirmed importance of user stories
- Identified need for edge case consideration

**Action Items:**
- Added edge case requirements (e.g., FR25: Auto-update product status to 'sold')
- Enhanced error handling requirements (NFR28-NFR30)

---

### Topic 2: Security Requirements

**Question:** What security measures are you implementing?

**Responses:**
- Group A: "Password hashing, input validation, session management"
- Group B: "Role-based access control is crucial"
- Group C: "CSRF protection for forms"
- Group D: "File upload validation is important"

**Our Takeaway:**
- Confirmed our security requirements are comprehensive
- Identified CSRF protection as important (added NFR16)
- Validated file upload security (NFR11)

**Action Items:**
- Added CSRF protection requirement (NFR16)
- Strengthened file upload validation (NFR11)
- Emphasized session security (NFR10)

---

### Topic 3: User Experience Design

**Question:** How are you ensuring good user experience?

**Responses:**
- Group A: "Mobile responsiveness is critical"
- Group B: "Clear error messages improve usability"
- Group C: "Consistent navigation is key"
- Group D: "Form validation feedback is essential"

**Our Takeaway:**
- Mobile responsiveness is universally important (NFR18)
- Error messaging quality matters (NFR20)
- Consistency across pages is crucial (NFR19)

**Action Items:**
- Strengthened mobile responsiveness requirements
- Added detailed error message specifications
- Emphasized navigation consistency

---

### Topic 4: Admin Dashboard Features

**Question:** What features are you including in admin dashboard?

**Responses:**
- Group A: "Analytics and statistics"
- Group B: "User management and approval workflows"
- Group C: "Content moderation tools"
- Group D: "System monitoring"

**Our Takeaway:**
- Admin dashboards need comprehensive features
- Analytics are important for platform management
- Approval workflows are common requirement

**Action Items:**
- Validated our admin dashboard requirements (FR89-FR95)
- Confirmed importance of analytics (FR95)
- Strengthened approval workflow requirements

---

### Topic 5: Database Design

**Question:** How are you structuring your database?

**Responses:**
- Group A: "Normalized structure with foreign keys"
- Group B: "Separate tables for different entities"
- Group C: "Indexes for performance"
- Group D: "Scalable design for future growth"

**Our Takeaway:**
- Database normalization is standard practice
- Foreign key relationships are important
- Performance optimization needed (indexes)
- Scalability considerations are crucial

**Action Items:**
- Validated our database structure (users, products, orders, etc.)
- Confirmed foreign key relationships
- Added performance requirements (NFR2)

---

### Topic 6: Testing Strategy

**Question:** How are you planning to test your system?

**Responses:**
- Group A: "Unit testing for core functions"
- Group B: "Integration testing for workflows"
- Group C: "User acceptance testing"
- Group D: "Performance testing"

**Our Takeaway:**
- Multiple testing levels are needed
- User acceptance testing is important
- Performance testing validates NFRs

**Action Items:**
- Noted for future testing phase
- Validated performance requirements are testable

---

### Topic 7: Common Challenges

**Challenges Identified:**
1. **Requirement Ambiguity:** Vague requirements lead to confusion
2. **Scope Creep:** Features keep expanding
3. **Time Management:** Balancing development and documentation
4. **Team Coordination:** Ensuring all members contribute

**Solutions Discussed:**
- Clear requirement numbering (FR1, FR2, etc.)
- Scope definition in SRS
- Regular team meetings
- Task assignment via Trello

**Our Takeaway:**
- Our requirement numbering system (FR1-FR95) is good practice
- Scope definition in Section 1.2 is important
- Trello organization is essential

---

### Topic 8: Documentation Standards

**Question:** How are you structuring your SRS document?

**Responses:**
- Group A: "Following IEEE template"
- Group B: "Including user stories"
- Group C: "Detailed functional requirements"
- Group D: "Comprehensive non-functional requirements"

**Our Takeaway:**
- IEEE template is standard approach
- User stories add value
- Both functional and non-functional requirements needed

**Action Items:**
- Confirmed our SRS structure is appropriate
- Validated inclusion of user stories
- Strengthened non-functional requirements section

---

## Key Insights from Peer Discussions

### Insight 1: Requirement Validation
Peer discussions validated that our requirements are comprehensive and well-structured. Other groups faced similar challenges and used similar approaches.

### Insight 2: Security is Universal Concern
All groups emphasized security requirements. Our security section (NFR7-NFR16) aligns with industry standards.

### Insight 3: User Experience Matters
Mobile responsiveness and intuitive design were common themes. Our usability requirements (NFR17-NFR24) address these concerns.

### Insight 4: Admin Features are Essential
All groups with multi-user systems included admin dashboards. Our admin requirements (FR89-FR95) are comprehensive.

### Insight 5: Documentation Quality
Well-documented requirements prevent confusion and scope creep. Our detailed SRS structure supports this.

---

## Lessons Learned

### What Worked Well
- Multi-method requirement gathering (questionnaires + analysis)
- Detailed requirement numbering system
- Comprehensive non-functional requirements
- Peer discussions for validation

### Areas for Improvement
- Could have engaged more peer groups
- Earlier peer discussions would have been beneficial
- More detailed edge case consideration needed

### Best Practices Identified
1. **Clear Requirement Format:** Numbered, action-oriented requirements
2. **Comprehensive Coverage:** Both functional and non-functional
3. **User-Centered Design:** User stories and questionnaires
4. **Security First:** Early consideration of security requirements
5. **Documentation Standards:** Following IEEE template

---

## Action Items from Discussions

1. ✅ Added CSRF protection requirement (NFR16)
2. ✅ Strengthened error handling requirements
3. ✅ Validated admin dashboard features
4. ✅ Confirmed mobile responsiveness importance
5. ✅ Enhanced security requirements section
6. ✅ Validated requirement numbering system

---

## Conclusion

Peer discussions provided valuable validation and insights:

- **Confirmed** our requirements are comprehensive
- **Identified** additional security considerations
- **Validated** our approach to requirement gathering
- **Shared** best practices and lessons learned
- **Strengthened** our SRS document quality

These discussions were instrumental in refining our requirements and ensuring we haven't missed critical features or considerations.

---

**End of Appendix C**

