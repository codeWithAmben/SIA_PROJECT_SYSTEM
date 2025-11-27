Simple Farm System - Quick Notes

Endpoints and behaviors:
- `php/user-login.php` — Login endpoint; POST `email` (or username) and `password` to validate users (sets `$_SESSION['user']`) or admin (sets `$_SESSION['admin_logged']`). Returns JSON for AJAX or performs redirect for form POSTs.
- `php/register.php` — Register new user; POST `name`, `email`, `password` to create user in `data/users.xml`.
- `php/save-note.php` — Adds a note; now requires an authenticated session (either logged-in user or admin). Unauthorized POSTs return 401 JSON or redirect to index login modal.
- `php/save-entity.php` — Admin-only generic endpoint to add records for `animals`, `crops`, `users`, `tasks`, and `notes` (notes can also be created by logged-in users). `password` fields are hashed when saving user entries.
- `php/whoami.php` — Returns JSON with `user` (if logged-in) and `admin` flag for the client to check auth state.
 - `php/partials/header.php` — New partial that provides a reusable navigation bar, login button, and session flash display. Include it in pages with `include __DIR__ . '/php/partials/header.php'` (use `__DIR__` path as appropriate from pages.
 - `php/partials/footer.php` — New footer partial to be included on pages via `include __DIR__ . '/php/partials/footer.php'`.

Development notes:
- This is a demo running on XAMPP. Passwords are hashed using `password_hash`. For production, use robust auth, database-backed storage, and secure session handling.

