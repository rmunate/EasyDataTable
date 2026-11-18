# EasyDataTable 3.0.0
**A Fast and Efficient Way to Create BackEnd for Any DataTable in the Laravel PHP Framework**

![LOGO](https://github.com/rmunate/EasyDataTable/assets/91748598/f05bac16-8619-4181-a994-10837c00b5d4)

Are you tired of the hassle of handling DataTables in your Laravel projects? Say goodbye to the complexity! EasyDataTable is here to streamline your backend processes.

With EasyDataTable, you can harness the power of Laravel's Query Builder to swiftly and effortlessly create tables with all the features you need, as demanded by [DataTables](https://datatables.net/).

## Documentation
[![📖📖📖 **FULL DOCUMENTATION** 📖📖📖](https://img.shields.io/badge/FULL%20DOCUMENTATION-Visit%20Here-blue?style=for-the-badge)](https://rmunate.github.io/EasyDataTable/)

### Learn by example

- **Start small:** [prepared data](docs/other-features/data-preparated.md) and a [client-side catalog](docs/usage/client-side.md).
- **Scale up:** [SQL-backed search and pagination](docs/usage/server-side.md) and a [company-scoped leave dashboard](docs/usage/employee-leave.md) with joins, status and date filters, and authorized actions.
- **Integrate:** [Metronic](docs/usage/metronic.md), or copy the [Laravel examples and SQLite fixture](examples/README.md).

Try the [interactive preview](https://rmunate.github.io/EasyDataTable/examples/#try-the-dashboard) to see how filters change the server-side counts.

## Installation

Supports **Laravel 13.x with PHP 8.3+** and retains Laravel 8.x–12.x compatibility. See the [version matrix](docs/getting-started/versions.md) for per-major requirements.

```shell
composer require "rmunate/easy-datatable:^3.0"
```

## Testing and release

Run `composer install` and `composer test` with `pdo_sqlite` enabled. To reproduce the Pages build, run `npm ci` and `npm run docs:build`.

The [GitHub Actions workflow](.github/workflows/deploy.yml) checks pull requests and `master`. Select **Settings → Pages → Build and deployment → Source: GitHub Actions**. A successful run on `master` deploys the documentation; pull requests never deploy. For the Composer release, tag the verified commit `v3.0.0` and push the tag. Packagist obtains the version from Git; verify that its repository webhook or package update is enabled.

## License
This project is under the [MIT License](https://choosealicense.com/licenses/mit/).

🌟 Support My Projects! 🚀

[![Become a Sponsor](https://img.shields.io/badge/-Become%20a%20Sponsor-blue?style=for-the-badge&logo=github)](https://github.com/sponsors/rmunate)

Make any contributions you see fit; the code is entirely yours. Together, we can do amazing things and improve the world of development. Your support is invaluable. ✨

If you have ideas, suggestions, or just want to collaborate, we are open to everything! Join our community and be part of our journey to success! 🌐👩‍💻👨‍💻
