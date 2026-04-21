<div align="center">

# ♻️ WasteWise

### A full-stack Waste Management System

**Report waste · Track pickups · Manage via admin panel**

---

![PHP](https://img.shields.io/badge/PHP-7.2-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Apache](https://img.shields.io/badge/Apache-Docker-D22128?style=for-the-badge&logo=apache&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-4-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Hub-2496ED?style=for-the-badge&logo=docker&logoColor=white)

</div>

---

## 🌿 About

WasteWise is a web-based waste management platform where residents can submit waste collection requests and admins can track, update, and manage them — all through a clean, responsive UI. Fully containerized with Docker, so it runs anywhere in seconds.

---

## ✨ Features

| Area | What it does |
|------|-------------|
| 🔐 Auth | Signup with **OTP email verification**, login, logout |
| 🔑 Password | Forgot password → OTP reset flow |
| 🗑️ Reports | Submit waste collection requests with file upload |
| 📬 Contact | Contact form stored in the database |
| 🛡️ Admin | View, update status, delete waste reports |
| 🔒 Sessions | Admin and user sessions fully isolated |
| 📱 UI | Responsive design with animated wave hero section |

---

## 🚀 Quick Start

> **Requirement:** [Docker Desktop](https://www.docker.com/products/docker-desktop/) — that's it. No PHP, no MySQL needed.

```bash
# 1. Clone the repo
git clone https://github.com/ManjeetSAINII/waste-management-system.git
cd waste-management-system

# 2. Set up environment
cp .env.example .env

# 3. Run
docker compose up
```

Open **http://localhost:8080** in your browser. Done. 🎉

---

## 🗂️ Project Structure

```
waste-management-system/
├── 🌐 index.html                  # Homepage with wave UI
├── 🔑 login-user.php              # User login
├── 📝 signup-user.php             # User signup
├── ✉️  verify-email.php            # OTP email verification
├── 🔓 forgot-password.php         # Forgot password
├── 🔄 reset-password.php          # Reset password with OTP
├── 🚪 logout-user.php             # User logout
├── ⚙️  controllerUserData.php      # Auth logic
├── 📡 session-check.php           # AJAX session check
├── 📬 contact-submit.php          # Contact form handler
├── 🗑️  waste-report-submit.php     # Waste report handler
├── 🛡️  adminlogin.php              # Admin login
├── 📊 admin-dashboard.php         # Admin panel
├── 🔧 admin-action.php            # Admin actions
├── 🚪 admin-logout.php            # Admin logout
├── 🔌 connection.php              # DB connection (env-based)
├── 🗄️  wms.sql                     # Schema + seed data
├── 🐳 Dockerfile                  # Web image (PHP + Apache)
├── 🐳 Dockerfile.db               # DB image (MySQL + schema)
└── 🐙 docker-compose.yml          # Orchestration
```

---

## 🛡️ Admin Panel

Navigate to **http://localhost:8080/adminlogin.php**

| Field    | Default     |
|----------|-------------|
| Username | `admin`     |
| Password | `admintest` |

> Change these credentials after your first login.

---

## 🐳 Docker Commands

```bash
# Start in background
docker compose up -d

# View live logs
docker compose logs -f

# Stop
docker compose down

# Full reset (wipes database)
docker compose down -v
```

---

## 🤝 Contributing

1. Fork the repo
2. Create a feature branch: `git checkout -b feature/your-feature`
3. Commit your changes: `git commit -m "add your feature"`
4. Push and open a Pull Request

---

<div align="center">

Made with 💚 for a cleaner world

</div>
