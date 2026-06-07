# WedPlan — User Manual

This manual explains how to use the WedPlan web application. It is written for end users (couples and event planners) who will manage budgets, vendors, tasks, guests, and expenses via the web interface.

**Audience:** Couples, planners, and vendor coordinators using the WedPlan web UI.

**Contents:**
- Getting started
- Account and authentication
- Dashboard overview
- Budgets and categories
- Vendors and bookings
- Tasks and checklists
- Guests and RSVP
- Expenses and payments
- Notifications
- Mobile sync and API notes
- Troubleshooting & FAQs

**Getting started**

1. Open the application URL in your browser (ask your administrator for the deployed URL or run locally with `php artisan serve`).
2. Register for an account using the Sign Up form (name, email, password) or log in using existing credentials.

**Account and authentication**

- Register: provide full name, email, and password. Confirm your email if the installer enabled email verification.
- Login: use email and password. Use password reset if necessary via the "Forgot password" flow.
- Profile: access your profile from the top-right menu to update contact information and partner details.

**Dashboard overview**

The Dashboard is your central view. Typical cards and sections include:
- Budget summary: shows total estimated, spent, and remaining budget.
- Upcoming tasks: prioritized checklist items with due dates.
- Bookings: recent vendor bookings and payment status.
- Quick actions: add expense, create task, add vendor, or invite guest.

Use the navigation menu to access Budgets, Vendors, Tasks, Guests, and Settings.

**Budgets and categories**

- Create a Budget Category: go to Budgets → Add Category. Set an estimated amount and optional notes.
- Edit Category: update estimated amount to reflect changes in planning.
- Track spending: add expenses and associate them with budget categories to see real-time spent vs. estimated figures.
- Reports: view a breakdown of categories and chart views (if enabled) to see where money is allocated.

**Vendors and bookings**

- Add Vendor: Vendors → New Vendor. Provide vendor name, contact details, service type, and contract notes.
- Create Booking: from a Vendor page, create a booking with date, deposit, total amount, and payment milestones.
- Payment tracking: mark deposit or payment milestones as paid and upload receipts (if the app supports file uploads).

**Tasks and checklists**

- Add Task: Tasks → New Task. Assign to a user (if collaborating), set priority, due date, and attach notes.
- Task states: Open, In Progress, Completed. Filter tasks by date, priority, or assignee.
- Templates: use pre-made task templates (if present) to scaffold common wedding planning workflows.

**Guests and RSVP**

- Add Guest: Guests → New Guest. Capture name, email/phone, RSVP status, and meal preference.
- Bulk import: use CSV import if available to add multiple guests quickly.
- Seating/Groups: assign guests to groups or tables if feature exists in your deployment.

**Expenses and payments**

- Add Expense: Budgets → Add Expense or Expenses → New Expense. Link to a Budget Category and optionally a Vendor or Booking.
- Receipts: upload image/pdf receipts where supported.
- Filtering: filter expenses by date, category, vendor, or payment status.

**Notifications**

- Alerts: the application can send alerts for upcoming tasks, payment due dates, or vendor confirmations.
- Email/SMS: check your profile settings to enable/disable email or SMS notifications (depends on deployment).

**Mobile sync and API notes**

- This repository implements a REST API used by the companion mobile app. If you use the mobile app, data (budgets, tasks, guests, expenses) will sync via API.
- For developers: see `routes/api.php` and `app/Http/Controllers/Api` for endpoints and API resources.

**Troubleshooting & FAQs**

- Can’t log in: use password reset. If email never arrives, check spam and confirm SMTP settings in `.env` (admin task).
- Missing features: some deployments may disable features. Contact the site administrator for enabled modules.
- Data backup: administrators should back up the database and storage directory regularly.

**Support & feedback**

For bugs, feature requests, or help, open an issue in the project repository or contact the site administrator managing the deployment.

---

This manual focuses on standard user flows. If you need screenshots, step-by-step annotated guides, or printable checklists, I can add them next.
