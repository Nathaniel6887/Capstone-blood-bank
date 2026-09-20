# 🩸 Caraga Regional Hospital (CRH) — Blood Bank Information & Management System

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Vue 3](https://img.shields.io/badge/Vue.js-3.5-4FC08D?style=for-the-badge&logo=vuedotjs&logoColor=white)](https://vuejs.org)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-3.0-9553E9?style=for-the-badge&logo=inertia&logoColor=white)](https://inertiajs.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.0-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![TypeScript](https://img.shields.io/badge/TypeScript-5.2-3178C6?style=for-the-badge&logo=typescript&logoColor=white)](https://www.typescriptlang.org)

An enterprise-grade Blood Bank Information and Operations Management System developed for **Caraga Regional Hospital (CRH)**. Designed to streamline donor recruitment, mobile and hospital-based blood collections, component preparation, crossmatching, clinical ward blood issuance, regional blood network transfers, and official DOH / NVBSP regulatory reporting.

---

## 📋 Table of Contents

- [Key Features & Modules](#-key-features--modules)
  - [1. Executive Operations Dashboard](#1-executive-operations-dashboard)
  - [2. Blood Collection & Donor Management](#2-blood-collection--donor-management)
  - [3. Blood Consumption & Clinical Issuance](#3-blood-consumption--clinical-issuance)
  - [4. Staff Accounts & Access Control](#4-staff-accounts--access-control)
  - [5. Regional Reports & DOH Analytics](#5-regional-reports--doh-analytics)
  - [6. Security & Authentication](#6-security--authentication)
- [System Architecture & Tech Stack](#-system-architecture--tech-stack)
- [Project Directory Structure](#-project-directory-structure)
- [Installation & Getting Started](#-installation--getting-started)
- [Database Configuration & Seeding](#-database-configuration--seeding)
- [Development & Build Commands](#-development--build-commands)
- [User Roles & Authorization Matrix](#-user-roles--authorization-matrix)
- [License & Institutional Notice](#-license--institutional-notice)

---

## 🌟 Key Features & Modules

### 1. Executive Operations Dashboard
- **Live ABO/Rh Inventory**: Real-time stock visibility for all 8 blood groups (`A+`, `A-`, `B+`, `B-`, `AB+`, `AB-`, `O+`, `O-`) with low-reserve alerts.
- **Operations Summary**: Immediate metrics for monthly donor collection, clinical unit turnover, emergency reserve reserves, and active on-duty personnel.
- **Quick Action Bar**: One-click navigation to log new collections, issue units to inpatient wards, add returning donor records, and audit reports.
- **Activity Feed**: Live log of recent donor entries and urgent crossmatch requests.

---

### 2. Blood Collection & Donor Management (`/blood-collection`)
- **First-Time Donor Registration (`Create.vue`)**: Comprehensive donor intake capturing personal details, contact data, blood type/Rh, donation category (Voluntary Non-Remunerated, Replacement, Autologous), physical examination vitals (weight, BP, pulse, hemoglobin), and collection volume.
- **Returning Donor Fast Intake**: Searchable donor index allowing instant addition of new donation sessions while automatically computing 3-month eligibility intervals.
- **Donor Profile & History (`View.vue`)**: Detailed donor card displaying lifetime donation count, last donation date, eligibility status, medical notes, and printable donor records.
- **Quarantine & Testing Segregation**: Automated flagging of newly drawn units for Transfusion-Transmissible Infection (TTI) laboratory screening.

---

### 3. Blood Consumption & Clinical Issuance (`/blood-consumtion`)
- **Ward Requisition Logging (`Create.vue`)**: Issue blood units by requisition number, patient name, Hospital Record Number (HRN), ward/room assignment, and attending physician.
- **Component Specificity**: Supports Whole Blood, Packed Red Blood Cells (PRBC), Fresh Frozen Plasma (FFP), Platelet Concentrate (PC), and Cryoprecipitate.
- **Crossmatch & Urgency Management**: Classifies requests into Routine, Urgent, and Stat / Emergency, with strict crossmatch compatibility validation.
- **Blood Issue Slip & Transfusion Record (`View.vue`)**: Printable official issue slip with cold-chain transfer verification and nurse sign-off.
- **Unit Disposition Status**: Quick transition between `Issued`, `Transfused`, `Returned`, and `Discarded`.

---

### 4. Staff Accounts & Access Control (`/staff-accounts`)
- **Authorized Personnel Registry**: Management of hospital staff across departments (Blood Bank & Transfusion Medicine, Clinical Pathology, Donor Recruitment, Quality Assurance).
- **Staff Registration & Update (`Create.vue`)**: Modular dialog with auto-generated Employee IDs (`CRH-EMP-xxx`), departmental assignments, and role-based privilege allocation.
- **Digital Personnel ID & Profile (`View.vue`)**: Printable staff badge and credentials summary with comprehensive role responsibility checklists.
- **Multi-Factor Status Switching**: Inline status management (`Active`, `On Leave`, `Inactive`) and quick ID/email clipboard tools.

---

### 5. Regional Reports & DOH Analytics (`/reports`)
- **Network Scope Filtering**:
  - **Inside Caraga Network**: Provincial health offices and municipal blood collection stations (*Surigao del Norte, Surigao del Sur, Agusan del Norte, Agusan del Sur, Dinagat Islands*).
  - **Outside Caraga Transfers**: Inter-regional hospital exchanges (*Region X Northern Mindanao, Region XI Davao, Region VII Central Visayas, NCR, etc.*).
- **Categorized Print Layout (`PrintLayout.vue`)**: Official printable report formats complete with Caraga Regional Hospital institutional headers, report code badges, tabular blood unit breakdown, and authorized medical officer signatory blocks.
- **Report Lifecycle Tracking**: Audit stages from `Draft`, `Under Review`, `Verified`, to `Audited`.

---

### 6. Security & Authentication
- **Multi-Factor Authentication (2FA)**: Built-in TOTP and Two-Factor challenge validation via Laravel Fortify.
- **Passkeys / WebAuthn**: Passwordless biometric authentication using `@laravel/passkeys`.
- **Role-Based Authorization**: Distinct access levels ensuring standard staff cannot alter administrative security settings or delete essential audit records.

---

## 🛠 System Architecture & Tech Stack

| Layer | Technologies Used |
|---|---|
| **Backend Framework** | [Laravel 12](https://laravel.com) (PHP 8.3+) |
| **Frontend Framework** | [Vue 3](https://vuejs.org) (Composition API, `<script setup lang="ts">`) |
| **Client-Server Bridge** | [Inertia.js v3](https://inertiajs.com) |
| **Styling & Design System** | [Tailwind CSS v4](https://tailwindcss.com), Custom Caraga Regional Hospital theme |
| **UI Component Primitives** | [Reka UI](https://reka-ui.com), [Lucide Vue Icons](https://lucide.dev) |
| **Build Tooling** | [Vite 8](https://vite.dev), TypeScript 5.2 |
| **Database** | SQLite (Default for quick setup), MySQL 8.0+, or PostgreSQL 15+ |

---

## 📁 Project Directory Structure

```text
bloodbank/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── BloodCollectionController.php   # Donor intake & collection logic
│   │       ├── BloodConsumptionController.php  # Ward requisition & issuance logic
│   │       ├── DashboardController.php         # Analytics & stock calculations
│   │       ├── ReportController.php            # Regional network reports
│   │       └── StaffAccountController.php      # Personnel credentials & roles
│   └── Models/
│       ├── BloodDonor.php                      # Donor records & donation sessions
│       ├── BloodIssue.php                      # Transfusion & ward issuance records
│       ├── BloodReport.php                     # Regional network & DOH reports
│       └── User.php                            # Hospital staff accounts & roles
├── database/
│   ├── migrations/                             # Database schema migrations
│   └── seeders/
│       ├── BloodDonorSeeder.php                # Sample donors & donation history
│       ├── BloodIssueSeeder.php                # Sample ward requisitions
│       ├── BloodReportSeeder.php               # Regional reports dataset
│       ├── DatabaseSeeder.php                  # Master seeder
│       └── UserSeeder.php                      # Authorized hospital personnel
├── resources/
│   ├── css/
│   │   └── app.css                             # Tailwind CSS v4 design system
│   └── js/
│       ├── components/                         # Reusable UI cards, headers, buttons
│       │   ├── AppHeader.vue                   # Top navigation bar
│       │   ├── AppSidebar.vue                  # Main operations navigation
│       │   └── ui/                             # Reka UI primitives
│       ├── layouts/
│       │   └── AppLayout.vue                   # Central authenticated layout
│       └── pages/
│           ├── Dashboard.vue                   # Central operations overview
│           ├── blood-collection/
│           │   ├── Create.vue                  # First-time & returning donor modal
│           │   ├── Index.vue                   # Donor records list & search
│           │   └── View.vue                    # Donor profile & donation history
│           ├── blood-consumtion/
│           │   ├── Create.vue                  # Blood issuance & requisition modal
│           │   ├── Index.vue                   # Blood issuance registry & search
│           │   └── View.vue                    # Transfusion slip & issue details
│           ├── reports/
│           │   ├── Create.vue                  # Report generation modal
│           │   ├── Index.vue                   # Network reports list & filters
│           │   ├── PrintLayout.vue             # Official hospital print template
│           │   └── View.vue                    # Report breakdown & audit view
│           └── staff-accounts/
│               ├── Create.vue                  # Staff registration & edit modal
│               ├── Index.vue                   # Staff directory & role filtering
│               └── View.vue                    # Digital ID & authorization profile
├── routes/
│   ├── auth.php                                # Fortify & authentication routes
│   └── web.php                                 # Application feature routes
├── composer.json                               # PHP dependencies
├── package.json                                # Node.js dependencies
└── vite.config.ts                              # Vite & frontend build pipeline
```

---

## 🚀 Installation & Getting Started

### Prerequisites
Make sure your environment meets the following minimum requirements:
- **PHP**: `^8.3` (with `pdo`, `mbstring`, `openssl`, `curl`, `sqlite3` / `pdo_mysql` extensions enabled)
- **Composer**: `^2.7`
- **Node.js**: `^20.0` or `^22.0`
- **NPM**: `^10.0`

---

### Step-by-Step Installation

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/Nathaniel6887/Capstone-blood-bank.git bloodbank
   cd bloodbank
   ```

2. **Install PHP Dependencies**:
   ```bash
   composer install
   ```

3. **Install JavaScript Dependencies**:
   ```bash
   npm install
   ```

4. **Set Up Environment File**:
   ```bash
   cp .env.example .env
   ```

5. **Generate Application Encryption Key**:
   ```bash
   php artisan key:generate
   ```

---

## 🗄 Database Configuration & Seeding

1. **Configure Database in `.env`**:
   For the default SQLite database:
   ```env
   DB_CONNECTION=sqlite
   ```
   *(Ensure `database/database.sqlite` exists, or run `touch database/database.sqlite`)*

   Or for MySQL:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=crh_bloodbank
   DB_USERNAME=root
   DB_PASSWORD=
   ```

2. **Run Migrations & Seed Default Data**:
   ```bash
   php artisan migrate:fresh --seed
   ```

### Default Demonstration Accounts
After running the seeders, you can log in with:

| Account Type | Email | Password | Assigned Role |
|---|---|---|---|
| **System Administrator** | `admin@crh.gov.ph` | `password` | System Administrator |
| **Medical Technologist** | `medtech@crh.gov.ph` | `password` | Registered Medical Technologist (RMT) |
| **Laboratory Supervisor** | `supervisor@crh.gov.ph` | `password` | Laboratory Supervisor |
| **Phlebotomist** | `phleb@crh.gov.ph` | `password` | Phlebotomist / Donor Care |

---

## 💻 Development & Build Commands

### Start Local Development Server
Run the Laravel backend and Vite frontend hot-module reloading concurrently:
```bash
# Terminal 1: Laravel Backend
php artisan serve

# Terminal 2: Vite Dev Server
npm run dev
```

### Production Build
To compile and minify all Vue components, TypeScript, and Tailwind CSS assets:
```bash
npm run build
```

### Code Formatting & Linting
```bash
# PHP Code Styling (Pint)
composer run lint

# TypeScript & Vue Template Type Checking
npm run types:check
```

---

## 👥 User Roles & Authorization Matrix

| Role | Donor Intake | Blood Testing & Crossmatch | Ward Issuance | Stock Audit | Regional Reports | Staff Admin |
|:---|:---:|:---:|:---:|:---:|:---:|:---:|
| **System Administrator** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Laboratory Supervisor** | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ |
| **Medical Technologist (RMT)** | ✅ | ✅ | ✅ | ✅ | 👁️ (View) | ❌ |
| **Blood Bank Officer** | ✅ | ❌ | ✅ | ✅ | 👁️ (View) | ❌ |
| **Phlebotomist / Donor Care** | ✅ | ❌ | ❌ | 👁️ (View) | ❌ | ❌ |
| **Transfusion Safety Nurse** | ❌ | ❌ | ✅ | 👁️ (View) | ❌ | ❌ |

---

## 📄 Institutional Notice & Acknowledgments

Developed for **Caraga Regional Hospital (CRH)**, Surigao City, Philippines, in compliance with the standards of the **National Voluntary Blood Services Program (NVBSP)** and the **Department of Health (DOH)**.

*For technical inquiries or system enhancements, contact the CRH Blood Bank & Transfusion Medicine IT Support Section.*
