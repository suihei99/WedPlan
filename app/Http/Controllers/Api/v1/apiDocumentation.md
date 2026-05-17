# WebPlan API v1 Documentation

This document is a quick integration guide for the mobile app. All endpoints below are rooted at `/api/v1`.

## Base URL

Use your app domain with the API prefix:

```text
https://your-domain.com/api/v1
```

For local development, the exact host depends on your Laravel setup.

## Authentication

Most endpoints require a Sanctum bearer token.

Send this header with protected requests:

```http
Authorization: Bearer YOUR_TOKEN_HERE
Accept: application/json
Content-Type: application/json
```

## Common Response Pattern

Success responses usually follow one of these shapes:

```json
{
  "message": "Success message",
  "data": {}
}
```

or:

```json
{
  "success": true,
  "message": "Response text"
}
```

Validation errors return HTTP `422` with Laravel's default error payload.

## Public Endpoints

### Get Guest QR Data

`GET /api/v1/guest/qr/{code}`

Used to fetch guest QR information.

Example:

```http
GET /api/v1/guest/qr/ABC123
```

## Authentication Endpoints

### Register Couple

`POST /api/v1/auth/register/couple`

Request body:

```json
{
  "email": "couple@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "partner_1_name": "Alya",
  "partner_2_name": "Haziq",
  "wedding_date": "2026-12-31",
  "wedding_venue": "Kuala Lumpur",
  "wedding_time": "19:30",
  "total_budget_limit": 50000
}
```

Notes:

- `password_confirmation` is required.
- `wedding_date`, `wedding_venue`, `wedding_time`, and `total_budget_limit` are optional.

### Register Vendor

`POST /api/v1/auth/register/vendor`

Request body must be sent as `multipart/form-data` because it includes a file upload.

Fields:

- `email`
- `password`
- `password_confirmation`
- `business_name`
- `business_type`
- `contact_number`
- `address`
- `business_documents` file upload (`pdf` or `png`)

### Login

`POST /api/v1/auth/login`

Request body:

```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

Successful response:

```json
{
  "message": "Login successful",
  "role": "couple",
  "token": "sanctum-token",
  "user": {}
}
```

### Logout

`POST /api/v1/auth/logout`

Requires authentication.

## Shared Authenticated Endpoints

### Get Settings

`GET /api/v1/settings`

### Update Settings

`PUT /api/v1/settings`

Use this for authenticated user settings updates.

## Couple Endpoints

All couple endpoints require:

- authentication via Sanctum token
- `role:couple`

### Dashboard

`GET /api/v1/couple/dashboard`

### Budget Categories

`GET /api/v1/couple/budget`

`POST /api/v1/couple/budget`

Request body:

```json
{
  "category_name": "Venue",
  "allocated_amount": 15000
}
```

`GET /api/v1/couple/budget/{budgetCategory}`

`PUT /api/v1/couple/budget/{budgetCategory}`

`DELETE /api/v1/couple/budget/{budgetCategory}`

### Expenses

`GET /api/v1/couple/expenses`

`POST /api/v1/couple/expenses`

Request body:

```json
{
  "budget_category_id": 1,
  "expense_name": "Hall deposit",
  "amount": 3000,
  "date_paid": "2026-06-01",
  "description": "Initial deposit for venue",
  "payment_method": "cash"
}
```

Optional file upload:

- `receipt` as `pdf`, `jpg`, `jpeg`, or `png`

`GET /api/v1/couple/expenses/{expense}`

`PUT /api/v1/couple/expenses/{expense}`

`DELETE /api/v1/couple/expenses/{expense}`

### Guests

`GET /api/v1/couple/guests`

`POST /api/v1/couple/guests`

Request body:

```json
{
  "name": "Siti",
  "phone": "+60123456789",
  "pax_count": 2,
  "rsvp_status": "pending"
}
```

`GET /api/v1/couple/guests/{guest}`

`PUT /api/v1/couple/guests/{guest}`

`PUT /api/v1/couple/guests/{guest}/rsvp`

`POST /api/v1/couple/guests/{guest}/check-in`

`DELETE /api/v1/couple/guests/{guest}`

### Public Invitation & QR Endpoints

These public endpoints are used by guests (mobile app or web) to view an invitation card and scan/enter codes.

- `GET /api/v1/guest/qr/{code}`
  - Legacy QR endpoint. Returns the invitation payload (same shape as `/guest/invitation/{code}`) and the generated `qr_image_url` so the app can show or download the QR image.

- `GET /api/v1/guest/invitation/{code}`
  - Preferred for manual code entry. Returns a full invitation payload with guest, couple and wedding details.

Example request:

```
GET /api/v1/guest/invitation/INV12345
Accept: application/json
```

Successful response (200):

```json
{
  "data": {
    "invite_code": "INV12345",
    "guest_name": "Charlie Guest",
    "pax_count": 3,
    "rsvp_status": "pending",
    "couple": {
      "partner_1_name": "Adam",
      "partner_2_name": "Bella",
      "display_name": "Adam & Bella"
    },
    "wedding": {
      "venue": "Grand Ballroom",
      "date": "2026-12-25",
      "time": "18:30"
    },
    "checkin_url": "https://your-domain.com/guest/checkin/INV12345",
    "qr_image_url": "https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=..."
  }
}
```

Not found response (404):

```json
{
  "message": "Invitation not found."
}
```

Integration notes:

- Use `/guest/invitation/{code}` for manual code entry to get structured invitation data for the UI.
- Both endpoints are public and do not require authentication so they can be used by mobile guests who don't have accounts.
- For check-in actions (confirming attendance), use the authenticated couple endpoints: `POST /api/v1/couple/guests/{guest}/check-in` or the couple-managed guest APIs.

### Tasks

`GET /api/v1/couple/tasks`

`POST /api/v1/couple/tasks`

Request body:

```json
{
  "task_name": "Confirm photographer",
  "description": "Call and finalize package",
  "deadline": "2026-08-01",
  "is_completed": false,
  "priority": 2
}
```

`GET /api/v1/couple/tasks/{task}`

`PUT /api/v1/couple/tasks/{task}`

`PUT /api/v1/couple/tasks/{task}/complete`

`DELETE /api/v1/couple/tasks/{task}`

### AI Budget Assistant

`POST /api/v1/couple/ai-budget/estimate`

Request body:

```json
{
  "guest_count": 250,
  "budget_range": "RM 25000 - RM 40000"
}
```

Supported `budget_range` values:

- `RM 10000 - RM 20000`
- `RM 25000 - RM 40000`
- `RM 2500 - RM 40000`
- `RM 50000 And Above`
- `None Of Above`

`POST /api/v1/couple/ai-budget/chat`

Request body:

```json
{
  "message": "How should I split the budget for 250 guests?",
  "guest_count": 250,
  "budget_range": "RM 25000 - RM 40000"
}
```

## Vendor Endpoints

All vendor endpoints require:

- authentication via Sanctum token
- `role:vendor`

### Dashboard

`GET /api/v1/vendor/dashboard`

### Services

`GET /api/v1/vendor/services`

`POST /api/v1/vendor/services`

Request body:

```json
{
  "service_name": "Photography Package",
  "type_service": "photography",
  "price_estimate": 4500,
  "description": "Full day wedding coverage"
}
```

If you upload an image, send `multipart/form-data` and include `image_url` as a file.

`GET /api/v1/vendor/services/{service}`

`PUT /api/v1/vendor/services/{service}`

`DELETE /api/v1/vendor/services/{service}`

### Bookings

`GET /api/v1/vendor/bookings`

`POST /api/v1/vendor/bookings`

Request body:

```json
{
  "couple_id": 12,
  "type_service": "photography",
  "booking_date": "2026-09-10",
  "status": true,
  "notes": "Morning session"
}
```

`GET /api/v1/vendor/bookings/{booking}`

`PUT /api/v1/vendor/bookings/{booking}`

`DELETE /api/v1/vendor/bookings/{booking}`

### Notifications

`GET /api/v1/vendor/notifications`

`GET /api/v1/vendor/notifications/{notification}`

`PUT /api/v1/vendor/notifications/{notification}/read`

`DELETE /api/v1/vendor/notifications/{notification}`

## Error Codes

- `401` unauthorized or invalid token
- `403` forbidden / role mismatch / profile missing
- `404` resource not found
- `422` validation error
- `429` rate limit exceeded for AI budget chat
- `500` server error
- `503` AI assistant unavailable

## Mobile Integration Tips

- Use `multipart/form-data` for any endpoint that uploads files.
- Store the Sanctum token securely on the device.
- Send `Accept: application/json` on every request to keep responses consistent.
- For role-specific endpoints, route users after login using the returned `role` value.
