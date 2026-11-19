# Full UI & UX Design Brief: SMS Portal & Website

**Audience:** Admins, Teachers, Students, Parents, Accountants, Public Visitors
**Brand:** Trustworthy Academic Navy (`#1E3A8A`), Slate Gray (`#64748B`), Emerald Success (`#10B981`), Warm Paper (`#F8FAFC`). Primary font: Inter.

## 1. User Journey: Parent Checking Child Progress
1. Parent lands on public homepage (`/`) -> Clicks "Portal Login".
2. Enters credentials -> Redirected to `/parent/dashboard`.
3. Default view shows Child A summary -> Uses child switcher dropdown to select Child B.
4. Views attendance percentage graph, recent grades, and pending fee invoices.
5. Clicks "Pay Now" on fee invoice -> Redirected to secure payment gateway.

## 2. Layout & Breakpoints
* **Mobile First (`<640px`):** Single-column stacked cards, hamburger slide-over menu, bottom action bar for teachers.
* **Tablet (`640px - 1024px`):** Collapsible sidebar, 2-column widget grids.
* **Desktop (`>1024px`):** Fixed sidebar, multi-column analytics dashboards, responsive data tables.

## 3. Component Inventory & States
* **Data Table:** Hover highlight (`bg-slate-50`), empty state with illustration, loading skeleton.
* **Button:** Primary (Navy background, white text, hover `bg-blue-900`, active scale), secondary (slate border).

## 4. Accessibility (a11y)
* WCAG 2.1 AA compliant contrast ratios across all badge and text pairings.
* Full keyboard navigation support (`tabindex`, `aria-expanded` on mobile menus).