# Developer Documentation: Card Inventory Management System

This document provides a comprehensive overview of the Card Inventory Management System codebase, architecture, and key functionalities. It is intended for developers working on this project to get up to speed quickly.

## 1. Project Overview

The project is a PHP-based Card Inventory Management System designed for managing credit and debit card inventory, tracking card production and movements (deposits, withdrawals), and generating bank-specific reports. It features role-based access control to cater to different user responsibilities within the card management lifecycle.

- **Core Purpose**: Inventory control, transaction tracking, and reporting for payment cards.
- **Technology Stack**: Native PHP (no frameworks), MySQL/MariaDB, HTML, CSS, JavaScript.
- **Key Features**:
    - User Authentication & Role-Based Access Control (RBAC)
    - Card Inventory Management (CRUD)
    - Transaction Logging (Deposits, Withdrawals)
    - Reporting Workflow (Creation, Verification, Rejection)
    - Bank and User Management

## 2. Architecture

The application follows a Model-View-Controller (MVC)-like architectural pattern, although not strictly enforced as it would be in a modern framework.

### Directory Structure

```
.
├── controllers/        # Business logic, handles requests
│   ├── AuthController.php
│   ├── CardController.php
│   ├── ReportController.php
│   └── ...
├── css/                # Stylesheets
│   └── styles.css
├── db/                 # Database connection logic
│   └── database.php
├── models/             # Data logic, database interaction
│   ├── CardModel.php
│   ├── ReportModel.php
│   ├── TransactionModel.php
│   └── UserModel.php
├── views/              # Presentation layer (HTML templates)
│   ├── auth/
│   ├── card/
│   ├── includes/       # Shared components (header, footer, sidebar)
│   └── ...
├── .env.example        # Template for environment variables
├── .gitignore
├── index.php           # Main entry point & front controller/router
└── README.md
```

## 3. Routing

Routing is handled by a simple front controller mechanism in `index.php`.

- **Entry Point**: All requests are directed to `index.php`.
- **Mechanism**: A `path` GET parameter determines which controller and method to execute.
- **Example**: `index.php?path=card/create` would likely route to the `create()` method in `CardController`.
- **Authentication**: `index.php` also includes logic to check if a route requires authentication before dispatching the request. Public routes (like login) are explicitly defined.

## 4. Database

### Configuration
- Database credentials (`DB_HOST`, `DB_USERNAME`, `DB_PASSWORD`, `DB_DATABASE`) are managed through an `.env` file.
- A `.env.example` file is provided as a template.

### Connection
- The connection is managed by the `Database` class in `db/database.php`.
- It uses a **Singleton pattern** to ensure only one database connection instance exists.
- It uses `mysqli` for database interaction.

### Schema Overview
The database consists of the following key tables:
- `users`: Stores user credentials, roles, and associated bank.
- `roles`: Defines user roles (e.g., Admin, Bank, Logistics Officer).
- `banks`: Stores bank information.
- `cards`: The main inventory table for card stock, including quantity.
- `transactions`: Logs all card movements (deposits, withdrawals) and links to cards and reports.
- `reports`: Aggregates transactions for verification and submission by banks.
- `rejections`: Stores information about rejected quantities from a transaction within a report.

## 5. Controllers (Business Logic)

Controllers orchestrate the application flow, handling user input, interacting with models, and rendering views.

### `AuthController.php`
- **Responsibilities**: User authentication, session management, and authorization (RBAC).
- **Key Methods**:
    - `login()`: Verifies user credentials and starts a session.
    - `logout()`: Destroys the session.
    - `isAdmin()`, `isBank()`, `isLogisticsOfficer()`, `isProductionOfficer()`: Role-checking helper methods used throughout the application to enforce permissions.
    - `authMiddleware()`: Protects routes that require a logged-in user.

### `CardController.php`
- **Responsibilities**: Manages all card and card transaction-related actions.
- **Key Methods**:
    - `index()`: Lists all cards, grouped by bank. Can be filtered by card type.
    - `create()`/`store()`: Display and handle the new card creation form.
    - `details($cardId)`: Shows detailed information for a single card.
    - `viewTransactions($cardId)`: Displays the transaction history for a card.
    - `depositCardForm()`/`processDeposit()`: Display and handle the deposit of cards into inventory.
    - `editTransactionForm()`/`updateTransaction()`: Display and handle the editing of a transaction.

### `ReportController.php`
- **Responsibilities**: Manages the entire reporting lifecycle.
- **Key Methods**:
    - `index()`: Lists all reports.
    - `bank_reports()`: Displays reports specific to the logged-in bank user.
    - `withdraw()`/`processWithdraw()`: Handles the withdrawal of cards from inventory, creating a 'withdraw' transaction.
    - `submitReport()`: Submits a set of withdrawal transactions for verification.
    - `verify($reportId)`: Displays the verification screen for a report.
    - `processVerification()`: Marks a report and its transactions as verified.
    - `rejectCard()`: Handles the rejection of a certain quantity from a withdrawal transaction.

## 6. Models (Data Access Layer)

Models are responsible for all database interactions. They use prepared statements to prevent SQL injection.

### `UserModel.php`
- **Responsibilities**: CRUD operations for users.
- **Interacts with**: `users`, `roles`, `banks` tables.
- **Key Methods**: `getAllUsersWithRoles()`, `getUserByEmail()`, `addUser()`, `updateUser()`, `deleteUser()`.

### `CardModel.php`
- **Responsibilities**: Manages `cards` table and related deposit/update transactions.
- **Interacts with**: `cards`, `banks`, `transactions` tables.
- **Key Methods**: `getBanksWithCards()`, `addCard()`, `getCardDetails()`, `addDeposit()`. Uses database transactions for atomicity (e.g., updating card quantity and creating a transaction record together).

### `ReportModel.php`
- **Responsibilities**: Complex queries and operations for reporting and withdrawals.
- **Interacts with**: `reports`, `transactions`, `cards`, `banks`, `rejections` tables.
- **Key Methods**: `getReportsByBank()`, `withdrawCard()`, `createWithdrawalReport()`, `verifyReport()`, `addRejection()`.

### `TransactionModel.php`
- **Responsibilities**: Focuses on modifying transactions, especially in the context of rejections and report assignments.
- **Interacts with**: `transactions`, `reports`, `rejections` tables.
- **Key Methods**: `rejectTransaction()`, `assignToReport()`, `markTransactionVerified()`.
- **Note**: There is some functional overlap with `ReportModel` which could be a target for future refactoring.

## 7. Views (Presentation)

Views are PHP files that render HTML. They are organized into folders by feature.

### Shared Components (`views/includes/`)
- `header.php`: Contains the HTML head, links to CSS/JS, and the site header.
- `sidebar.php`: Contains the main navigation menu. It dynamically shows/hides links based on the user's role.
- `footer.php`: Contains the site footer and closes the `<body>` and `<html>` tags. It also contains some global JavaScript.

### Data Display
- Views receive data from controllers (e.g., as instance properties like `$this->banks`) and use PHP loops (`foreach`) and echo statements (`<?= ... ?>`) to render it.

## 8. Frontend (CSS & JavaScript)

### CSS
- The main stylesheet is `css/styles.css`. It contains general styling for layout, tables, forms, sidebar, etc.
- **Known Issue**: There are numerous instances of component-specific CSS being defined in inline `<style>` blocks within PHP view files (e.g., `sidebar.php`, `card/index.php`). This should be refactored into the main stylesheet for better maintainability.

### JavaScript
- JavaScript is used for minor UI enhancements.
- **Global Script**: `footer.php` contains a script for the accordion-style display of bank card lists.
- **View-Specific Scripts**: Some views contain their own `<script>` blocks for specific functionality (e.g., `card/index.php` has a script to auto-expand the card list for bank users).

## 9. Security

- **Authentication**: Session-based login is managed by `AuthController`.
- **Authorization**: Role-Based Access Control (RBAC) is enforced in controllers and views by checking the user's role (`isAdmin()`, `isBank()`, etc.).
- **SQL Injection**: Models consistently use `mysqli` prepared statements to prevent SQL injection vulnerabilities.
- **Cross-Site Scripting (XSS)**: User output is generally escaped using `htmlspecialchars()`, though a full audit would be beneficial.

## 10. Known Issues & Areas for Improvement

- **Refactor Inline CSS**: All `<style>` blocks should be removed from `.php` files and their contents moved to `css/styles.css` using general-purpose classes.
- **Refactor Inline JS**: Scripts in view files should be moved to a dedicated `.js` file.
- **Error Handling**: The application often uses `die()` or `exit()` on error, which is not user-friendly. A more robust error handling and logging mechanism should be implemented.
- **Routing System**: The current `path`-based routing is simple but can become difficult to manage as the application grows. A more structured routing system would be beneficial.
- **Model Responsibilities**: The responsibilities between `ReportModel` and `TransactionModel` overlap. Their roles could be clarified and refactored for better separation of concerns.
- **Dependency Injection**: The application uses direct instantiation (e.g., `new AuthController()`). Using a dependency injection container would improve testability and decoupling.
