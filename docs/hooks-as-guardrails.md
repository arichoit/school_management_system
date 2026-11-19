# AI Code Hooks & Guardrails

* **PostToolUse:** Automatically run `vendor/bin/pint` and `php artisan test` after file edits.
* **PreToolUse:** Block edits to protected paths (`database/migrations/`, `.env`, `config/`).
* **Stop:** Run test suite before concluding session.
* **Notifications:** Sound alert when human input or approval is required.