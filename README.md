# Comic Vault (PHP + MySQL)

A simple comic book collection manager made with PHP and MySQL (mysqli). The home page is public and shows random comics. After logging in, **My Collection** shows only your own comics, where you can add, edit and delete them.

**Login (admin):** admin@example.com / Admin@123

**Login (user):** test@example.com / test123

## Three kinds of visitors
- **Guest** (not logged in): vermilion accent, 12 random comics in Discover, read-only
- **User:** green accent, 24 random comics in Discover, plus **My Collection** (add / edit / delete only their own comics)
- **Admin:** indigo accent, **Dashboard**, **Users** page (change role, delete) and full add / edit / delete on every comic

## Files
- `connection.php` - connects PHP to the MySQL database (`mysqli_connect`) and holds the small helper functions
- `index.php` - Discover page (search + display comics)
- `form.php` - HTML form + PHP form processing (INSERT and UPDATE)
- `delete.php` - DELETE query
- `mycollection.php`, `comic.php`, `add_to_collection.php` - SELECT / INSERT queries
- `login.php`, `register.php`, `logout.php` - accounts and sessions
- `admin/dashboard.php`, `admin/users.php` - admin pages
- `includes/` - header, footer, search form and comic cards shared by the pages
- `assets/style.css` - the CSS design, `assets/app.js` - form checking

## Setup (XAMPP)
1. Copy this folder to `C:\xampp\htdocs\comicbooks`.
2. Start Apache and MySQL in the XAMPP Control Panel.
3. Open http://localhost/phpmyadmin, create a database named `comics`, then import `database/schema.sql`.
4. Check the settings at the top of `connection.php` (database name, user `root`, empty password).
5. Open http://localhost/comicbooks/

## Database files
- `database/schema.sql` - new database (tables + 20 sample comics + admin account)
- `database/upgrade_covers.sql` - existing database: makes collected comics follow the catalog cover (run once)
- `database/upgrade.sql` - existing database: adds role / last login columns
- `database/more_comics.sql` - existing database: adds 15 more sample comics
