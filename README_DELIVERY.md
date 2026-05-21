# Payrole — Delivery Guide (Bangladesh Payroll)

## Quick deploy (production)

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
# Set DB_* in .env
php artisan migrate --force
php artisan db:seed --class=PolicySeeder
php artisan storage:link
# APP_DEBUG=false, APP_ENV=production
```

Optional `.env`:

```
ZKTECO_DEVICE_TOKEN=your-secret-token
```

Configure ZKTeco device to send header `X-Device-Token` or query `?token=...` when token is set.

## Before first payroll run

1. Add employees with **gross_salary** (and components if needed).
2. Mark attendance for the month (or connect ZKTeco).
3. **Admin → Policy** — verify late %, PF %, tax exempt (seeded defaults).
4. **Admin → Payroll → Process** — select month/year, POST process (not GET).
5. Review **Salary Sheet**, print payslips, mark paid.

## Features delivered in this build

- Unified **Bangladesh tax** calculator (`App\Services\Payroll\TaxCalculator`)
- Manual tax records respected; month format `1` / `01` both work
- **Employee PF only** deducted from net; employer PF stored as `company_pf` (info)
- **Policy-driven** late pay % and PF %
- **Permission checks** for payroll routes (super admin bypass)
- **Held salary** routes, **bulk payslip** view
- **Employee portal** payslips (`/employee/payslips`)
- Salary **audit** columns: `processed_by`, `updated_by`
- ZKTeco optional device token

## Roles

| Role | Access |
|------|--------|
| Super admin / permission_id=1 | Full admin |
| Admin with role permissions | Per `config/permissions.php` |
| Employee (`customer`) | Own payslips, attendance, leave |

## UAT checklist (same day)

- [ ] Process payroll for a test month (2–3 employees)
- [ ] Compare net pay with manual Excel
- [ ] Print single + bulk payslip
- [ ] Employee login → view own payslip
- [ ] Mark paid / held
- [ ] Export salary sheet

## Known limits (disclose to client)

- Tax uses **simplified NBR slabs** — confirm with CA for investment rebate, house rent exemption, etc.
- No government e-TDS / iT filing export
- Minimum wage / labour law rules not automated

## Support

Run tests: `php artisan test --filter=TaxCalculator`
