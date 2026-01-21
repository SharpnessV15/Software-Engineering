# Electricity Billing System - Setup Guide

## 1. Database Import
To set up the database with fresh data:
1.  Open **phpMyAdmin** (usually at `http://localhost/phpmyadmin`).
2.  Select your database (e.g., `electricity_app` or create a new one).
3.  Click the **Import** tab.
4.  Choose the file `electricity_app.sql` from your project folder.
5.  Click **Go** to run the SQL script.

## 2. Default Login Credentials
Use these accounts to access the system:

### Administrator
- **Registration Number**: `admin`
- **Password**: `admin123`
- **Connection Type**: Staff (Subsidized rates)

### Meter Reader
- **Registration Number**: `reader`
- **Password**: `reader123`
- **Connection Type**: Staff (Subsidized rates)

## 3. Test Users (No Password)
These users are for billing testing only (Role: User):
- `1001`: **Home** Connection
- `1002`: **Corporate** Connection
- `1003`: **Industrial** Connection

## 4. Features
- **Roles**: Admin, Reader, User.
- **Connection Types**: Home, Corporate, Industrial, Staff.
- **Validation**: Strict regex validation with popup alerts.
- **Billing**: Tiered rates based on connection type.
