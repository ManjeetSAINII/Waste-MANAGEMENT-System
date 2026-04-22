<div align="center">

# ♻️ WasteWise

### A Full-Stack Waste Management System

**Report Waste · Track Pickups · Manage via Admin Panel**

🌐 **Live Demo:** https://wastemanagement.wuaze.com

---

![PHP](https://img.shields.io/badge/PHP-7.2-777BB4?style=for-the-badge\&logo=php\&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7-4479A1?style=for-the-badge\&logo=mysql\&logoColor=white)
![Apache](https://img.shields.io/badge/Apache-Docker-D22128?style=for-the-badge\&logo=apache\&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-4-7952B3?style=for-the-badge\&logo=bootstrap\&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Hub-2496ED?style=for-the-badge\&logo=docker\&logoColor=white)

</div>

---

## 🌿 About

**WasteWise** is a web-based waste management platform that allows residents to report waste collection requests while enabling administrators to monitor, update, and manage them efficiently.

The system provides a clean UI, secure authentication, and a complete workflow from reporting to resolution.

---

## ✨ Features

| Area                 | Description                                      |
| -------------------- | ------------------------------------------------ |
| 🔐 Authentication    | Signup with OTP email verification, login/logout |
| 🔑 Password Recovery | Forgot password with OTP reset                   |
| 🗑️ Waste Reports    | Submit requests with file/image upload           |
| 📬 Contact System    | Messages stored directly in database             |
| 🛡️ Admin Panel      | Manage, update, and delete waste reports         |
| 🔒 Session Handling  | Separate sessions for admin and users            |
| 📱 Responsive UI     | Clean Bootstrap UI with animations               |

---

## 🚀 Live Demo

👉 https://wastemanagement.wuaze.com

---

## 🗂️ Project Structure

```
waste-management-system/
├── index.html
├── login-user.php
├── signup-user.php
├── verify-email.php
├── forgot-password.php
├── reset-password.php
├── logout-user.php
├── controllerUserData.php
├── session-check.php
├── contact-submit.php
├── waste-report-submit.php
├── adminlogin.php
├── admin-dashboard.php
├── admin-action.php
├── admin-logout.php
├── connection.php
├── wms.sql
├── Dockerfile
├── Dockerfile.db
├── docker-compose.yml
```

---

## ⚙️ Local Setup (Optional - Docker)

```bash
# Clone repository
git clone https://github.com/ManjeetSAINII/waste-management-system.git
cd waste-management-system

# Setup environment
cp .env.example .env

# Run project
docker compose up
```

Open:
👉 http://localhost:8080

---

## 🛡️ Admin Access

👉 https://wastemanagement.wuaze.com/adminlogin.php

| Field    | Value     |
| -------- | --------- |
| Username | admin     |
| Password | admintest |

⚠️ Change credentials after first login.

---

## 🐳 Docker Commands

```bash
docker compose up -d
docker compose logs -f
docker compose down
docker compose down -v
```

---

## 🤝 Contributing

1. Fork the repository
2. Create your branch (`git checkout -b feature/new-feature`)
3. Commit changes
4. Push and create Pull Request

---

<div align="center">

💚 Built for a cleaner and smarter environment

</div>
