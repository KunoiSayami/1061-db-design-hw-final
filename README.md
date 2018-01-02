# 1062 Database Design Final Homework
Database Design Final Homework: Message board (Website base)

## Status
Finished

## Special Thanks
Redblaze seniors helped me test the feasibility of SQL injection

## Project Target
* Create a message board in website
* Message board can be add, edit also delete
* Not permission control

## Installation
* Import `comm.sql` to your database
* Edit the database username, password and so on in `homework.php`
* Copy `homework.php` to your own http server which has php support

### PHP Configure

```php
	// Variable settings

	// Show on user header
	$USER_STRING = "";

	// Database Setting

	// Database Name
	$DATABASE_NAME = ''; 
	// Table Name
	$TABLE_NAME = '';
	// Database connect
	$DATABASE_LOCATION = 'localhost';
	// Database user name
	$DATABASE_USER_NAME = '';
	// Database user password
	$DATABASE_USER_PASSWORD = '';

	// Button text setting
	// Do not modify unless necessary

	// Edit button text
	$EDIT_BUTTON = 'Post';
	// New comment button text
	$POST_NEW_BUTTON = 'Insert';

	// Self File name setting
	// Do not modify unless necessary

	// Redirect file name
	$FILE_NAME = basename($_SERVER['PHP_SELF']);
```

## License
General Public License 3