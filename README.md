# Telehealth System

A web-based telehealth management system built with PHP and MySQL. The system helps patients, caregivers, doctors, nurses, lab technicians, and administrators coordinate healthcare services from one platform.

## Features

- User registration and login with role-based access
- Patient appointment booking and appointment status tracking
- Doctor and nurse appointment management
- Patient records, prescriptions, and document uploads
- Lab test requests, lab result uploads, and lab status tracking
- Caregiver patient support, appointment chats, and document uploads
- Admin management for users, hospitals, labs, ratings, feedback, and reports
- Notifications and profile management

## Technologies

- PHP
- MySQL
- HTML, CSS, and JavaScript
- XAMPP/Apache for local development

## Setup

1. Copy the project folder into your XAMPP `htdocs` directory.
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Create a MySQL database named `telehealthDB`.
4. Import the database file:

   ```sql
   database/1061410-database.sql
   ```

5. Check the database connection settings in `db.php`.
6. Open the system in your browser:

   ```text
   http://localhost/Telehealth_system/home.php
   ```

## Main Roles

- **Admin:** manages users, hospitals, labs, reports, ratings, and feedback.
- **Patient:** books appointments, views prescriptions, uploads documents, and checks lab tests.
- **Doctor:** manages appointments, patient history, prescriptions, and lab requests.
- **Nurse:** assists with appointments and patient management.
- **Caregiver:** supports assigned patients and communicates about appointments.
- **Lab Technician:** manages lab tests, uploads results, and handles lab-related chats.

## Project Structure

- `admin/` - administrator pages
- `doctor/` - doctor dashboard and workflows
- `nurse/` - nurse dashboard and workflows
- `patient/` - patient dashboard and services
- `caregiver/` - caregiver dashboard and support tools
- `labtech/` - lab technician dashboard and lab test tools
- `process/` - backend form processing scripts
- `database/` - SQL database dump
- `css/` and `img/` - styling and images

