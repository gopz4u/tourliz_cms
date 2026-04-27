# B2B Custom Itinerary Builder

## Overview

A new module designed for B2B agencies to build custom, white-labeled itineraries for their clients. It allows creating specific trip plans not tied to pre-defined packages.

## ✅ Features Implemented

1.  **Custom Itinerary Model**
    -   Stores client details, pricing with markup, and daily itinerary.
    -   Linked to Agency (User) and Destination (Place).

2.  **API Endpoints**
    -   `POST /api/b2b/itineraries` - Create new blank itinerary.
    -   `PUT /api/b2b/itineraries/{id}` - Update details (places, hotels, activities, pricing).
    -   `GET /api/b2b/itineraries` - List agency's proposals.
    -   `GET /api/b2b/itineraries/{id}/pdf` - Generate professional PDF proposal.

3.  **PDF Proposal Generator**
    -   Generates a clean, professional PDF.
    -   Includes: Agency Info, Client Name, Trip Summary, Detailed Daily Itinerary, Pricing.

## 🚀 How to Use (API)

### 1. Create a Draft
```http
POST /api/b2b/itineraries
Content-Type: application/json
Authorization: Bearer <token>

{
    "title": "Family Trip to Bali",
    "client_name": "Smith Family",
    "place_id": 16,
    "duration_days": 5,
    "start_date": "2026-06-15"
}
```

### 2. Update Details
```http
PUT /api/b2b/itineraries/{id}
Content-Type: application/json
Authorization: Bearer <token>

{
    "markup_percentage": 15, // Add 15% markup
    "itinerary": [
        {
            "day": 1,
            "title": "Welcome to Bali",
            "places": [
                {"name": "Uluwatu Temple", "visit_duration": "2 hours"}
            ],
            "hotel": {"name": "Grand Hyatt", "price_per_night": 250, "currency": "USD"}
        }
        // ... more days
    ]
}
```

### 3. Generate Proposal
```http
GET /api/b2b/itineraries/{id}/pdf
Authorization: Bearer <token>
```
*Returns a downloadable PDF.*

## 📋 Next Steps for Integration

1.  **Frontend Interface**: Build a "Proposal Builder" UI in the B2B dashboard using these APIs.
2.  **Email Integration**: Add `POST /email` to send the PDF directly to the client.
