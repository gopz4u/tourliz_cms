# Quick Start Guide - Testing Itinerary API

## ✅ Routes Fixed!

The route ordering issue has been resolved. Static routes now come before parameterized routes.

## 🚀 How to Test

### Step 1: Get Package List
Run this command to see all available packages:
```bash
php list_packages.php
```

### Step 2: Test in Browser

Open any of these URLs in your browser (replace `{slug}` with actual package slug):

**Example URLs:**
```
http://localhost:8000/api/v1/packages/the-phuket-krabi-5d4n-itinerary-at-a-glance/itinerary
http://localhost:8000/api/v1/packages/bali-5-days-4-nights-honeymoon-package/itinerary
http://localhost:8000/api/v1/packages/dubai-city-tour-with-burj-khalifa/itinerary
```

### Step 3: Generate New Itinerary (Optional)

To generate a fresh itinerary for a package:
```bash
curl -X POST http://localhost:8000/api/v1/packages/{slug}/itinerary/generate
```

## 📋 What You'll See

The API returns JSON with:

```json
{
  "package": {
    "id": 1,
    "name": "Package Name",
    "slug": "package-slug",
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
      "title": "Arrival in Destination",
      "places": [...],
      "hotel": {...},
      "transport": [...],
      "activities": [...],
      "meals": {...},
      "notes": "..."
    }
  ]
}
```

## 🎯 Available Packages (from seeder)

Based on the seeder output, these packages have itineraries:

1. The Phuket & Krabi 5D4N Itinerary at a Glance
2. Explore Malaysia +penang with Cultural Vibe
3. MELAKA 4D3N - HERITAGE & CULTURE ESCAPE
4. PHU QUOC 4D3N - VIETNAM'S TROPICAL PARADISE
5. Dubai City Tour with Burj Khalifa
6. Bali 5 Days 4 Nights Honeymoon Package
7. Singapore Universal Studios Entry
8. Maldives Water Villa Stay
9. Thailand Bangkok City Tour
10. Malaysia Kuala Lumpur & Genting Highlands
11. Sri Lanka Cultural Triangle Tour
12. Nepal Kathmandu & Pokhara Adventure
13. Goa Beach Holiday Package
14. Kerala Backwaters Houseboat Cruise

## 🔧 Troubleshooting

**404 Error - Package Not Found:**
- Make sure you're using the correct slug (run `php list_packages.php` to see all slugs)
- Slugs are lowercase with hyphens (e.g., `bali-5-days-4-nights-honeymoon-package`)

**No Itinerary:**
- Run the seeder: `php artisan db:seed --class=ItinerarySeeder`
- Or generate one: `POST /api/v1/packages/{slug}/itinerary/generate`

**Server Not Running:**
- Make sure `php artisan serve` is running
- Default URL is `http://localhost:8000`

## 📚 Full Documentation

See [ITINERARY_API.md](file:///c:/xampp/htdocs/tourliz_cms/ITINERARY_API.md) for complete API documentation.
