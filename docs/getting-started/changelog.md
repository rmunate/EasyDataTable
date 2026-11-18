---
title: Release Notes
editLink: true
outline: deep
---

# Release Notes

## 3.0.0

- Adds Laravel 13.x support and declares the database and HTTP components used directly. Laravel 8.x through 12.x remain supported.
- Corrects filtered counts for grouped and distinct queries, searching for `0`, and `length: -1`. Processing no longer changes the supplied builder.
- **Upgrade:** invalid or incomplete configurations now throw `DatatableException`. Requested ordering must use a selected field or alias. If you relied on ordering by the first column when no order is sent, set `orderBy()` on the base query.
- `query()` and `fromData()` replace the previous source. Third-party implementations of `IEasyDataTable` must implement `fromData(iterable $data)`.
- GitHub Actions runs SQLite integration tests and builds the documentation before deploying to Pages.

## [2.0.0]

- Adjusted to be compatible with Laravel 11