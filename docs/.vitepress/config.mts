import { defineConfig } from 'vitepress'

export default defineConfig({
    title: "EasyDataTable",
    description: "A Quick and Efficient Way to Create the Backend for Any DataTable in the PHP Laravel Framework 💻✨",
    lang: 'en-US',
    lastUpdated: true,
    base: '/EasyDataTable',
    themeConfig: {
        footer: {
            message: 'Released under the MIT License.',
            copyright: 'Copyright © 2021-2024 Raul Mauricio Uñate'
        },
        editLink: {
            pattern: 'https://github.com/rmunate/EasyDataTable/tree/main/docs/:path'
        },
        logo: '/img/logo.png',
        nav: [
            {
                text: 'Docs ^2.0',
                link: '/',
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
                text: 'Usage',
                collapsed: true,
                items: [
                    {text: 'Client Side', link: '/usage/client-side'},
                    {text: 'Server Side', link: '/usage/server-side'}
                ]
            }, {
                text: 'Other Features',
                collapsed: true,
                items: [
                    {text: 'Data Preparated', link: '/other-features/data-preparated'}
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
            {icon: 'github', link: 'https://github.com/rmunate/PHP2JS'}
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
                content: 'A Quick and Efficient Way to Create the Backend for Any DataTable in the PHP Laravel Framework 💻✨'
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