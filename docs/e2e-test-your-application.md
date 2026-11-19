# E2E Testing Protocol (Pest / Playwright)

* **Money Paths First:** 
  1. Student admission inquiry submission.
  2. Teacher submitting daily attendance.
  3. Parent paying fee invoice via gateway simulation.
* **Selectors:** Use semantic HTML attributes (`role`, `aria-label`, data-testid).
* **Unhappy Paths:** Test invalid login credentials, expired payment sessions, and missing required attendance fields.
* **Execution:** Run headless in CI (`npm run test:e2e`), headed locally for visual debugging.