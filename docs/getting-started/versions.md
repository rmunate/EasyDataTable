---
title: Version compatibility
description: Check the Laravel and PHP combinations supported by EasyDataTable 3.0.0.
---

# Version compatibility

These docs describe **EasyDataTable 3.0.0**. The package requires `illuminate/database` and `illuminate/http` from Laravel 8 through 13; Composer resolves them to the versions compatible with your app.

| Laravel | PHP used to test EasyDataTable in CI |
| --- | --- |
| 13.x | 8.3, 8.4 and 8.5 |
| 11.x, 12.x | 8.2 |
| 10.x | 8.1 |
| 9.x | 8.1 |
| 8.x | 7.4 |

Use the PHP version required by your installed Laravel release and its dependencies. The CI matrix shows which combinations the project checks; it does not mean every minor PHP version in between is tested. For new apps, choose a Laravel and PHP release that still receives updates.

## Frontend compatibility

The sample views use DataTables **2.3.4** and jQuery **3.7.1**. The leave dashboard also uses Responsive **3.0.7**. These versions are pinned in the examples, not required by Composer. A frontend using other versions must still follow the [request and response contract](../reference/api.md).

## Updating your app

Run `composer show rmunate/easy-datatable` and `php artisan --version` in your app to identify the installed versions. Read the [release notes](./changelog.md) before upgrading. Compatibility with older PHP or Laravel releases is not a recommendation to deploy end-of-life software, and future Laravel majors are not declared compatible until tested.

Compatibility with older versions is not a recommendation to run end-of-life PHP or Laravel releases. Future Laravel majors are not declared compatible until tested.
