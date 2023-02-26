## Features
* Built with Laravel and Vue
* Authentication via Laravel Sanctum
* Database Seeder for admin account and 10,000 records
* Sort/filterable dataset export via PhpSpreadsheet

## Description
This application was built for a members-only sailing club in Sydney, Australia. It allows non-members and members of guests to sign-in (via a physical QR code at recption) and enter the premises. Upon submitting the required information, the front-facing application redirects the guest to a confirmation page and display a ticket code for future reference. 

Each successful form submission is logged to a 'records' table on a SQL database and is accessible via a secure admin panel (located at './admin/records').

## License
This project is a portfolio piece built as a proposal for an external company. You are free to use it for personal use, however you may not redistribute it or use it for commercial purposes without permission. All artwork, logos and trademarks are copyrights of their respective owners. © Brandon Sterling 2023
