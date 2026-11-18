import { defineConfig } from 'vitepress'

export default defineConfig({
    title: "EasyDataTable",
    description: 'Build DataTables endpoints in Laravel, from prepared data to SQL-backed dashboards.',
    lang: 'en-US',
    lastUpdated: true,
    base: '/EasyDataTable/',
    themeConfig: {
        footer: {
            message: 'Released under the MIT License.',
            copyright: 'Copyright © 2021-2026 Raul Mauricio Uñate'
        },
        editLink: {
            pattern: 'https://github.com/rmunate/EasyDataTable/edit/master/docs/:path'
        },
        logo: '/img/logo.png',
        nav: [
            {
                text: 'Guide',
                link: '/home',
            },
            {
                text: 'Examples',
                link: '/examples/',
                activeMatch: '^/(examples/|usage/|other-features/data-preparated)',
            },
            {
                text: 'v3.0.0',
                link: '/getting-started/changelog',
            }
        ],
        sidebar: [
            {
                text: 'Getting Started',
                collapsed: false,
                items: [
                    {text: 'Introduction', link: '/home'},
                    {text: 'Installation', link: '/getting-started/installation'},
                    {text: 'Versions', link: '/getting-started/versions'},
                    {text: 'Release Notes', link: '/getting-started/changelog'},
                ]
            }, {
                text: 'Examples',
                collapsed: false,
                items: [
                    {text: 'Overview & interactive preview', link: '/examples/'},
                    {text: '01 · Prepared data', link: '/other-features/data-preparated'},
                    {text: '02 · Client-side catalog', link: '/usage/client-side'},
                    {text: '03 · Server-side catalog', link: '/usage/server-side'},
                    {text: '04 · Employee leave dashboard', link: '/usage/employee-leave'},
                    {text: '05 · Metronic integration', link: '/usage/metronic'},
                ]
            },{
                text: 'Contribute',
                collapsed: true,
                items: [
                    {text: 'Bug Report', link: '/contribute/report-bugs'},
                    {text: 'Contribution', link: '/contribute/contribution'}
                ]
            }
        ],
        socialLinks: [
            {icon: 'github', link: 'https://github.com/rmunate/EasyDataTable'}
        ],
        search: {
            provider: 'local'
        }
    },
    head: [
        ['link', {
                rel: 'stylesheet',
                href: '/EasyDataTable/css/style.css'
            }
        ],
        ['link', {
                rel: 'icon',
                href: '/EasyDataTable/img/logo.png',
                type: 'image/png'
            }
        ],
        ['meta', {
                property: 'og:image',
                content: '/EasyDataTable/img/logo-github.png'
            }
        ],
        ['meta', {
                property: 'og:image:secure_url',
                content: '/EasyDataTable/img/logo-github.png'
            }
        ],
        ['meta', {
                property: 'og:image:width',
                content: '600'
            }
        ],
        ['meta', {
                property: 'og:image:height',
                content: '400'
            }
        ],
        ['meta', {
                property: 'og:title',
                content: 'EasyDataTable'
            }
        ],
        ['meta', {
                property: 'og:description',
                content: 'Build DataTables endpoints in Laravel, from prepared data to SQL-backed dashboards.'
            }
        ],
        ['meta', {
                property: 'og:url',
                content: 'https://rmunate.github.io/EasyDataTable/'
            }
        ],
        ['meta', {
                property: 'og:type',
                content: 'website'
            }
        ]
    ],

})