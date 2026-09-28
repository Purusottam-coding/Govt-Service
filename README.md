# Government Service Management System (सरकारी सेवा व्यवस्थापन प्रणाली)

A full-stack municipal and government service management portal built with **Laravel** and **Blade**. The platform provides streamlined digital workflows for citizen public service applications, document submission and verification, automated unique verification IDs, QR-based fee payments, and an administrative review portal.

---
## 🚀 Key Features

### 👤 Citizen Portal
- **User Authentication:** Registration, secure login, and profile management.
- **Service Catalog:** Browse available municipal and departmental services with details, fee structure, and document requirements.
- **Online Applications:** Submit applications with required supporting document uploads.
- **Digital Payment Flow:** QR-code-based fee payment and voucher/receipt image upload.
- **Application Tracking:** Real-time tracking of application and payment status (Pending, Under Review, Approved, Rejected).
- **Approved Document Issuance & Verification:** Download/view approved certificates featuring a generated **Unique Verification ID** (e.g., `ABC123XYZ`) with QR verification.
- **Document Search:** Quick lookup and public verification of issued certificates using their unique ID.

### 🛡️ Administrative Portal
- **Dashboard Overview:** Comprehensive statistics on total applications, pending verifications, collected revenue, and active services.
- **Application Workflow:** Review citizen applications, inspect uploaded documents, update status, and add remarks.
- **Document Approval & ID Generation:** Approve documents and automatically generate unique tracking/verification identifiers.
- **Payment Verification:** Verify submitted bank vouchers and approve payment transactions.
- **Department & Service Management:** Add, edit, or configure services, fees, and requirements.
- **Notices & Citizen Feedback:** Publish official announcements and manage user feedback.

### 🌐 UI & Accessibility
- **Bilingual Interface:** English & Nepali dates and labels.
- **Accessibility Tools:** Quick font size adjuster (A- / A / A+) and high-contrast toggle.
- **Responsive Layout:** Optimized for desktop, tablet, and mobile browsers.

---

## 🛠️ Prerequisites

Make sure the following are installed on your machine:

- **PHP:** `^8.2` or newer
- **Composer:** `^2.x`
- **Node.js & npm:** `^18.x` or `^20.x`
- **MySQL Database Server:** `^8.0` or MariaDB
- **Web Server:** Built-in PHP server or Apache/Nginx

---

## ⚙️ Installation & Setup

### 1. Clone the Repository
```bash
git clone https://github.com/Purusottam-coding/Govt-Service-Management-System.git
cd government-service
```

### 2. Configure Backend

Navigate to the `backend/` directory:
```bash
cd backend
```

Install PHP dependencies:
```bash
composer install
```

Set up environment variables:
```bash
# On Windows
copy .env.example .env

# On Linux/macOS
cp .env.example .env
```

Generate the Laravel application key:
```bash
php artisan key:generate
```

### 3. Database Configuration

Open `backend/.env` and update your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=government_service
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

Create the database in MySQL (if it doesn't already exist):
```sql
CREATE DATABASE government_service CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Run database migrations and seed default administrative data:
```bash
php artisan migrate --seed
```

Create the symbolic link for uploaded documents:
```bash
php artisan storage:link
```

### 4. Mail Configuration (Optional - For OTP & Notifications)

Configure your SMTP settings in `backend/.env` for password resets and email notifications:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@governmentservice.gov.np"
MAIL_FROM_NAME="Government Service Portal"
```

---

## 💻 Running the Application

### 1. Start the Laravel Server

From inside the `backend/` directory:
```bash
cd backend
php artisan serve
```

The application will be accessible at:
```text
http://127.0.0.1:8000
```

### 2. Frontend Assets (Optional Development Mode)

If you modify styling or scripts requiring Vite compilation:
```bash
cd frontend
npm install
npm run dev
```

---

## 🧪 Testing

Run the automated test suite from the `backend/` folder:
```bash
cd backend
php artisan test
```

---

## 🔒 Security & Best Practices

- Always keep sensitive information such as `.env` excluded from version control (`.gitignore`).
- When modifying `.env` parameters, clear cached configurations using:
  ```bash
  php artisan config:clear
  php artisan cache:clear
  ```

---

## 📄 License

This project is open-source and available under the [MIT License](https://opensource.org/licenses/MIT).
