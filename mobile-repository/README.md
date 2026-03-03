# Mobile Repository System

A comprehensive web-based system for managing mobile phone data using PHP and MySQL. This project demonstrates secure authentication, database operations, and responsive web design.

## 🚀 Features

### 🔐 Authentication System
- Secure login with username/password verification
- PHP session management
- Password hashing using `password_hash()`
- Logout functionality with session destruction
- Protection against unauthorized access

### 📱 Mobile Phone Management
- Add new mobile phones with validation
- View all mobile phones in a structured table
- Price-based filtering:
  - Mobile phones between Rs10,000 - Rs20,000
  - Mobile phones above Rs20,000
- Search functionality for mobile phones
- Real-time statistics and analytics

### 🎨 User Interface
- Modern, responsive design using CSS
- Gradient backgrounds and card-based layouts
- Interactive tables with hover effects
- Mobile-friendly responsive design
- Clean navigation menu

## 📁 Project Structure

```
mobile-repository/
├── db.php                    # Database connection class
├── login.php                 # Login page
├── authenticate.php          # Authentication handler
├── logout.php                # Logout script
├── dashboard.php             # Home/Dashboard page
├── add_mobile.php            # Add mobile phone form
├── insert_mobile.php         # Insert mobile handler
├── view_all.php              # View all mobile phones
├── price_10_20.php           # Mobile phones Rs10K-Rs20K
├── price_above_20.php        # Mobile phones above Rs20K
├── style.css                 # Stylesheet
├── database_setup.sql        # Database setup script
└── README.md                 # This file
```

## 🛠️ Setup Instructions

### Prerequisites
- XAMPP (or similar PHP/MySQL environment)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Modern web browser

### Step 1: Database Setup

1. Start XAMPP and launch phpMyAdmin
2. Create a new database named `mobile_repository`
3. Import the `database_setup.sql` file:
   - Open phpMyAdmin
   - Select the `mobile_repository` database
   - Click "Import" tab
   - Choose the `database_setup.sql` file
   - Click "Go"

### Step 2: Project Setup

1. Copy the `mobile-repository` folder to your XAMPP htdocs directory:
   ```
   C:/xampp/htdocs/mobile-repository/
   ```

2. Ensure file permissions are correct (if on Linux/Mac)

### Step 3: Configuration

The database connection is pre-configured for XAMPP:
- Host: localhost
- Username: root
- Password: (empty)
- Database: mobile_repository

If your MySQL credentials are different, update `db.php`:
```php
private $host = "localhost";
private $username = "your_username";
private $password = "your_password";
private $dbname = "mobile_repository";
```

### Step 4: Access the Application

1. Start Apache and MySQL in XAMPP
2. Open your web browser
3. Navigate to: `http://localhost/mobile-repository/`

## 🔑 Default Login Credentials

- **Username:** admin
- **Password:** admin123

## 📊 Database Schema

### Users Table
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Mobiles Table
```sql
CREATE TABLE mobiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    brand VARCHAR(100) NOT NULL,
    price INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## 🔧 Technical Implementation

### Security Features
- **SQL Injection Prevention:** Using PDO prepared statements
- **XSS Protection:** HTML escaping with `htmlspecialchars()`
- **Password Security:** Hashing using `password_hash()` and `password_verify()`
- **Session Security:** Session regeneration and proper destruction
- **Input Validation:** Server-side validation for all form inputs

### Error Handling
- Exception handling using try-catch blocks
- User-friendly error messages
- Detailed error logging for debugging
- Graceful degradation on database errors

### Code Quality
- Clean, commented PHP code
- Separation of concerns
- Object-oriented database connection
- Responsive HTML5/CSS3 design
- Semantic HTML structure

## 🎯 Key Concepts Demonstrated

### PHP Concepts
- Session management
- Form handling and validation
- Database operations (CRUD)
- Exception handling
- File organization and includes
- Security best practices

### Database Concepts
- MySQL database design
- SQL queries (SELECT, INSERT)
- Database connection management
- Data filtering and sorting
- Aggregate functions

### Web Development
- Responsive web design
- CSS Grid and Flexbox
- Form validation (client & server-side)
- Navigation and routing
- User experience design

## 📱 Sample Data

The system comes pre-loaded with sample mobile phone data:
- iPhone 14 (Rs85,000)
- Samsung Galaxy S23 (Rs75,000)
- OnePlus 11 (Rs55,000)
- Xiaomi Redmi Note 12 (Rs18,000)
- And more...

## 🚀 Usage Guide

1. **Login:** Use the default credentials to access the system
2. **Dashboard:** View statistics and recent mobile phones
3. **Add Mobile:** Add new mobile phones using the form
4. **View All:** Browse all mobile phones with search functionality
5. **Price Filters:** View mobile phones by price ranges
6. **Logout:** Securely logout when done

## 🔍 Features in Detail

### Dashboard
- Real-time statistics (total mobiles, price ranges)
- Quick action buttons
- Latest mobile phones display
- Visual data representation

### Add Mobile Form
- Client-side and server-side validation
- Real-time error feedback
- Form data persistence on errors
- User-friendly input fields

### View All Mobiles
- Sortable table with all mobile phones
- Search functionality by name/brand
- Summary statistics
- Responsive table design

### Price Range Pages
- Detailed analysis for each price segment
- Visual charts and graphs
- Value scoring and recommendations
- Market insights

## 🛡️ Security Considerations

- All database queries use prepared statements
- User inputs are properly sanitized
- Passwords are securely hashed
- Session management is secure
- Error messages don't reveal sensitive information

## 📞 Support

For issues or questions:
1. Check XAMPP is running properly
2. Verify database connection details
3. Ensure file permissions are correct
4. Check PHP error logs for debugging

## 🔄 Future Enhancements

Potential features to add:
- Edit and delete mobile phones
- User management system
- Export functionality (PDF/Excel)
- Image upload for mobile phones
- Advanced filtering options
- API endpoints for mobile access
- Backup and restore functionality

---

**Project Requirements Fulfilled: ✅**
- ✅ Authentication system with login/logout
- ✅ Database connection with exception handling
- ✅ CRUD operations (Create, Read)
- ✅ Price-based filtering
- ✅ Form validation
- ✅ Responsive UI design
- ✅ Secure coding practices
- ✅ Error handling
- ✅ Clean project structure
