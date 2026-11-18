---
description: Install EasyDataTable with Composer, start Laravel locally and verify your setup before building the first table.
---

# Installation

Install the PHP package in your Laravel application. DataTables remains a separate frontend dependency; the first example includes all the browser assets it uses.

## 1. Check your environment

You need a Laravel application, **Composer 2** and a PHP version supported by your Laravel major. For **Laravel 13**, use **PHP 8.3 or later within PHP 8.x**. See the [version matrix](./versions.md) for Laravel 8 through 12.

```sh
php -v
composer --version
```

In an existing Laravel application, run `php artisan --version` from the directory that contains `artisan` and `composer.json`. If you are starting from scratch, create a local Laravel 13 app first:

```sh
composer create-project "laravel/laravel:^13.0" easy-datatable-demo
cd easy-datatable-demo
```

## 2. Install and confirm the package {#install-the-package}

Run both commands from the **Laravel application root**, not from a clone of EasyDataTable:

```sh
composer require "rmunate/easy-datatable:^3.0"
composer show rmunate/easy-datatable
```

The second command should list `rmunate/easy-datatable` with an installed **3.x** version. Use `Rmunate\EasyDatatable\EasyDataTable` as the PHP import.

::: info What installation changes
Composer makes the class available through autoloading. There is no service provider to register, configuration to publish, or migration to run for the package itself.
:::

## 3. Start the local app

The first recipe does not query a database. If you are using a **fresh demo app without a database** and the `web` middleware stores sessions in one, set `SESSION_DRIVER=file` in your app's `.env`, then run `php artisan config:clear`. A fresh Laravel app also needs a nonempty `APP_KEY`; run `php artisan key:generate` **only if your new app has no key**.

```sh
php artisan serve --host=127.0.0.1 --port=8000
```

Keep that terminal open. Open <http://127.0.0.1:8000> to verify the app responds. The example routes will exist after you add them in the [first-table guide](../other-features/data-preparated.md).

## 4. Load the frontend assets

The standalone Blade recipes load **DataTables 2** and **jQuery** from pinned CDN URLs; the advanced dashboard also loads the **Responsive** extension. You do not need a Node build for those recipes, but the browser must be able to reach the CDN. If your Laravel app already loads DataTables, use its existing assets and initialize each table only once.

Next: [build the prepared-data catalog](../other-features/data-preparated.md). If a check fails, see [installation troubleshooting](../reference/troubleshooting.md#installation-and-startup).
