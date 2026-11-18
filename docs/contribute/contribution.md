---
title: Contributing
description: Run the EasyDataTable package tests and preview documentation changes before opening a pull request.
---

<script setup>
import { VPTeamMembers } from 'vitepress/theme-without-fonts'

const members = [
  {
    avatar: 'https://avatars.githubusercontent.com/u/91748598?v=4',
    name: 'Raúl Mauricio Uñate Castro',
    title: 'Creator',
    links: [
      { icon: 'github', link: 'https://github.com/rmunate'},
    ]
  },{
    avatar: 'https://avatars.githubusercontent.com/u/79948570?v=4',
    name: 'Carlos Giovanni Rodriguez',
    title: 'Developer',
    links: [
      { icon: 'github', link: 'https://github.com/Tutox0073'},
    ]
  },{
    avatar: 'https://avatars.githubusercontent.com/u/125677067?v=4',
    name: 'Laura Valentina Borda Vargas',
    title: 'Developer',
    links: [
      { icon: 'github', link: 'https://github.com/laura-borda'},
    ]
  },{
    avatar: 'https://avatars.githubusercontent.com/u/52323838?v=4',
    name: 'Wilmer A. Sanchez Saez',
    title: 'Developer',
    links: [
      { icon: 'github', link: 'https://github.com/wilmersaz'},
    ]
  },{
    avatar: 'https://avatars.githubusercontent.com/u/51100789?v=4',
    name: 'John Alejandro Diaz Pinilla',
    title: 'Developer',
    links: [
      { icon: 'github', link: 'https://github.com/alejandrodiazpinilla'},
    ]
  },{
    avatar: 'https://avatars.githubusercontent.com/u/52427080?v=4',
    name: 'Jorge Hernan Castañeda',
    title: 'Developer',
    links: [
      { icon: 'github', link: 'https://github.com/jhcastaneda'},
    ]
  }
]
</script>

# Contributing

Keep package examples, API behavior and the documentation in sync. A small pull request with a reproducible check is easier to review.

## Run the project locally

Fork and clone [EasyDataTable](https://github.com/rmunate/EasyDataTable), then work from the repository root. This repository is a Composer library, not a Laravel application.

::: code-group

```sh [PHP tests]
composer install
composer test
```

```sh [Docs preview]
npm ci
npm run docs:dev
```
:::

PHP tests use an in-memory SQLite database and need `pdo_sqlite`. The docs workflow uses **Node.js 22**. Open the documentation URL printed by VitePress; the site is served under `/EasyDataTable/`. Run `npm run docs:build` to check the static build and internal page links before submitting a docs change.

## Open a pull request

1. Create a branch for one change. For a bug fix, add a focused regression test; for a behavior change, update the relevant [guide](../home.md), [reference](../reference/api.md) or copyable example.
2. Open a pull request targeting `master`. Include the problem, what changed, and the checks you ran. For a visual change, show the result in light and dark mode or include screenshots.
3. Check that the **test** and **docs** jobs pass. Pull requests are validated but do not deploy the site.

## Developers and contributors

<VPTeamMembers size="small" :members="members" />

## License and support

EasyDataTable is released under the [MIT License](https://github.com/rmunate/EasyDataTable/blob/master/LICENSE.md). You can also [sponsor the maintainer](https://github.com/sponsors/rmunate).