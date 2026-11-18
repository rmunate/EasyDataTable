---
title: Examples
description: Five EasyDataTable recipes from a database-free catalog to a company-scoped dashboard.
---

<script setup>
import LeavePreview from '../.vitepress/components/LeavePreview.vue'
</script>

# Build your first table. Then scale it.

Start with three rows, move a real catalog to the database, and finish with an employee leave dashboard. Each example shows what Laravel returns and what DataTables does with it.

[Jump to the interactive preview](#try-the-dashboard)

## Choose a recipe

| Level | Recipe | What you will build |
| --- | --- | --- |
| <Badge type="tip" text="01" /> | [Prepared data](../other-features/data-preparated) | An Ajax table from a PHP array, no database required. |
| <Badge type="tip" text="02" /> | [Client-side catalog](../usage/client-side) | A database-backed catalog that maps available stock and sorts in the browser. |
| <Badge type="info" text="03" /> | [Server-side catalog](../usage/server-side) | SQL-backed search, ordering and pagination, including the JSON for one draw. |
| <Badge type="warning" text="04" /> | [Employee leave dashboard](../usage/employee-leave) | A three-table join, tenant scope, filters, a review action and a grouped report. |
| <Badge type="warning" text="05" /> | [Metronic integration](../usage/metronic) | A responsive dashboard with a themed toolbar and an external search box. |

## Try the dashboard

Select **Pending**, search for **Vacation**, and set **Starts on or after** to **October 10, 2026**. Watch the difference between the base count, the search count, and the number of rows on the page. Clear the filters and use **Next** to page through the full snapshot.

<LeavePreview />

This preview runs on fixed sample data in your browser. To run the real Laravel queries and test user permissions, follow the [advanced guide](../usage/employee-leave) or open the [example source and setup instructions](https://github.com/rmunate/EasyDataTable/tree/master/examples).
