---
title: Introduction
editLink: true
---

# Introduction

EasyDataTable builds the Laravel JSON endpoint for [DataTables](https://datatables.net/). You decide which rows a user may see; the package returns either a whole dataset or one filtered page.

| Start with | Use | Who searches and pages? |
| --- | --- | --- |
| An array, collection or iterable | [Prepared data](./other-features/data-preparated) | The browser |
| A small SQL result set | [Client-side mode](./usage/client-side) | The browser |
| A growing table or a complex query | [Server-side mode](./usage/server-side) | Laravel and the database |

::: tip Learn by building
[Explore the five examples and try the interactive preview](./examples/) before copying a recipe into your app.
:::

EasyDataTable does not install a frontend library or decide authorization. The examples use Blade and DataTables 2, but any client that speaks the same JSON protocol can use the endpoint.
