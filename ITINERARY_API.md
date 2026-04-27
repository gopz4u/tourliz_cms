# Itinerary API Documentation

## Overview

The Itinerary API provides endpoints to retrieve and generate detailed day-by-day itineraries for tour packages. Each itinerary includes places to visit, hotel accommodations, transport options, activities, entry tickets, meals, and daily notes.

## Endpoints

### 1. Get Package Itinerary

Retrieve the complete itinerary for a specific package.

**Endpoint:** `GET /api/v1/packages/{slug}/itinerary`

**Parameters:**
- `slug` (path parameter) - The package slug identifier

**Example Request:**
```bash
curl http://localhost/api/v1/packages/bali-adventure/itinerary
```

**Example Response:**
```json
{
  "package": {
    "id": 1,
    "name": "Bali Adventure Package",
    "slug": "bali-adventure",
    "duration": "5 days 4 nights",
    "currency": "USD"
  },
  "summary": {
    "total_days": 5,
    "total_nights": 4,
    "total_places": 8,
    "total_activities": 5
  },
  "cost_breakdown": {
    "hotels": 600,
    "transport": 230,
    "activities": 225,
    "entry_tickets": 150,
    "total": 1205,
    "currency": "USD"
  },
  "itinerary": [
    {
      "day": 1,
      "title": "Arrival in Bali",
      "places": [
        {
          "place_id": 1,
          "name": "Bali - Main Attraction",
          "place_name": "Bali",
          "place_slug": "bali",
          "visit_duration": "2-3 hours",
          "entry_ticket": {
            "required": true,
            "price": 20,
            "currency": "USD",
            "booking_required": false
          }
        }
      ],
      "hotel": {
        "name": "Grand Bali Hotel",
        "type": "5-star",
        "check_in": "14:00",
        "check_out": "12:00",
        "price_per_night": 200,
        "currency": "USD",
        "amenities": ["WiFi", "Breakfast", "Pool", "Gym", "Spa"]
      },
      "transport": [
        {
          "type": "Airport Transfer",
          "mode": "Private Car",
          "from": "Airport",
          "to": "Hotel",
          "price": 50,
          "currency": "USD",
          "duration": "45 minutes",
          "notes": "Driver will be waiting at arrivals with name board"
        }
      ],
      "activities": [
        {
          "name": "City Walking Tour",
          "time": "15:00",
          "duration": "3 hours",
          "entry_ticket": {
            "price": 25,
            "currency": "USD",
            "booking_required": true
          },
          "description": "Experience the best of Bali with this amazing city walking tour"
        }
      ],
      "meals": {
        "breakfast": "Not included",
        "lunch": "Local restaurant (own expense)",
        "dinner": "Special dinner included"
      },
      "notes": "Comfortable walking shoes recommended. Bring sunscreen and water. Check-in at hotel after 2 PM."
    }
  ]
}
```

**Error Response (404):**
```json
{
  "message": "This package does not have an itinerary yet.",
  "package": {
    "id": 1,
    "name": "Package Name",
    "slug": "package-slug"
  }
}
```

---

### 2. Generate Sample Itinerary

Generate and save a sample itinerary for a package that doesn't have one.

**Endpoint:** `POST /api/v1/packages/{slug}/itinerary/generate`

**Parameters:**
- `slug` (path parameter) - The package slug identifier
- `days` (optional query parameter) - Number of days for the itinerary (defaults to package duration or 3)

**Example Request:**
```bash
curl -X POST http://localhost/api/v1/packages/bali-adventure/itinerary/generate?days=5
```

**Example Response:**
```json
{
  "message": "Sample itinerary generated successfully",
  "package": {
    "id": 1,
    "name": "Bali Adventure Package",
    "slug": "bali-adventure"
  },
  "itinerary": {
    "package": { ... },
    "summary": { ... },
    "cost_breakdown": { ... },
    "itinerary": [ ... ]
  }
}
```

---

## Itinerary JSON Structure

Each day in the itinerary follows this structure:

```json
{
  "day": 1,
  "title": "Day Title",
  "places": [
    {
      "place_id": 1,
      "name": "Place Name",
      "place_name": "Full Place Name (from database)",
      "place_slug": "place-slug",
      "place_image": "image-url",
      "visit_duration": "2-3 hours",
      "entry_ticket": {
        "required": true,
        "price": 20,
        "currency": "USD",
        "booking_required": false
      }
    }
  ],
  "hotel": {
    "name": "Hotel Name",
    "type": "5-star",
    "check_in": "14:00",
    "check_out": "12:00",
    "price_per_night": 200,
    "currency": "USD",
    "amenities": ["WiFi", "Breakfast", "Pool"]
  },
  "transport": [
    {
      "type": "Airport Transfer",
      "mode": "Private Car",
      "from": "Airport",
      "to": "Hotel",
      "price": 50,
      "currency": "USD",
      "duration": "45 minutes",
      "notes": "Additional notes"
    }
  ],
  "activities": [
    {
      "attraction_id": 5,
      "name": "Activity Name",
      "attraction_name": "Full Attraction Name (from database)",
      "attraction_slug": "attraction-slug",
      "time": "10:00 AM",
      "duration": "3 hours",
      "entry_ticket": {
        "price": 25,
        "currency": "USD",
        "booking_required": true
      },
      "description": "Activity description"
    }
  ],
  "meals": {
    "breakfast": "Included at hotel",
    "lunch": "Local restaurant (own expense)",
    "dinner": "Not included"
  },
  "notes": "Daily notes and recommendations"
}
```

---

## Cost Breakdown

The API automatically calculates costs from the itinerary:

- **hotels**: Total accommodation costs (sum of all price_per_night)
- **transport**: Total transport costs (sum of all transport prices)
- **activities**: Total activity costs (sum of all activity entry tickets)
- **entry_tickets**: Total entry ticket costs for places
- **total**: Grand total of all costs
- **currency**: Currency code (from package)

---

## Testing the API

### Step 1: Check existing packages
```bash
curl http://localhost/api/v1/packages
```

### Step 2: Generate itinerary for a package
```bash
curl -X POST http://localhost/api/v1/packages/{package-slug}/itinerary/generate
```

### Step 3: Retrieve the generated itinerary
```bash
curl http://localhost/api/v1/packages/{package-slug}/itinerary
```

### Step 4: Use the seeder to populate all packages
```bash
php artisan db:seed --class=ItinerarySeeder
```

---

## Integration Notes

### Frontend Integration

1. **Display Itinerary**: Fetch the itinerary and display day-by-day breakdown
2. **Show Costs**: Display the cost breakdown to help users understand pricing
3. **Booking Flow**: Use itinerary data to pre-fill booking forms
4. **Print/PDF**: Generate printable itinerary documents

### Data Enrichment

The API automatically enriches itinerary data with:
- Place details (name, slug, image) from the `places` table
- Attraction details (name, slug, image, description) from the `attractions` table

This means you only need to store IDs in the itinerary JSON, and the API will fetch full details.

---

## Error Handling

- **404**: Package not found or itinerary doesn't exist
- **500**: Server error (check logs for details)

---

## Future Enhancements

Potential improvements for the itinerary system:

1. **Custom Itineraries**: Allow users to customize generated itineraries
2. **Multiple Itinerary Versions**: Store different itinerary options per package
3. **Real-time Pricing**: Integrate with live pricing APIs for hotels and activities
4. **Availability Calendar**: Check availability for hotels and activities
5. **Weather Integration**: Show weather forecasts for each day
6. **Map Integration**: Display routes and locations on interactive maps
