Employee Payroll CRUD System
Description: This is a simple Employee Payroll CRUD (Create, Read, Update, Delete) system built with PHP and MySQL. It allows admins to manage employee payrolls, including viewing, editing, and updating employee pay details. The system includes secure login, user registration, and a password reset feature.

Features
User Registration: New users can create an account by filling in the necessary details.

Login System: Registered users can log in securely.

Forgot Password: Allows users to reset their password using a token-based system sent to their email.

Admin Dashboard: Admins can manage users, view employee payroll data, and perform CRUD operations.

Payroll Management: Admins can add, view, edit, and delete payroll details for employees.

### Screenshots

1. **Login Page**  
    ![Login Page](images\LOGINPAGE.png)  
    The login page allows users to log into the system by entering their email and password.

2. **Register Page**  
    ![Register Page](images\REGISTER.png)  
    New users can register by providing their details like name, email, and password and role.

3. **Forgot Password Page**  
    ![Forgot Password Page](images\FORGORPASSWORD.png)  
    If users forget their password, they can reset it using a token sent to their email.

4. **Admin Dashboard**  
    ![Admin Dashboard](images\ADMINDASHBOARD.png)  
    The Admin Dashboard provides an overview of all employee data, including payroll details and the ability to perform CRUD operations.

5. **Payroll Page**  
    ![Payroll Page](images\PAYROLL.png)  
    The Payroll page displays employees' pay information such as regular hours, overtime, and total salary.

6. **Edit Payroll Page**  
    ![Edit Payroll Page](images\UPDATEPAYROLL.png)  
    Admins can edit the payroll details for employees, including their hourly rate, regular hours, and overtime.


How to Run the Project
1. Clone the Repository:
bash
Copy
Edit
git clone https://github.com/yourusername/employee-payroll-system.git
2. Install Dependencies:
Ensure you have a PHP server like XAMPP or MAMP installed.
bash
Copy
Edit
# Navigate to your project directory
cd employee-payroll-system
3. Set Up the Database:
Create a MySQL database and import the provided database.sql file (or manually set up the database schema).

Update the database connection settings in config.php.

Example database connection setup:

php
Copy
Edit
$conn = new mysqli('localhost', 'root', '', 'employee_payroll');
4. Run the Application:
Start your PHP server and open the application in your browser at http://localhost/employee-payroll-system.

Technologies Used
Backend: PHP
Frontend: HTML, Tailwind CSS, JavaScript, 
Database: MySQL


