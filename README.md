# User Profile Management System

A simple **PHP-based User Profile Management System** that allows users to register, log in, manage their profile, upload a profile picture, change their password, and securely log out.

---

## 📌 Features

* ✅ User Registration
* ✅ User Login
* ✅ Remember Me functionality
* ✅ Secure Password Hashing
* ✅ User Profile Page
* ✅ Upload & Update Profile Picture
* ✅ Change Password
* ✅ Logout
* ✅ Session Management
* ✅ Input Validation
* ✅ JSON-based User Storage (No Database Required)

---

## 🛠️ Technologies Used

* PHP
* HTML5
* CSS3
* JSON (for storing user data)
* PHP Sessions
* Cookies (Remember Me)

---

## 📂 Project Structure

```
project/
│── index.php
│── login.php
│── register.php
│── profile.php
│── change-password.php
│── logout.php
│── users.json
│── uploads/
└── README.md
```

---

## 🚀 How to Run

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/user-profile-system.git
```

### 2. Move Project

Copy the project folder into:

```
xampp/htdocs/
```

or

```
wamp/www/
```

### 3. Start Server

* Start **Apache** from XAMPP/WAMP.

### 4. Open Browser

```
http://localhost/project-folder/
```

---

## 👤 Registration

Users can:

* Create a username
* Enter email
* Set password
* Upload a profile picture (optional)

Validation includes:

* Username cannot contain spaces
* Unique username
* Unique email
* Valid email format
* Minimum password length of 6 characters

---

## 🔐 Login

Users log in using:

* Username
* Password

Features:

* Password verification
* Remember Me (30 days)
* Session creation

---

## 👤 Profile

After login, users can:

* View username
* View email
* View registration date
* View profile picture
* Update profile picture

---

## 🔑 Change Password

Users must enter:

* Current password
* New password
* Confirm new password

Passwords are securely updated after verification.

---

## 📸 Profile Picture

Supported formats:

* JPG
* JPEG
* PNG
* GIF

Maximum file size:

```
2 MB
```

Uploaded images are stored inside:

```
uploads/
```

---

## 🔒 Security Features

* Password hashing using `password_hash()`
* Password verification using `password_verify()`
* Session authentication
* Remember Me token
* Input validation
* File upload validation
* HTML escaping to prevent XSS

---

## 📁 Data Storage

User information is stored inside:

```
users.json
```

Each user record contains:

* Username
* Email
* Password Hash
* Profile Picture
* Created Date
* Remember Token

---

## 🎯 Future Improvements

* MySQL Database Support
* Email Verification
* Forgot Password
* Admin Dashboard
* User Roles
* Two-Factor Authentication (2FA)
* Responsive UI with Bootstrap
* Dark Mode

---

## 📸 Screens

* Login Page
* Registration Page
* User Profile
* Change Password
* Logout

---

## 👨‍💻 Author

**Sakshyam Poudel**

---

## 📄 License

This project is developed for **learning and educational purposes**. Feel free to modify and use it for personal or academic projects.
