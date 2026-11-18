---
title: Report a bug
description: Collect PHP and Laravel versions, a minimal example and the failing DataTables request before reporting a bug.
---

# Report a bug

Start with [troubleshooting](../reference/troubleshooting.md) to rule out configuration, field names and HTTP response issues. If the behavior is still unexpected, collect a small reproduction.

## What to include

- **Versions:** output of `php -v`, `php artisan --version` and `composer show rmunate/easy-datatable`, plus your database engine and your DataTables version.
- **Setup:** the processing mode, route or controller code, query projection, and the JavaScript `columns` and `ajax` config. A few synthetic fixture rows are enough.
- **Reproduction:** the action you took, exact expected result and actual result. For a failed draw, include the request parameters, HTTP status, and a trimmed JSON response or exception message.

::: warning Use sample data
Remove cookies, credentials, personal records and internal URLs before sharing logs or requests.
:::

## Send the report

[Check whether the issue is already open](https://github.com/rmunate/EasyDataTable/issues), then [open a new issue](https://github.com/rmunate/EasyDataTable/issues/new) with the details. For a documentation problem, link to the page and name the step that is unclear or no longer works.
