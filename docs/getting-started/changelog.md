---
title: Release Notes
description: EasyDataTable changes and the steps for updating existing integrations to 3.0.0.
---

# Release Notes

## 3.0.0

### What's new

- Laravel 13.x is supported alongside Laravel 8.x through 12.x. The package declares the database and HTTP components it uses directly.
- Server-side draws process a clone of the supplied builder. Filtered counts for grouped and distinct queries, searches for `0`, and `length: -1` are handled correctly.
- GitHub Actions runs SQLite integration tests across the [supported matrix](./versions.md) and builds the documentation before Pages deployment.

### Upgrade from 2.x

1. Change your Composer constraint to `^3.0` using the [installation command](./installation.md#install-the-package).
2. Review every endpoint. Use `fromData($rows)` for prepared data, `clientSide()->query($builder)` for a full query result, or `serverSide()->query($builder)->request($request)` for a page. Do not pass a request or a search callback in client-side mode.
3. Align JavaScript `columns[].data` with the selected SQL field or alias. Invalid or incomplete configurations now throw `DatatableException`. When no order is sent, no first-column default is applied by the package; set `orderBy()` on the base query if you need a predictable default.
4. If you implement `IEasyDataTable` yourself, add `fromData(iterable $data)`. Test each migrated endpoint for a search, a sort and an empty result.

See the [API reference](../reference/api.md) for the full contract.

## 2.0.0

- Added Laravel 11 support.