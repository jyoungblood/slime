# Changelog

## 2.0.0 — 2026-11-04

- **Breaking:** Upgrade to DBKit 2. Pass query values separately from SQL, and move read options to argument four. See the README migration guide.
- Initialize the database inside request middleware so Slim handles connection failures. Run database work inside request handlers, not controller registration.
- Upgrade the Slim framework to `^4.15.3`.
- Add 7 bootstrap smoke tests for optional database setup, error responses, and DBKit 2 parameter binding.
