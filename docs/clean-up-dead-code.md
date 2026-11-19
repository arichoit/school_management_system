# Dead Code Cleanup Protocol

* **Scope:** Whole repository (`app/`, `resources/`, `routes/`).
* **Checks:**
  * Unused Blade partials and orphaned CSS utility classes.
  * Unreferenced controller methods and middleware.
  * Stale package dependencies in `composer.json` and `package.json`.
* **Verification:** Run `php artisan test` and Vite build after every cleanup batch.