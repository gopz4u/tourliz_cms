# Working Itinerary API URLs

## ✅ Correct Package Slugs

Here are the **actual working URLs** for all 14 packages with itineraries:

### 1. The Phuket & Krabi
```
http://127.0.0.1:8000/api/v1/packages/the-phuket-krabi-5d4n-itinerary-at-a-glance/itinerary
```

### 2. Kuala Lumpur City Tour
```
http://127.0.0.1:8000/api/v1/packages/kuala-lumpur-city-tour-malaysia-holiday-package/itinerary
```

### 3. MELAKA Heritage & Culture
```
http://127.0.0.1:8000/api/v1/packages/melaka-4d3n-heritage-culture-escape/itinerary
```

### 4. Phu Quoc Vietnam
```
http://127.0.0.1:8000/api/v1/packages/phu-quoc-4d3n-vietnams-tropical-paradise/itinerary
```

### 5. Dubai City Tour
```
http://127.0.0.1:8000/api/v1/packages/dubai-city-tour-burj-khalifa/itinerary
```

### 6. Bali Honeymoon ⭐
```
http://127.0.0.1:8000/api/v1/packages/bali-5-days-4-nights-honeymoon/itinerary
```

### 7. Singapore Universal Studios
```
http://127.0.0.1:8000/api/v1/packages/singapore-universal-studios/itinerary
```

### 8. Maldives Water Villa
```
http://127.0.0.1:8000/api/v1/packages/maldives-water-villa-stay/itinerary
```

### 9. Thailand Bangkok
```
http://127.0.0.1:8000/api/v1/packages/thailand-bangkok-city-tour/itinerary
```

### 10. Malaysia KL & Genting
```
http://127.0.0.1:8000/api/v1/packages/malaysia-kl-genting/itinerary
```

### 11. Sri Lanka Cultural Triangle
```
http://127.0.0.1:8000/api/v1/packages/sri-lanka-cultural-triangle/itinerary
```

### 12. Nepal Kathmandu & Pokhara
```
http://127.0.0.1:8000/api/v1/packages/nepal-kathmandu-pokhara/itinerary
```

### 13. Goa Beach Holiday
```
http://127.0.0.1:8000/api/v1/packages/goa-beach-holiday/itinerary
```

### 14. Kerala Backwaters Houseboat
```
http://127.0.0.1:8000/api/v1/packages/kerala-backwaters-houseboat/itinerary
```

---

## 🎯 Quick Test

**Copy and paste any URL above into your browser!**

The correct Bali slug is:
```
bali-5-days-4-nights-honeymoon
```
NOT `bali-5-days-4-nights-honeymoon-package`

---

## 📝 What You'll See

Each endpoint returns JSON with:
- Package details
- Summary (days, nights, places, activities)
- **Cost breakdown** (hotels, transport, activities, tickets)
- **Complete day-by-day itinerary**

Example response structure:
```json
{
  "package": {...},
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
  "itinerary": [...]
}
```
