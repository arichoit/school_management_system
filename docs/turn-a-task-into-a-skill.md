# Turn a Task Into a Skill (.AI/skills/school-report-generator/SKILL.md)

* **Task:** Automated PDF Term Report Card Generation.
* **Workflow:**
  1. Filed student enrollments and exam grades for active academic term.
  2. Calculate GPA and attendance percentages.
  3. Render print-optimized Blade view (`resources/views/reports/card.blade.php`).
  4. Output downloadable PDF via Laravel Browsershot / DomPDF.
* **Done Definition:** Report card generates cleanly with zero alignment overflow and accurate grade calculations.