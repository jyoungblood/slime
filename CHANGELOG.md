# Changelog

## 1.3.0 — 2026-11-04

- **Breaking API change:** require DBKit `^2.0.1` and lock it to 2.0.1. Use column/value criteria maps or `[SQL, ...values]` tuples, with read options third. See the README migration guide.
- **Migration from DBKit 2.0.0:** replace SQL plus separate parameter arrays with column/value maps or `[SQL, ...values]` tuples; read options return to argument three. Database initialization and insert calls are unchanged.
- Initialize the database inside request middleware so Slim handles connection failures. Run database work inside request handlers, not controller registration.
- Upgrade the Slim framework to `^4.15.3`.
- Update the existing 7 bootstrap smoke tests for optional database setup, error responses, and DBKit 2.0.1 criteria binding.

The briefly published Slime 2.0.0 release is withdrawn in favor of 1.3.0. Applications pinned to Slime `^2.0` must change their constraint to `^1.3`; existing lockfiles are not rewritten automatically.
