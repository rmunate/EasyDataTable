---
title: Examples
description: Choose a copyable Laravel recipe or try server-side counts and filters in an interactive sample.
---

<script setup>
import LeavePreview from '../.vitepress/components/LeavePreview.vue'
</script>

# Examples & interactive preview

Build a three-row table without a database, move a real catalog to SQL, then add a dashboard with joins and permissions. Each recipe shows what to put in Laravel, how to initialize the frontend and how to verify it works.

[Jump to the interactive preview](#try-the-dashboard)

## Choose a recipe

| Level | Recipe | What you will build |
| --- | --- | --- |
| <Badge type="tip" text="01" /> | [Prepared data](../other-features/data-preparated.md) | An Ajax table from a PHP array, no database required. |
| <Badge type="tip" text="02" /> | [Client-side catalog](../usage/client-side.md) | A database-backed catalog that maps available stock and sorts in the browser. |
| <Badge type="info" text="03" /> | [Server-side catalog](../usage/server-side.md) | SQL search, order and pagination, with a URL you can use to inspect one draw directly. |
| <Badge type="warning" text="04" /> | [Employee leave dashboard](../usage/employee-leave.md) | A three-table join, company scope, filters, a review action and a grouped report. |
| <Badge type="warning" text="05" /> | [Metronic integration](../usage/metronic.md) | The same responsive dashboard embedded in a Metronic card with an external toolbar. |

If you do not have a Laravel app yet, [start with installation](../getting-started/installation.md). You can use the preview below without one.

## Try the dashboard

1. Select **Pending**. The company and status filters leave **3** base rows (`recordsTotal`).
2. Search for **Vacation**. The base count stays **3**; the search matches **2** (`recordsFiltered`).
3. Set **Starts on or after** to **October 10, 2026**. The base count becomes **2**. The search matches only request **104**, so `data.length` is **1**.
4. Select **Clear filters** and then **Next** to page through the full sample. There are **7** matches but only **3** rows per page.

<LeavePreview />

The preview uses fixed rows and simulates a draw in your browser. It does not send requests to Laravel. To run the real queries and check permissions, follow the [dashboard guide](../usage/employee-leave.md) or browse the [example source](https://github.com/rmunate/EasyDataTable/tree/master/examples).
