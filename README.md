# 🌍 Digital Tourism Platform

A full-featured tour and travel booking platform built with PHP & MySQL — covering tour packages, flights, bus bookings, an admin panel, eSewa payment integration, and Firebase push notifications.

![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/license-see%20LICENSE-blue)

---

## 📋 Table of Contents

- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Project Structure](#-project-structure)
- [Getting Started](#-getting-started)
- [Configuration](#-configuration)
- [Security](#-security-implemented)
- [Roadmap](#-roadmap)
- [Contributors](#-developed-by)
- [License](#-license)

---

## 🚀 Features

### 🌐 Frontend

- Tour listing (Domestic & International)
- Flight and bus listings with group fare badges
- Dynamic tour details page with itinerary
- Inquiry / booking form with live client-side validation
- Downloadable trip PDF
- eSewa payment integration
- Mobile-responsive layout
- Success & error handling UI

### 🔐 Admin Panel

- Secure admin login
- Add / edit / delete tours
- Dynamic itinerary management
- Banner & PDF upload
- Manage flights and buses
- "Popular" badge toggle
- Active / inactive status control

### 🔔 Notifications

- Firebase Cloud Messaging (FCM)
- Multi-device admin push notifications
- Automatic invalid token cleanup

---

## 🛠 Tech Stack

| Layer         | Technology                                |
| ------------- | ----------------------------------------- |
| Backend       | PHP (procedural + prepared statements)    |
| Database      | MySQL                                     |
| Frontend      | Vanilla JavaScript, custom responsive CSS |
| Notifications | Firebase Cloud Messaging                  |
| Payments      | eSewa                                     |
| Server        | Apache / XAMPP                            |

---

## 📁 Project Structure

digital-tourism-platform/
├── admin/ # Admin panel (tours, flights, bookings management)
├── api/ # API endpoints
├── assets/ # Images, CSS, JS, static files
├── config/ # DB connection & app configuration
├── includes/ # Shared PHP includes (headers, footers, helpers)
├── sql_db/ # Database schema / seed SQL
├── logs/ # Runtime logs (git-ignored in practice)
├── booking.php # Tour booking flow
├── esewa-payment.php # eSewa payment initiation
├── esewa-success.php # Payment success callback
├── esewa-fail.php # Payment failure callback
├── tours.php / tour-details.php
├── flights.php / flight-details.php
├── buses.php / bus-details.php
├── signin.php / signup.php / signout.php
├── admin/ # Admin dashboard
└── .htaccess

---

## ⚡ Getting Started

### Prerequisites

- PHP 8.0+
- MySQL 5.7+ / MariaDB
- Apache (XAMPP, WAMP, or LAMP stack)
- A Firebase project (for push notifications)
- An eSewa merchant account (for payments)

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
   - Import the schema from `sql_db/`:

```bash
     mysql -u root -p tourism_platform < sql_db/schema.sql
```

4. **Configure environment variables**
   - Copy the example config and fill in your own credentials:

```bash
     cp config/config.example.php config/db.php
```

- Update `config/db.php` with your DB host, username, password, and database name.
- Add your Firebase server key and eSewa merchant credentials where required (see [Configuration](#-configuration)).

5. **Start Apache & MySQL**, then visit:
   http://localhost/digital-tourism-platform/

---

## ⚙️ Configuration

This project relies on a few credentials that **should never be committed to git**:

| Variable                                   | Used in             | Purpose                |
| ------------------------------------------ | ------------------- | ---------------------- |
| `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME` | `config/db.php`     | MySQL connection       |
| Firebase Server Key                        | admin notifications | FCM push notifications |
| eSewa Merchant ID / Secret                 | `esewa-payment.php` | Payment gateway        |

> ⚠️ If `config/db.php` currently contains real credentials, rotate them and move to environment variables or a git-ignored config file before making the repo public / sharing it.

---

## 🔒 Security Implemented

- Prepared statements for database queries
- Session-based authentication
- Password hashing (`password_hash`)
- Token-based FCM device management
- Input sanitization
- PRG pattern (Post-Redirect-Get) on form submissions
- Client + server-side validation

---

## 🗺 Roadmap

- [ ] CSRF token validation on all forms
- [ ] Consistent output escaping (`htmlspecialchars`) across all views
- [ ] Server-side amount recalculation on payment flows
- [ ] Automated tests / CI (PHP lint on push)
- [ ] `.env`-based configuration

---

## 👨‍💻 Developed By

- Kushal Acharya
- Bipin Chapai

---

## 📄 License

See [LICENSE](./LICENSE) for details.
