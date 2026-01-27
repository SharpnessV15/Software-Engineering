# Electricity Billing System Documentation
## Software Engineering Lab - Task 2 Compliance

### 1. Module Specifications

#### **A. Authentication Module (`login.php`)**
- **Input**: Registration Number, Password, Role.
- **Logic**: Verifies credentials against `users` table. Checks role.
- **Output**: Session start and redirect to dashboard (User/Admin/Reader).
- **Preconditions**: User must be registered.

#### **B. Registration Module (`register.php`)**
- **Input**: Name, Reg No, Phone, Connection Details, Address, Reg Type.
- **Logic**: 
  - Validates inputs (Regex for Name, Phone, Alphanumeric IDs). 
  - Concatenates address fields.
  - Inserts into `users` table.
- **Output**: Success/Error popup.
- **Preconditions**: Admin access required.

#### **C. Billing Calculation Module (`calculate_bill.php`)**
- **Input**: Units Used, Connection Type.
- **Logic**: 
  - **Min Charge**: 25 Rs (if units=0).
  - **Tiers**:
    - 0-50: 1.5 Rs
    - 51-100: 2.5 Rs
    - 101-150: 3.5 Rs
    - >150: 4.5 (Home) / 5.5 (Corp) / 6.5 (Ind).
- **Output**: Total Amount, Breakdown of calculation.

#### **D. Read Meter Module (`reader_dashboard.php`)**
- **Input**: User Reg No, Units, Date.
- **Logic**: Calls Calculation Module. Sets Due Date (+15 days). Inserts into `bills`.
- **Output**: Mock Bill Display, Database Insert.

#### **E. User View Module (`user_dashboard.php`)**
- **Input**: Session ID.
- **Logic**: Fetches bill history. Checks Due Date vs Current Date for Fine (150 Rs).
- **Output**: Bill Table with Status (Paid/Unpaid), Fine, Pay Button.

### 2. Billing Algorithm (Pseudo-code)

```text
FUNCTION calculateBill(units, type):
    IF units == 0:
        RETURN 25 (Minimum Charge)
    
    bill = 0
    IF type == 'staff':
        rates = [1.0, 2.0, 3.0, 3.0]
    ELSE IF type == 'corporate':
        rates = [1.5, 2.5, 3.5, 5.5]
    ELSE IF type == 'industrial':
        rates = [1.5, 2.5, 3.5, 6.5]
    ELSE:
        rates = [1.5, 2.5, 3.5, 4.5] (Home)

    // Tier 1
    units_t1 = MIN(units, 50)
    bill += units_t1 * rates[0]
    units -= units_t1

    // Tier 2
    IF units > 0:
        units_t2 = MIN(units, 50)
        bill += units_t2 * rates[1]
        units -= units_t2

    // Tier 3
    IF units > 0:
        units_t3 = MIN(units, 50)
        bill += units_t3 * rates[2]
        units -= units_t3

    // Tier 4
    IF units > 0:
        bill += units * rates[3]

    RETURN bill
```

### 3. Test Plan

| Test ID | Functionality | Input | Expected Output | Actual Output |
| :--- | :--- | :--- | :--- | :--- |
| **TC-01** | Registration Name Validation | Name: "John123" | Error: "Alphabets only" | Pass |
| **TC-02** | Registration Phone Validation | Phone: "123456" | Error: "Exactly 10 digits" | Pass |
| **TC-03** | Minimum Bill Calculation | Units: 0 | Bill: 25.00 Rs | Pass |
| **TC-04** | Home Tier 1 Calculation | Units: 50, Type: Home | 50 * 1.5 = 75.00 Rs | Pass |
| **TC-05** | Corporate Tier 4 Calculation | Units: 151, Type: Corp | (75+125+175) + 1*5.5 = 380.50 | Pass |
| **TC-06** | Fine Calculation | Due Date: Yesterday, Status: Unpaid | Amount + 150 Rs | Pass |
| **TC-07** | Payment Flow | Click "Pay Now" | Status changes to "Paid" | Pass |

### 4. Quality Characteristics
- **Usability**: Simple Dashboard with clear "Pay Now" actions.
- **Efficiency**: Centralized `calculate_bill.php` avoids code duplication.
- **Reusability**: `header.php` and `calculate_bill.php` used across multiple pages.
- **Interoperability**: Standard HTML/PHP stack compatible with any LAMP server.
