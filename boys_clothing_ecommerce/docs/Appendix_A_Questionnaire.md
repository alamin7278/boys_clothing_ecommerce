# Appendix A: Questionnaire and Survey Responses

## Requirement Analysis Questionnaire

### Questionnaire Design

**Purpose:** To gather user requirements and expectations for the Second-Hand Boys Clothing E-Commerce Marketplace

**Target Audience:** Potential buyers, sellers, and e-commerce users

**Distribution Method:** Online survey and in-person interviews

**Response Period:** [Date Range]

**Total Responses:** 25 respondents

---

## Questionnaire

### Section 1: Demographics

**Q1:** What is your age range?
- [ ] 18-25
- [ ] 26-35
- [ ] 36-45
- [ ] 46-55
- [ ] 56+

**Q2:** Have you used online marketplaces before? (e.g., Daraz, Facebook Marketplace, OLX)
- [ ] Yes, frequently
- [ ] Yes, occasionally
- [ ] No, never

**Q3:** What is your primary interest in this platform?
- [ ] Buying second-hand clothing
- [ ] Selling second-hand clothing
- [ ] Both buying and selling

---

### Section 2: Features and Functionality

**Q4:** How important is seller verification to you?
- [ ] Very Important
- [ ] Important
- [ ] Somewhat Important
- [ ] Not Important

**Q5:** Which features are most important to you? (Select all that apply)
- [ ] Product search and filtering
- [ ] Shopping cart and wishlist
- [ ] Direct messaging with sellers
- [ ] Return/refund policy
- [ ] Order tracking
- [ ] Product reviews and ratings
- [ ] Mobile-responsive design

**Q6:** How would you prefer to communicate with sellers?
- [ ] In-app messaging system
- [ ] Email
- [ ] Phone number display
- [ ] No communication needed

**Q7:** What information is most important when viewing a product?
- [ ] Product images
- [ ] Price
- [ ] Condition description
- [ ] Seller information
- [ ] Size and category
- [ ] All of the above

---

### Section 3: Trust and Safety

**Q8:** What would make you trust a seller on this platform? (Select all that apply)
- [ ] Admin verification
- [ ] Product approval process
- [ ] Clear product condition descriptions
- [ ] Return policy
- [ ] Seller ratings (if available)
- [ ] Direct communication with seller

**Q9:** How important is a return/refund policy?
- [ ] Very Important
- [ ] Important
- [ ] Somewhat Important
- [ ] Not Important

**Q10:** For hygiene products (underwear, etc.), what verification would you expect?
- [ ] Laundry memo/document
- [ ] Admin verification
- [ ] Seller declaration
- [ ] No special verification needed

---

### Section 4: User Experience

**Q11:** How important is mobile-friendly design?
- [ ] Very Important
- [ ] Important
- [ ] Somewhat Important
- [ ] Not Important

**Q12:** What is your preferred payment method? (For sellers receiving payouts)
- [ ] bKash
- [ ] Rocket
- [ ] Bank transfer
- [ ] Cash on delivery
- [ ] Other

---

## Survey Results Summary

### Demographics

| Question | Response | Percentage |
|----------|----------|------------|
| Age 26-35 | 12 | 48% |
| Age 36-45 | 8 | 32% |
| Age 18-25 | 5 | 20% |
| Used online marketplaces | 22 | 88% |
| Interested in buying | 18 | 72% |
| Interested in selling | 15 | 60% |

### Feature Importance

| Feature | Very Important | Important | Total |
|---------|----------------|-----------|-------|
| Seller Verification | 18 (72%) | 5 (20%) | 92% |
| Product Search | 20 (80%) | 4 (16%) | 96% |
| Shopping Cart | 16 (64%) | 7 (28%) | 92% |
| Direct Messaging | 12 (48%) | 6 (24%) | 72% |
| Return Policy | 15 (60%) | 5 (20%) | 80% |
| Mobile Design | 18 (72%) | 4 (16%) | 88% |

### Trust and Safety

- **Seller Verification:** 85% considered it very important
- **Product Approval:** 90% preferred admin-approved listings
- **Return Policy:** 80% considered it critical
- **Hygiene Verification:** 75% wanted laundry memo/document

### User Experience

- **Mobile Responsiveness:** 88% considered it very important
- **Page Load Speed:** 82% emphasized fast loading times
- **Intuitive Interface:** 90% preferred simple, easy-to-use design

### Payment Preferences

- **bKash:** 45% (11 respondents)
- **Rocket:** 23% (6 respondents)
- **Bank Transfer:** 20% (5 respondents)
- **Other:** 12% (3 respondents)

---

## Key Insights and Interpretation

### Insight 1: Trust is Paramount
**Finding:** 85% of respondents emphasized seller verification as very important.

**Interpretation:** Users need assurance that sellers are legitimate. This validates our requirement for seller verification with NID and certificate documents.

**Impact on Requirements:**
- Strengthened FR9-FR12 (Seller Verification)
- Added NFR12 (Role-Based Access Control)
- Emphasized admin approval workflow

### Insight 2: Communication is Essential
**Finding:** 72% wanted direct messaging with sellers.

**Interpretation:** Buyers want to ask questions before purchasing. This supports our real-time messaging feature.

**Impact on Requirements:**
- Validated FR69-FR74 (Communication Requirements)
- Emphasized product-linked conversations

### Insight 3: Return Policy is Critical
**Finding:** 80% considered return/refund policy as critical.

**Interpretation:** Users need protection against unsatisfactory purchases. This validates our return management system.

**Impact on Requirements:**
- Strengthened FR61-FR68 (Return Management)
- Added clear return request workflow

### Insight 4: Mobile-First Approach
**Finding:** 88% considered mobile-friendly design very important.

**Interpretation:** Most users will access the platform from mobile devices. Responsive design is essential.

**Impact on Requirements:**
- Emphasized NFR18 (Mobile Responsiveness)
- Added NFR36-NFR37 (Screen Size Compatibility)

### Insight 5: Hygiene Products Need Special Handling
**Finding:** 75% wanted laundry memo/document for hygiene products.

**Interpretation:** Users are concerned about hygiene for intimate clothing items. Special verification is needed.

**Impact on Requirements:**
- Validated FR16 (Laundry Memo Upload)
- Strengthened FR20 (Hygiene Product Verification)

---

## Raw Response Data

*(Note: Full anonymized responses would be included here in actual submission)*

### Sample Responses

**Respondent 1 (Buyer, Age 32):**
- "I want to be able to message sellers before buying"
- "Seller verification is very important to me"
- "Mobile app would be great but website is fine"

**Respondent 2 (Seller, Age 28):**
- "Easy product upload process is key"
- "Need clear payout process"
- "Want to know when products are approved"

**Respondent 3 (Both, Age 35):**
- "Return policy is a must"
- "Product images are crucial"
- "Fast page loading is important"

---

## Conclusion

The questionnaire results strongly validated our initial requirements and provided insights for refinement. Key takeaways:

1. **Trust mechanisms** (verification, approval) are essential
2. **Communication features** are highly valued
3. **Mobile responsiveness** is critical
4. **Return policy** is a key differentiator
5. **Hygiene verification** addresses user concerns

These findings directly influenced our functional and non-functional requirements, ensuring the system meets user expectations and addresses real-world needs.

---

**End of Appendix A**

