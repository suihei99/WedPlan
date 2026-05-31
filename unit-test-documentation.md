# Unit Test Documentation

This document records the HTTP tests prepared for the thesis. In the codebase, these are implemented as feature tests because they verify full API and web routing behavior end to end.

## API Test

- File: `tests/Feature/Api/GuestQrApiTest.php`
- Route covered: `GET /api/v1/guest/qr/{code}`
- Purpose: confirms the API returns invitation details for a valid invite code and includes both the check-in URL and QR image URL.
- Main assertions: response is successful, guest and couple data are returned, and the generated URLs match the expected values.

## Web Test

- File: `tests/Feature/Web/GuestQrRedirectTest.php`
- Route covered: `GET /guest/qr/{code}`
- Purpose: confirms the web route redirects to the generated QR image URL.
- Main assertions: response is a redirect and the location matches the QR service URL generated from the invite code.

## Run Command

```bash
php artisan test --compact tests/Feature/Api/GuestQrApiTest.php tests/Feature/Web/GuestQrRedirectTest.php
```

## Thesis Note

These tests are useful for a thesis because they demonstrate both backend API validation and user-facing web route behavior with real database-backed test data.