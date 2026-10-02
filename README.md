# 🌍 Digital Tourism Platform

A full-featured tour and travel booking platform built with PHP & MySQL — covering tour packages, flights, bus bookings, a blog, an AI-powered chatbot and recommendation engine, a full admin panel, dual payment gateway integration (eSewa + Khalti), and Firebase push notifications.

![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/license-see%20LICENSE-blue)

---

## 📋 Table of Contents

- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Project Structure](#-project-structure)
- [Database](#-database)
- [Getting Started](#-getting-started)
- [Configuration](#-configuration)
- [Security](#-security-implemented)
- [Roadmap](#-roadmap)
- [Contributors](#-developed-by)
- [License](#-license)

---

## 🚀 Features

### 🌐 Frontend

- Tour listing (Domestic & International) with search, filters, and detail pages with full itineraries
- Flight and bus listings with group fare badges
- Booking flow with booking details, booking history (My Bookings), and cancellation
- Blog with categories and threaded comments
- Photo gallery organized into albums
- Client / partner showcase, testimonials, and trip reviews
- FAQs page
- Dynamic sitemap
- Downloadable trip PDF
- Dual payment gateway: **eSewa** and **Khalti**, each with dedicated success/failure callback pages
- Account system: signup with **email OTP verification**, sign in/out, forgot/reset password
- Mobile-responsive layout with clean, extension-less URLs (e.g. `/trips` instead of `/trips.php`)

### 🤖 AI-Powered Chatbot, Recommendations & Algorithms

- On-site chatbot that answers questions about trips, flights, and buses using a coverage-scored keyword matcher backed by **Gemini embeddings** (`gemini-embedding-001`) for semantic similarity, with a one-time backfill script to embed existing content
- **Smart hybrid recommendation engine** combining five weighted signals: content-based similarity (price, duration, tour type against a blended user-taste profile), collaborative filtering (co-booking patterns), popularity (log-dampened bookings/clicks), ranking by scores with the use of Bayesian Rating Algorithm for ratings as well and more
- **Nearby packages**: Haversine-distance based "packages near this one" on tour detail pages, plus a live-GPS "packages near you" widget
- User activity tracking (views, time spent, clicks) feeding both the recommendation engine and admin analytics
- Dynamically discount price and full and deposit cost calculation

### 🔐 Admin Panel

- Secure admin login with session-based auth
- Dashboard with key metrics
- Manage trips (add/edit/delete, dynamic itinerary builder, banner & PDF upload, "Popular" badge, active/inactive toggle)
- Manage flights and buses
- Manage blog posts, blog categories, and blog comments (with moderation)
- Manage gallery albums and photos
- Manage clients/partners, testimonials, and trip reviews
- Manage FAQs
- View and manage package bookings, bus inquiries, and general inquiries
- Manage admin users
- Multi-device push notifications via Firebase Cloud Messaging, with automatic invalid-token cleanup

---

## 🛠 Tech Stack

| Layer         | Technology                                           |
| ------------- | ---------------------------------------------------- |
| Backend       | PHP (procedural + prepared statements)               |
| Database      | MySQL                                                |
| Frontend      | Vanilla JavaScript, custom responsive CSS            |
| AI / Chatbot  | Google Gemini API (embeddings + semantic search)     |
| Notifications | Firebase Cloud Messaging                             |
| Payments      | eSewa, Khalti                                        |
| Email         | PHPMailer (OTP verification, password reset, alerts) |
| Server        | Apache / XAMPP                                       |

---

## 📁 Project Structure

```
digital-tourism-platform/
├── admin/              # Admin panel (trips, flights, buses, blog, gallery, reviews, bookings, etc.)
│   ├── api/             # Admin-only API endpoints (e.g. FCM token save)
│   ├── scripts/          # One-off maintenance scripts (e.g. chatbot embeddings backfill)
│   └── includes/         # Shared admin header/sidebar/footer
├── api/                 # Public API endpoints (chatbot, embeddings, recommendations, nearby, search, etc.)
├── payment/             # eSewa & Khalti payment initiation + success/fail callbacks
├── assets/               # Images, CSS, JS, fonts, static files
├── config/               # DB connection & app configuration
├── includes/             # Shared PHP includes (headers, footers, helpers)
├── sql_db/               # Database schema (dtp.sql)
├── booking.php / booking-details.php / cancel-booking.php / my-bookings.php
├── trips.php / tour-details.php
├── flights.php / flight-details.php
├── buses.php / bus-details.php
├── blogs.php / blog-details.php
├── gallery.php / album.php
├── faqs.php / about.php / services.php / contact.php
├── signin.php / signup.php / verify-otp.php / signout.php
├── forgot-password.php / reset-password.php / profile.php
├── firebase-messaging-sw.js
└── .htaccess
```

---

## 🗄 Database

The schema (`sql_db/dtp.sql`) defines 25 tables, including:

- **Core**: `trips`, `tour_itineraries`, `flights`, `buses`, `users`, `admins`
- **Bookings & inquiries**: `package_bookings`, `bus_inquiries`, `inquiries`
- **Content**: `blogs`, `blog_categories`, `blog_comments`, `gallery_albums`, `gallery_photos`, `faqs`, `clients`, `testimonials`, `trip_reviews`, `site_content`
- **AI / analytics**: `chatbot_quiries`, `user_activity`, `recmnd_clicks`
- **Auth & notifications**: `password_resets`, `admin_fcm_tokens`

---

## ⚡ Getting Started

### Prerequisites

- PHP 8.0+
- MySQL 5.7+ / MariaDB
- Apache (XAMPP, WAMP, or LAMP stack) with `mod_rewrite` enabled
- A Firebase project (for push notifications)
- A Google Gemini API key (for chatbot embeddings/semantic search)
- An eSewa merchant account and a Khalti merchant account (for payments)
- An SMTP-capable email account (for OTP/password-reset emails via PHPMailer)

### Installation

1. **Clone the repo**

```bash
   git clone https://github.com/KooSL/digital-tourism-platform.git
   cd digital-tourism-platform
```

2. **Move into your server directory** (e.g. XAMPP's `htdocs`)

```bash
   mv digital-tourism-platform /path/to/htdocs/
```

3. **Import the database**
   - Create a MySQL database (e.g. `tourism_platform`)
   - Import the schema:

```bash
     mysql -u root -p tourism_platform < sql_db/dtp.sql
```

4. **Configure environment variables**
   - Create a `.env` file in the project root (see [Configuration](#-configuration) for the required keys)
   - Add your Firebase service account JSON, Gemini API key, and eSewa/Khalti secret keys

5. **(Optional) Backfill chatbot embeddings**
   - Run `admin/scripts/backfill_chatbot_embeddings.php` once after seeding tour data, so the chatbot's semantic search has vectors to compare against

6. **Start Apache & MySQL**, then visit:
   http://localhost/digital-tourism-platform/

---

## ⚙️ Configuration

This project loads its configuration from a **git-ignored `.env` file** in the project root (`config/db.php` parses it directly — there's no separate PHP config file to copy).

| Variable                                                          | Purpose                                        |
| ----------------------------------------------------------------- | ---------------------------------------------- |
| `APP_NAME`, `APP_ENV`                                             | General app settings                           |
| `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`                        | MySQL connection                               |
| `FCM_PROJECT_ID` (+ `config/firebase-service-account.json`)       | Firebase Cloud Messaging push notifications    |
| `SEND_MAIL_USERNAME`, `SEND_MAIL_PASSWORD`, `RECVE_MAIL_USERNAME` | PHPMailer (OTP emails, password reset, alerts) |
| `geminiAPIKey`                                                    | Gemini embeddings for the chatbot              |
| `ESEWA_SECRET_KEY`                                                | eSewa payment gateway                          |
| `KHALTI_SECRET_KEY`                                               | Khalti payment gateway                         |

> ⚠️ Never commit `.env` or `config/firebase-service-account.json`. If real credentials have ever been committed to this repo's history, rotate them immediately — removing the file in a later commit does not invalidate an already-exposed key.

---

## 🔒 Security Implemented

- Prepared statements for database queries
- Session-based authentication
- Password hashing (`password_hash`)
- Email OTP verification on signup, plus a token-based forgot/reset password flow
- CSRF token validation on forms across the site (signup, verify-otp, and others)
- Token-based FCM device management with automatic cleanup of invalid tokens
- Input sanitization
- PRG pattern (Post-Redirect-Get) on form submissions
- Client + server-side validation
- `.env`-based configuration, kept out of version control
- `.htaccess` rules blocking direct access to `.env`, `.json`, `.log`, and `.sql` files

---

## 🗺 Roadmap

- [ ] Extend CSRF token validation to any remaining forms without it
- [ ] Consistent output escaping (`htmlspecialchars`) across all views
- [ ] Server-side amount recalculation on payment flows (both eSewa and Khalti)
- [ ] Automated tests / CI (PHP lint on push)
- [ ] Add a `.env.example` template for easier onboarding

---

## 👨‍💻 Developed By

- Kushal Acharya
- Bipin Chapai

---

## 📄 License

See [LICENSE](./LICENSE) for details.
