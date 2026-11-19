# Ultra Plan Mode: SMS Feature Implementation

**Task:** Build Role-Based Authentication & Portal Routing for Laravel SMS
**Constraints:** Laravel 13, MySQL, Zero SPA framework overhead, Strict RBAC

## 1. File Analysis
* `routes/web.php` - Currently handles basic welcome view; needs refactoring into segregated route files.
* `app/Http/Middleware/` - Requires custom middleware (`RoleMiddleware`) verifying authenticated user roles.
* `database/migrations/` - Needs user role columns and multi-child parent association tables.

## 2. Current vs Target Behavior
* *Current:* Single guest landing page with default Laravel login.
* *Target:* Public website at `/` with role-based redirection post-login to `/admin/dashboard`, `/teacher/dashboard`, `/parent/dashboard`, etc., with strict access control.

## 3. Approaches & Tradeoffs
* *Approach A:* Single controller with `if/else` role checks. (High maintenance, security risk).
* *Approach B:* Separate controllers and isolated route files protected by middleware. (**Selected:** Clean separation of concerns, easy auditing).

## 4. Selected Approach Justification
Isolating route files and controllers prevents authorization leaks and scales cleanly across 5 distinct user roles.

## 5. Verifiable Step Sequence
1. Create `RoleMiddleware` and register in `bootstrap/app.php`. [Check: unauthorized users receive 403].
2. Establish role-specific route groups and controllers. [Check: `php artisan route:list` verifies paths].
3. Build responsive Tailwind navigation layouts for each role. [Check: visual inspection across breakpoints].

## 6. Risks & Rollback
* *Risk:* Infinite redirection loops on login. *Rollback:* Revert `RedirectIfAuthenticated` middleware.

## 7. Sensitive Area Sign-Off
* **[AUTH / PAYMENTS / PROD DATA]:** Requires explicit Super Admin sign-off before modifying middleware permission logic or payment gateway webhook handlers.