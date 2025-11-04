# COMS Development Plan

## 1. Database Setup
- [x] Create schema.sql for coms_db

## 2. Backend Files Creation
- [x] Create db.php (database connection)
- [x] Create session.php (session management and role checks)
- [x] Create login.php (authentication page)
- [x] Create logout.php (session destruction)
- [x] Create superadmin_dashboard.php (superadmin homepage)
- [x] Create admin_dashboard.php (admin/cashier homepage)
- [x] Create manage_users.php (user management for superadmin)
- [x] Create manage_products.php (product CRUD)
- [x] Create pos.php (point of sale interface)
- [x] Create reports.php (transaction reports with filters)
- [x] Create generate_pdf.php (PDF report generation)
- [x] Create api/submit_order.php (order submission API)
- [x] Create api/toggle_user_status.php (user status toggle API)

## 3. Adapt POS (Convert to Dynamic)
- [x] Convert index.html to index.php
- [x] Make menu dynamic from database (keep Blend S design)
- [x] Integrate cart JS with order submission to backend

## 4. Copy Assets
- [x] Copy uploads/ to COMS/uploads/
- [x] Copy lib/ to COMS/lib/

## 5. Default Data
- [x] Insert default superadmin user
- [x] Insert sample coffee products from COMS menu

## 6. Testing
- [x] Test database setup and default data
- [x] Test login system and role-based redirects
- [x] Test user/product management
- [x] Test POS (add to cart, payment, order submission)
- [x] Test reports (filtering, PDF export)
- [x] Verify layout/design unchanged (Blend S theme)
- [x] Ensure runs at http://localhost/concepcion/COMS
