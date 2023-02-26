## Features
* Built with Laravel and Vue
* Authentication via Laravel Sanctum
* Database Seeder for admin account and 10,000 records
* Sort/filterable dataset export via PhpSpreadsheet

## Description
This application was built for a members-only sailing club in Sydney, Australia. It allows non-members and guests of members to sign-in (via a physical QR code at recption) and enter the premises. Upon submitting the required information, the front-facing application redirects the guest to a confirmation page and display a ticket code for future reference. 

Each successful form submission is logged to a 'records' table on a SQL database and is accessible via a secure admin panel (located at *./admin/records*). Once logged in, authorized users can access a custom data table of processed records with a number of sort and filter methods: 
 - asc/desc based on name, ticket no, address, date, etc.
 - filter by substring contained within a record, or by start/end date range,
 
Records can also be manually created or deleted from the admin UI. To ensure data integrity, individual records cannot be updated/edited (they must be deleted and then re-entered using the current timestamp, if necessary).

Exporting record data is possible through the green button at the top right of the interface. When exporting, all the current parameters that make up the data table's current 'state' will be applied and only those records that pass the filtering process will be exported. For example, if the system contains 10,000 total records but a substring filter is applied that only returns 100 records, only those 100 records will be added to the export file (and in the sort order displayed on the screen). 

## Installation 

## License
You are free to use this project for personal use, however you may not redistribute it or use it for commercial purposes without permission. All artwork, logos and trademarks are copyrights of their respective owners. © Brandon Sterling 2023
