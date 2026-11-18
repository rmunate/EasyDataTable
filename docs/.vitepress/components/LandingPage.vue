<script setup lang="ts">
import { VPLink } from 'vitepress/theme-without-fonts'
import { ref } from 'vue'

const command = 'composer require "rmunate/easy-datatable:^3.0"'
const copyStatus = ref<'idle' | 'copied' | 'unavailable'>('idle')

const paths = [
    { number: '01', tone: 'cyan', label: 'QUICKSTART', title: 'Build your first table',
        description: 'Install the package, return three products as JSON and wire them to a Blade view. No database needed.',
        link: '/getting-started/installation', action: 'Start with installation' },
    { number: '02', tone: 'violet', label: 'QUERY BUILDER', title: 'Move the work to SQL',
        description: 'Connect a real catalog, then let Laravel search, sort and return one page at a time.',
        link: '/usage/server-side', action: 'Build the server-side catalog' },
    { number: '03', tone: 'orange', label: 'COMPLETE EXAMPLE', title: 'Build a dashboard',
        description: 'Join three tables, apply company scope and filters, and show review actions only to authorized users.',
        link: '/usage/employee-leave', action: 'Follow the dashboard guide' },
]

async function copyCommand() {
    try {
        await navigator.clipboard.writeText(command)
        copyStatus.value = 'copied'
    } catch {
        copyStatus.value = 'unavailable'
    }
}
</script>

<template>
    <main class="landing">
        <section class="hero" aria-labelledby="landing-title">
            <div class="hero-inner frame">
                <div class="hero-content">
                    <p class="hero-kicker">
                        <span class="version">v3.0.0</span>
                        Documentation for Laravel 8–13
                    </p>
                    <h1 id="landing-title">DataTables,<br><span>powered by Laravel.</span></h1>
                    <p class="hero-summary">
                        Build the JSON your table needs, from a PHP array to SQL-backed
                        search, sorting and pagination.
                    </p>
                    <div class="hero-actions">
                        <VPLink class="action action-primary" href="/other-features/data-preparated">
                            Build your first table <span aria-hidden="true">&rarr;</span>
                        </VPLink>
                        <VPLink class="action action-secondary" href="/examples/">Explore examples</VPLink>
                    </div>
                    <div class="install">
                        <span aria-hidden="true" class="prompt">$</span>
                        <code>{{ command }}</code>
                        <button type="button" aria-label="Copy Composer command" @click="copyCommand">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <rect x="8" y="8" width="12" height="12" rx="2" />
                                <path d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2" />
                            </svg>
                            {{ copyStatus === 'copied' ? 'Copied' : 'Copy' }}
                        </button>
                    </div>
                    <p class="copy-feedback" role="status">
                        <span v-if="copyStatus === 'copied'" class="sr-only">Composer command copied.</span>
                        <span v-else-if="copyStatus === 'unavailable'">Clipboard unavailable. Select and copy the command above.</span>
                    </p>
                </div>

                <div class="sample" aria-labelledby="sample-title">
                    <div class="sample-toolbar">
                        <span class="method">GET</span>
                        <code>/examples/leave/server-data</code>
                    </div>
                    <div class="sample-content">
                        <div class="sample-heading">
                            <span class="sample-label">RESPONSE PREVIEW</span>
                            <h2 id="sample-title">Employee leave</h2>
                        </div>
                        <dl class="sample-stats">
                            <div><dt>recordsTotal</dt><dd>7</dd></div>
                            <div><dt>recordsFiltered</dt><dd>7</dd></div>
                            <div><dt>data.length</dt><dd>3</dd></div>
                        </dl>
                        <table aria-label="Example leave requests on the current page">
                            <thead>
                                <tr><th scope="col">Employee</th><th scope="col">Leave type</th><th scope="col">Status</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>Mina Ortega</td><td>Training</td><td><span class="pending">Pending</span></td></tr>
                                <tr><td>Noah Silva</td><td>Vacation</td><td><span class="approved">Approved</span></td></tr>
                                <tr><td>Mei Tan</td><td>Uncategorized</td><td><span class="approved">Approved</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="sample-footer">
                        <span class="sample-ok">200 OK</span>
                        <VPLink href="/examples/#try-the-dashboard">Try the interactive demo <span aria-hidden="true">&rarr;</span></VPLink>
                    </div>
                </div>
            </div>
        </section>

        <section class="learning frame" aria-labelledby="learning-title">
            <div class="learning-heading">
                <div>
                    <p class="section-label">LEARNING PATH</p>
                    <h2 id="learning-title">Choose where to start.</h2>
                </div>
                <VPLink class="text-link" href="/home">Compare processing modes <span aria-hidden="true">&rarr;</span></VPLink>
            </div>
            <ul class="paths">
                <li v-for="path in paths" :key="path.number" :data-tone="path.tone">
                    <VPLink class="path-card" :href="path.link">
                        <div class="path-meta">
                            <span class="path-number">{{ path.number }}</span>
                            <span>{{ path.label }}</span>
                        </div>
                        <h3>{{ path.title }}</h3>
                        <p>{{ path.description }}</p>
                        <span class="path-action">{{ path.action }} <span aria-hidden="true">&rarr;</span></span>
                    </VPLink>
                </li>
            </ul>

            <div class="reference">
                <div>
                    <p class="section-label">DEVELOPER REFERENCE</p>
                    <h2>Look up a method or diagnose a failing request.</h2>
                </div>
                <div class="reference-links">
                    <VPLink href="/reference/api">PHP API &amp; JSON contract <span aria-hidden="true">&rarr;</span></VPLink>
                    <VPLink href="/reference/troubleshooting">Troubleshooting <span aria-hidden="true">&rarr;</span></VPLink>
                </div>
            </div>
        </section>
    </main>
</template>

<style scoped>
.frame {
    max-width: 1256px;
    margin-inline: auto;
    padding-inline: clamp(1.25rem, 4vw, 2.75rem);
}

.hero {
    --hero-text: #f6f9ff;
    --hero-muted: #b8c6df;
    --hero-cyan: #7deaf5;
    --edt-focus: var(--hero-cyan);
    position: relative;
    isolation: isolate;
    color: var(--hero-text);
    background:
        radial-gradient(ellipse at 87% 2%, #412978 0, transparent 56%),
        radial-gradient(ellipse at 13% 108%, #104355 0, transparent 62%),
        #101a31;
    border-bottom: 1px solid #3c4974;
}

.hero::before {
    content: '';
    position: absolute;
    inset: 0;
    z-index: -1;
    background-image:
        linear-gradient(rgb(191 212 255 / 8%) 1px, transparent 1px),
        linear-gradient(90deg, rgb(191 212 255 / 8%) 1px, transparent 1px);
    background-size: 42px 42px;
    mask-image: linear-gradient(115deg, transparent 20%, #000 90%);
    pointer-events: none;
}

.hero-inner {
    display: grid;
    grid-template-columns: minmax(0, 1.08fr) minmax(0, 1fr);
    align-items: center;
    gap: clamp(1.75rem, 4vw, 4.5rem);
    padding-block: clamp(3.75rem, 7vw, 6.25rem);
}

.hero-kicker {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: .75rem;
    margin-bottom: 1.1rem;
    color: var(--hero-muted);
    font-weight: 600;
    font-size: .95rem;
}

.version {
    padding: .12rem .65rem;
    border: 1px solid #457b9a;
    border-radius: 999px;
    color: var(--hero-cyan);
    background: #193451;
    font: 600 .75rem/1.5 var(--vp-font-family-mono);
}

.hero h1 {
    font-size: clamp(2.8rem, 5.2vw, 5rem);
    font-weight: 700;
    letter-spacing: -.045em;
    line-height: .99;
}

.hero h1 span {
    color: var(--hero-cyan);
}

@supports (background-clip: text) {
    .hero h1 span {
        color: transparent;
        background: linear-gradient(100deg, #7deaf5 10%, #91dfff 55%, #cbb0ff);
        background-clip: text;
    }
}

@media (forced-colors: active) {
    .hero h1 span { color: CanvasText; }
}

.hero-summary {
    max-width: 30rem;
    margin-top: 1.25rem;
    color: var(--hero-muted);
    font-size: clamp(1.1rem, 2.1vw, 1.35rem);
    line-height: 1.5;
}

.hero-actions, .reference-links {
    display: flex;
    flex-wrap: wrap;
    gap: .65rem;
}

.hero-actions { margin-top: 1.6rem; }

.action {
    display: inline-flex;
    gap: .7rem;
    align-items: center;
    justify-content: center;
    padding: .65rem 1rem;
    border: 1px solid transparent;
    border-radius: 10px;
    font-weight: 700;
}

.action-primary {
    color: #0b2433;
    background: linear-gradient(105deg, #62e5f5, #a4baff);
}

.action-primary:hover { background: #a6f1fa; }

.action-secondary {
    border-color: #687a9f;
    background: #182743;
    color: var(--hero-text);
}

.action-secondary:hover {
    border-color: var(--hero-cyan);
    background: #263b5f;
}

.install {
    display: flex;
    align-items: center;
    gap: .65rem;
    width: fit-content;
    max-width: 100%;
    margin-top: 1rem;
    padding: .25rem .25rem .25rem .8rem;
    border: 1px solid #3d5474;
    border-radius: 9px;
    background: #0d182d;
}

.install code, .sample-toolbar code, .sample-stats dt {
    font: .78rem/1.55 var(--vp-font-family-mono);
    overflow-wrap: anywhere;
}

.prompt { color: var(--hero-cyan); }

.install button {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    flex-shrink: 0;
    min-height: 2.2rem;
    padding-inline: .55rem;
    border-radius: 6px;
    background: #243856;
    font-size: .85rem;
    font-weight: 700;
}

.install button:hover { background: #324c72; }
.install svg { width: 1rem; height: 1rem; }

.copy-feedback {
    color: #ffce95;
    font-size: .86rem;
    line-height: 1.35;
}

.sample {
    min-width: 0;
    overflow: hidden;
    border: 1px solid #52648e;
    border-radius: 18px;
    background: #111f37;
    box-shadow: 0 24px 64px rgb(5 10 24 / 50%), 0 0 50px rgb(99 78 206 / 18%);
}

.sample-toolbar, .sample-footer {
    display: flex;
    align-items: center;
    gap: .65rem;
    flex-wrap: wrap;
    padding: .75rem clamp(.8rem, 3vw, 1.25rem);
    background: #192946;
}

.sample-toolbar { border-bottom: 1px solid #3a4b6d; }

.method {
    padding: .1rem .5rem;
    border-radius: 5px;
    background: #1b424c;
    color: #8af3d9;
    font: 700 .73rem/1.6 var(--vp-font-family-mono);
}

.sample-toolbar code { color: #cfdef5; }

.sample-content { padding: .9rem clamp(.8rem, 3vw, 1.25rem) 1rem; }

.sample-label, .section-label {
    font-size: .78rem;
    font-weight: 700;
    letter-spacing: .12em;
}

.sample-label { color: var(--hero-cyan); }
.sample-heading h2 { font-size: 1.55rem; font-weight: 700; line-height: 1.15; }

.sample-stats {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: .45rem;
    margin-block: .9rem;
}

.sample-stats > div {
    padding: .45rem .5rem;
    border: 1px solid #3d5577;
    border-radius: 8px;
    background: #1b2f4d;
}

.sample-stats dt { color: var(--hero-muted); font-size: clamp(.62rem, 1vw, .75rem); }
.sample-stats dd { margin: 0; color: var(--hero-cyan); font-weight: 700; font-size: 1.3rem; line-height: 1.3; }
.sample-stats > div:nth-child(2) dd { color: #d5c1ff; }
.sample-stats > div:nth-child(3) dd { color: #ffcf96; }

.sample table { width: 100%; text-align: left; font-size: .9rem; line-height: 1.4; }
.sample :is(th, td) { padding: .45rem .2rem; }
.sample th { color: var(--hero-muted); font-weight: 600; }
.sample td { border-top: 1px solid #354969; }
.sample tr > :last-child { text-align: right; }

.sample td span {
    display: inline-block;
    border-radius: 99px;
    padding: .14rem .5rem;
    font-size: .8rem;
    font-weight: 700;
}

.pending { color: #ffce95; background: #45332e; }
.approved { color: #9af4d1; background: #193c3d; }

.sample-footer {
    justify-content: space-between;
    border-top: 1px solid #3a4b6d;
    font-weight: 600;
    font-size: .86rem;
}

.sample-ok { color: #9af4d1; }
.sample-ok::before {
    content: '';
    display: inline-block;
    width: .45rem;
    height: .45rem;
    margin-inline-end: .38rem;
    border-radius: 50%;
    background: currentColor;
    vertical-align: .09em;
}

.sample-footer a { color: var(--hero-cyan); }
.sample-footer a:hover, .text-link:hover, .reference-links a:hover { text-decoration: underline; }

.learning { padding-block: clamp(2.5rem, 5vw, 4.5rem); }

.learning-heading {
    display: flex;
    flex-wrap: wrap;
    align-items: end;
    justify-content: space-between;
    gap: .75rem 1.5rem;
    margin-bottom: 1.2rem;
}

.section-label, .text-link {
    color: var(--vp-c-brand-1);
}

.learning-heading h2, .reference h2 {
    font-size: clamp(1.75rem, 3vw, 2.6rem);
    line-height: 1.15;
    font-weight: 700;
    letter-spacing: -.025em;
}

.text-link, .reference a { font-weight: 700; }

.paths {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: .9rem;
    list-style: none;
}

.paths li {
    --path-accent: var(--vp-c-brand-1);
    --path-soft: var(--vp-c-brand-soft);
    --path-edge: #26b5d3;
}

.paths [data-tone='violet'] {
    --path-accent: var(--edt-violet);
    --path-soft: var(--edt-violet-soft);
    --path-edge: #9364fa;
}

.paths [data-tone='orange'] {
    --path-accent: var(--edt-orange);
    --path-soft: var(--edt-orange-soft);
    --path-edge: #ee9b40;
}

.path-card {
    display: flex;
    flex-direction: column;
    align-items: start;
    gap: .55rem;
    height: 100%;
    padding: 1.15rem;
    border: 1px solid var(--vp-c-divider);
    border-top: 3px solid var(--path-edge);
    border-radius: 12px;
    background: var(--edt-surface);
    box-shadow: var(--edt-shadow);
}

.path-card:hover {
    border-color: var(--path-accent);
}

.path-meta {
    display: flex;
    align-items: center;
    gap: .6rem;
    color: var(--path-accent);
    font-size: .78rem;
    font-weight: 700;
    letter-spacing: .08em;
}

.path-number {
    display: grid;
    place-items: center;
    width: 2rem;
    height: 2rem;
    border-radius: 7px;
    background: var(--path-soft);
    font: 700 .8rem/1 var(--vp-font-family-mono);
}

.path-card h3 { font-weight: 700; font-size: 1.4rem; line-height: 1.2; }
.path-card p { color: var(--vp-c-text-2); line-height: 1.45; }
.path-action { margin-top: auto; padding-top: .45rem; color: var(--path-accent); font-weight: 700; }

.reference {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: .9rem 2rem;
    margin-top: 1.5rem;
    padding: 1.25rem;
    border: 1px solid var(--vp-c-divider);
    border-radius: 12px;
    background: var(--vp-c-bg-soft);
}

.reference h2 { font-size: clamp(1.25rem, 3vw, 1.65rem); }
.reference a { color: var(--vp-c-brand-1); }

@media (max-width: 850px) {
    .hero-inner { grid-template-columns: minmax(0, 1fr); gap: 2.5rem; }
    .hero h1 { font-size: clamp(2.75rem, 9.5vw, 4.55rem); }
    .sample { max-width: 37rem; }
    .paths { grid-template-columns: minmax(0, 1fr); }
}

@media (max-width: 480px) {
    .path-card { padding: 1rem; }
}
</style>
