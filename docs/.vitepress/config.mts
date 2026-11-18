import { defineConfig } from 'vitepress'

const base = '/EasyDataTable/'
const siteUrl = `https://rmunate.github.io${base}`
const description = 'Build DataTables endpoints in Laravel, from your first PHP array to a dashboard with SQL pagination and permissions.'

export default defineConfig({
    title: 'EasyDataTable',
    description,
    lang: 'en-US',
    lastUpdated: true,
    base,
    head: [
        ['link', { rel: 'icon', href: `${base}img/logo.png`, type: 'image/png' }],
        ['meta', { property: 'og:site_name', content: 'EasyDataTable' }],
        ['meta', { property: 'og:title', content: 'EasyDataTable' }],
        ['meta', { property: 'og:description', content: description }],
        ['meta', { property: 'og:image', content: `${siteUrl}img/logo-github.png` }],
        ['meta', { property: 'og:image:alt', content: 'Laravel and EasyDataTable' }],
        ['meta', { property: 'og:type', content: 'website' }],
    ],
    themeConfig: {
        logo: '/img/logo.png',
        outline: { level: [2, 3], label: 'On this page' },
        search: { provider: 'local' },
        footer: {
            message: `EasyDataTable is released under the MIT License. <a href="${base}licenses/titillium-web.txt">Titillium Web: SIL OFL 1.1</a>.`,
            copyright: 'Copyright © 2021-2026 Raul Mauricio Uñate',
        },
        editLink: {
            pattern: 'https://github.com/rmunate/EasyDataTable/edit/master/docs/:path',
        },
        nav: [
            { text: 'Guide', link: '/home', activeMatch: '^/(home|getting-started/installation)' },
            { text: 'Examples', link: '/examples/', activeMatch: '^/(examples/|usage/|other-features/)' },
            { text: 'Reference', link: '/reference/api', activeMatch: '^/reference/' },
            { text: 'v3.0.0', link: '/getting-started/changelog', activeMatch: '^/getting-started/(changelog|versions)' },
        ],
        sidebar: [
            {
                text: 'Start here',
                collapsed: false,
                items: [
                    { text: 'Introduction', link: '/home' },
                    { text: 'Installation', link: '/getting-started/installation' },
                ],
            },
            {
                text: 'Build a table',
                collapsed: false,
                items: [
                    { text: 'Examples & interactive preview', link: '/examples/' },
                    { text: '01 · Prepared data', link: '/other-features/data-preparated' },
                    { text: '02 · Client-side catalog', link: '/usage/client-side' },
                    { text: '03 · Server-side catalog', link: '/usage/server-side' },
                    { text: '04 · Employee leave dashboard', link: '/usage/employee-leave' },
                    { text: '05 · Metronic integration', link: '/usage/metronic' },
                ],
            },
            {
                text: 'Reference',
                collapsed: false,
                items: [
                    { text: 'PHP API & JSON contract', link: '/reference/api' },
                    { text: 'Troubleshooting', link: '/reference/troubleshooting' },
                    { text: 'Version compatibility', link: '/getting-started/versions' },
                    { text: 'Release notes', link: '/getting-started/changelog' },
                ],
            },
            {
                text: 'Project',
                collapsed: true,
                items: [
                    { text: 'Report a bug', link: '/contribute/report-bugs' },
                    { text: 'Contributing', link: '/contribute/contribution' },
                ],
            },
        ],
        socialLinks: [
            { icon: 'github', link: 'https://github.com/rmunate/EasyDataTable' },
        ],
    },
})