# B2B Agency & Custom Itinerary System

## ✅ Implementation Complete

### 1. Agency Management (CMS)
- **Manage Agencies**: Create B2B partner accounts with `company_name`, `logo`, `contact info`.
- **Assign Places**: Mark agencies as specialists for specific destinations (e.g., "Bali Expert").
- **Admin Panel**: Go to **Agencies (B2B)** in the sidebar to manage partners.

### 2. Custom Itinerary Builder
- **B2B API**: Endpoints for agencies to build trip proposals.
- **Flexible Pricing**: Agencies can set their own **markup %** on to the base cost.
- **Day-by-Day**: Full control over daily activities, hotels, and transport.

### 3. Proposal Formats
- **PDF Export**: Professional, white-labeled PDF with agency branding.
- **WhatsApp Integration**: API returns a direct `wa.me` link to share the proposal.

## 🚀 How to Use

### For Admins (CMS)
1.  Go to `http://localhost:8000/admin/agencies`
2.  Click **"Add Agency"**
3.  Enter company details and selects **Place Specializations**
4.  Save the agency. They can now login!

### For Agencies (API / Future Portal)
1.  **Create Itinerary**: `POST /api/b2b/itineraries`
2.  **Add Details**: `PUT /api/b2b/itineraries/{id}`
3.  **Get Proposal**: `GET /api/b2b/itineraries/{id}`
    -   Returns `whatsapp_link` for instant sharing.
4.  **Download PDF**: `GET /api/b2b/itineraries/{id}/pdf`

## 🔗 Quick Links
- **Admin Agencies**: [Manage Agencies](http://localhost:8000/admin/agencies)
- **API Documentation**: [B2B_ITINERARY_GUIDE.md](file:///c:/xampp/htdocs/tourliz_cms/B2B_ITINERARY_GUIDE.md)
