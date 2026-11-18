---
title: Introduction
description: Choose a processing mode and learn how Laravel, EasyDataTable and DataTables work together.
---

# How EasyDataTable works

EasyDataTable is the Laravel backend for [DataTables](https://datatables.net/). Give it prepared rows or a Query Builder, and it produces the JSON your table expects.

## Choose a processing mode

| Starting point | Processing mode | What Laravel sends | Guide |
| --- | --- | --- | --- |
| An array, collection or generator | Client-side with `fromData()` | All supplied rows in `data` | [First table](./other-features/data-preparated.md) |
| A small SQL result that fits comfortably in the browser | Client-side with `clientSide()->query($query)` | All visible rows in `data` | [Client-side catalog](./usage/client-side.md) |
| A growing result set or expensive filters | Server-side with `serverSide()->query($query)->request($request)` | One page, a draw number and two counts | [Server-side catalog](./usage/server-side.md) |

With **client-side** processing, one Ajax response supplies the dataset. DataTables then searches, sorts and pages it locally. With **server-side** processing, DataTables sends a new request as the user searches, sorts or changes pages; Laravel and the database do that work.

::: tip Choose based on the payload
There is no fixed row threshold. Measure the response size and browser performance, not just the number of records. A small reference list can stay client-side; for a large or frequently changing result, start with server-side processing.
:::

## Follow a request end to end

1. **Laravel defines access.** The route or controller authenticates the user, builds the query for rows they may see, and validates any application filters.
2. **EasyDataTable builds the payload.** It returns all client-side rows or counts and fetches one server-side page. An optional `map()` transforms just the rows being returned.
3. **DataTables renders the payload.** The browser initializes the table with the endpoint URL, the same processing mode and the names of the JSON fields it will display.

The package does not register Laravel routes, install a frontend plugin, or implement authorization. The examples use Blade and DataTables 2. You can use the same endpoint from Vue, React or another frontend if it sends and reads the [same JSON contract](./reference/api.md); no framework adapter is required on the Laravel side.

## Start building

1. [Install EasyDataTable](./getting-started/installation.md) and verify Composer resolved it.
2. [Build your first table](./other-features/data-preparated.md) without a database.
3. [Explore the examples](./examples/) for SQL, joins, custom filters and a themed integration. The [interactive preview](./examples/#try-the-dashboard) runs without a Laravel app.
