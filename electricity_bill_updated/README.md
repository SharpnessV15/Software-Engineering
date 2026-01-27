# Electricity Billing System

Welcome to the **Electricity Billing System**! This application allows you to manage electricity bills, track payments, and calculate charges based on different connection types.

## Getting Started

Follow these simple steps to get the application up and running on your local machine.

### 1. Database Setup
1.  Open **phpMyAdmin** in your browser (usually `http://localhost/phpmyadmin`).
2.  Create a new database named `electricity_app` (if it doesn't exist).
3.  Click on the **Import** tab.
4.  Upload the `electricity_app.sql` file located in this project folder.
5.  Click **Go** to import the sample data and tables.

### 2. Login Credentials
We have pre-created some accounts for you to test the system immediately:

#### **Administrator** (Full Access)
*   **User ID**: `admin`
*   **Password**: `admin123`

#### **Meter Reader** (Add Bills)
*   **User ID**: `reader`
*   **Password**: `reader123`

#### **Standard Users** (View Bills)
*   **Alice Home** (ID: `1001` - Home Connection)
*   **Bob Corp** (ID: `1002` - Corporate Connection)
*   **Charlie Factory** (ID: `1003` - Industrial Connection)
*(Note: Standard users do not require a password for this demo)*

## Key Features
*   **Real-time Billing**: Calculates bills automatically based on unit consumption and connection type (Home, Corporate, Industrial, Staff).
*   **Smart Fines**: Automatically applies a **150 Rs/month** fine for overdue bills calculated from the due date.
*   **Detailed Breakdown**: Shows exactly how your bill is calculated, including tier-wise charges.
*   **Payment Tracking**: Easily pay unpaid bills and view your complete payment history.
*   **Secure Access**: Role-based access ensures Admins, Readers, and Users only see what they need to.

## How to calculate a Fine?
If a bill is unpaid after its due date (15 days from billing), a cumulative fine starts accumulating every month.
*   **Formula**: `150 Rs` x `(Months Late)`

Need help? Check the `DOCUMENTATION.md` file for technical details.
