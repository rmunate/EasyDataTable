<script setup lang="ts">
import { computed, ref } from 'vue'

const rows = [
    { id: 107, code: 'E-102', employee: 'Mina Ortega', type: 'Training', reason: 'Workshop', date: '2026-10-22', status: 'pending' },
    { id: 106, code: 'E-101', employee: 'Noah Silva', type: 'Vacation', reason: 'Long weekend', date: '2026-10-18', status: 'approved' },
    { id: 105, code: 'E-100', employee: 'Mei Tan', type: null, reason: 'Schedule change', date: '2026-10-14', status: 'approved' },
    { id: 104, code: 'E-102', employee: 'Mina Ortega', type: 'Vacation', reason: 'Autumn break', date: '2026-10-12', status: 'pending' },
    { id: 103, code: 'E-102', employee: 'Mina Ortega', type: 'Personal', reason: 'Personal appointment', date: '2026-10-08', status: 'rejected' },
    { id: 102, code: 'E-101', employee: 'Noah Silva', type: 'Training', reason: 'Certification course', date: '2026-10-06', status: 'approved' },
    { id: 101, code: 'E-100', employee: 'Mei Tan', type: 'Vacation', reason: 'Family trip', date: '2026-10-03', status: 'pending' },
]
const pageSize = 3
const status = ref('')
const since = ref('')
const term = ref('')
const page = ref(0)

const baseRows = computed(() => rows.filter(row =>
    (!status.value || row.status === status.value) && (!since.value || row.date >= since.value)
))
const matches = computed(() => {
    const query = term.value.trim().toLowerCase()

    return baseRows.value.filter(row =>
        [row.employee, row.code, row.type ?? '', row.reason].some(value => value.toLowerCase().includes(query))
    )
})
const pageCount = computed(() => Math.max(1, Math.ceil(matches.value.length / pageSize)))
const pageRows = computed(() => matches.value.slice(page.value * pageSize, (page.value + 1) * pageSize))

function clearFilters() {
    term.value = ''
    status.value = ''
    since.value = ''
    page.value = 0
}
</script>

<template>
    <section class="leave-preview" aria-labelledby="leave-preview-title">
        <div class="preview-header">
            <div>
                <span class="eyebrow">INTERACTIVE SAMPLE</span>
                <strong id="leave-preview-title">Employee leave requests</strong>
            </div>
            <span class="company">Company 10</span>
        </div>
        <div class="preview-body">
            <div class="preview-filters">
                <label>
                    Global search
                    <input v-model="term" type="search" placeholder="Try Vacation" @input="page = 0">
                </label>
                <label>
                    Status
                    <select v-model="status" @change="page = 0">
                        <option value="">All statuses</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </label>
                <label>
                    Starts on or after
                    <input v-model="since" type="date" @input="page = 0">
                </label>
            </div>
            <dl class="preview-counts" aria-label="Response counts" aria-live="polite">
                <div><dt>recordsTotal</dt><dd>{{ baseRows.length }}</dd></div>
                <div><dt>recordsFiltered</dt><dd>{{ matches.length }}</dd></div>
                <div><dt>data.length</dt><dd>{{ pageRows.length }}</dd></div>
            </dl>
            <div class="preview-table" tabindex="0" role="region" aria-label="Scrollable leave request table">
                <table aria-label="Leave requests on the current page">
                    <thead>
                        <tr>
                            <th scope="col">Request</th><th scope="col">Employee</th><th scope="col">Type</th>
                            <th scope="col">Starts on</th><th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in pageRows" :key="row.id">
                            <td>#{{ row.id }}</td><td>{{ row.employee }}</td><td>{{ row.type ?? 'Uncategorized' }}</td>
                            <td>{{ row.date }}</td><td><span class="preview-status" :data-status="row.status">{{ row.status }}</span></td>
                        </tr>
                        <tr v-if="!pageRows.length">
                            <td colspan="5">No requests match these filters.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="preview-footer">
                <div class="preview-pagination" role="group" aria-label="Preview pagination">
                    <button type="button" :disabled="page === 0" @click="page--">Previous</button>
                    <span>Page {{ page + 1 }} of {{ pageCount }}</span>
                    <button type="button" :disabled="page + 1 >= pageCount" @click="page++">Next</button>
                </div>
                <button class="clear-filters" type="button" @click="clearFilters">Clear filters</button>
            </div>
        </div>
    </section>
</template>

<style scoped>
.leave-preview {
    overflow: hidden;
    border: 1px solid var(--vp-c-divider);
    border-radius: 16px;
    background: var(--edt-surface);
    box-shadow: var(--edt-shadow);
}

.preview-header, .preview-body { padding: clamp(1rem, 3vw, 1.65rem); }

.preview-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: .75rem;
    background: linear-gradient(110deg, var(--vp-c-brand-soft), var(--edt-violet-soft));
    border-bottom: 1px solid var(--vp-c-divider);
}
.eyebrow, .preview-header strong { display: block; }
.eyebrow { color: var(--vp-c-brand-1); font-size: .73rem; font-weight: 700; letter-spacing: .12em; }
.preview-header strong { font-size: clamp(1.2rem, 3vw, 1.65rem); line-height: 1.35; }
.company, .preview-status {
    display: inline-block;
    padding: .2rem .65rem;
    border-radius: 999px;
    background: var(--vp-c-brand-soft);
    color: var(--vp-c-brand-1);
    font-size: .8rem;
    font-weight: 600;
}

.company {
    background: var(--edt-surface);
    border: 1px solid var(--vp-c-divider);
}

.preview-filters, .preview-counts { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .65rem; }
.preview-filters label { font-size: .86rem; font-weight: 700; }

.preview-filters :is(input, select), .preview-footer button {
    min-width: 0;
    min-height: 2.5rem;
    border: 1px solid var(--vp-c-border);
    border-radius: 8px;
    background: var(--vp-c-bg-soft);
    padding: .45rem .65rem;
}

.preview-filters :is(input, select) { display: block; width: 100%; margin-top: .2rem; }
.preview-counts { margin-block: 1rem; }
.preview-counts > div { background: var(--vp-c-brand-soft); padding: .55rem .65rem; border-radius: 8px; }
.preview-counts dt { color: var(--vp-c-text-2); overflow-wrap: anywhere; font: .78rem/1.5 var(--vp-font-family-mono); }
.preview-counts dd { margin: 0; color: var(--vp-c-brand-1); font-size: 1.4rem; font-weight: 700; }
.preview-counts > div:nth-child(2) { background: var(--edt-violet-soft); }
.preview-counts > div:nth-child(2) dd { color: var(--edt-violet); }
.preview-counts > div:nth-child(3) { background: var(--edt-orange-soft); }
.preview-counts > div:nth-child(3) dd { color: var(--edt-orange); }

.preview-table { overflow-x: auto; border: 1px solid var(--vp-c-divider); border-radius: 8px; }
.preview-table table { display: table; width: 100%; margin: 0; white-space: nowrap; font-size: .9rem; }
.preview-table :is(th, td) { border: 0; padding: .55rem .7rem; }
.preview-table tbody tr { border-top: 1px solid var(--vp-c-divider); }
.preview-status { text-transform: capitalize; }
.preview-status[data-status="pending"] { background: var(--vp-c-warning-soft); color: var(--vp-c-warning-1); }
.preview-status[data-status="approved"] { background: var(--vp-c-success-soft); color: var(--vp-c-success-1); }
.preview-status[data-status="rejected"] { background: var(--vp-c-danger-soft); color: var(--vp-c-danger-1); }

.preview-footer, .preview-pagination { display: flex; align-items: center; flex-wrap: wrap; gap: .65rem; }
.preview-footer { justify-content: space-between; margin-top: 1rem; font-size: .9rem; font-weight: 600; }
.preview-footer button:disabled { opacity: .55; cursor: not-allowed; }
.preview-footer button:not(:disabled):hover { border-color: var(--vp-c-brand-1); }
.preview-footer .clear-filters { background: var(--vp-c-brand-soft); color: var(--vp-c-brand-1); }

@media (max-width: 560px) {
    .preview-filters, .preview-counts { grid-template-columns: minmax(0, 1fr); }
    .preview-counts > div { display: flex; align-items: center; justify-content: space-between; gap: .65rem; }
}
</style>
