# Card Inventory Management System

A web-based system for managing card inventory, tracking production, and generating reports for banks and card production facilities.

## Features

- **Card Management**
  - Track different types of cards (Credit, Debit)
  - Monitor card quantities in storage
  - Record card specifications (chip type, association, expiry)

- **Inventory Control**
  - Deposit new cards into storage
  - Withdraw cards for production
  - Track rejected cards and replacements

- **Reporting System**
  - Generate detailed reports for banks
  - Track production progress
  - Monitor card usage and rejections

- **Role-Based Access**
  - Logistics Officers: Manage card storage
  - Production Officers: Verify transactions
  - Bank Users: View their card progress

## Requirements

- PHP 8.0+
- MySQL/MariaDB
- Web Server (Apache/Nginx)

## Installation

1. Clone the repository
2. Create a database and import `card_inventory.sql`
3. Copy `.env.example` to `.env` and update the database credentials
4. Configure your web server to point to the project directory

## Security

This repository contains sensitive code. Make sure to:
- Never commit `.env` files
- Keep database credentials secure
- Follow proper authentication practices

## License

[Add appropriate license]


################################################

view_transaction kena fix bagi display deposit shj - kat model
dah siap withdraw (button Finish Withdaw), success message (withdraw.php)
generate report 