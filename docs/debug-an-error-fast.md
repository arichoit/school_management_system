# Debug an Error Fast: Protocol

**Error Template:**
* **Stack Trace:** [PASTE FULL ERROR]
* **Expected vs Actual:** State exact divergence in 1 sentence.
* **3 Hypotheses:** Ranked by likelihood.
* **Evidence Gathering:** Check logs (`storage/logs/laravel.log`) or run targeted tinker tests.
* **Root Cause Fix:** Fix underlying architectural cause, never mask symptoms.
* **Regression Test:** Add Pest/PHPUnit test covering the edge case.