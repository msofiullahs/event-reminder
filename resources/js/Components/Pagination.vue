<script setup>
import { Link } from '@inertiajs/vue3'
defineProps({
    links: Array
});
</script>

<template>
    <nav aria-label="pagination">
        <ul class="pagination justify-content-center">
            <template v-for="(pageItem, i) in links" :key="i">
                <template v-if="pageItem.url === null">
                    <li class="page-item disabled">
                        <span class="page-link" v-html="pageItem.label" />
                    </li>
                </template>
                <template v-else-if="pageItem.active">
                    <li class="page-item active" aria-current="page">
                        <span class="page-link" v-html="pageItem.label" />
                    </li>
                </template>
                <template v-else>
                    <li class="page-item">
                        <a class="page-link" :href="pageItem.url" v-html="pageItem.label" />
                    </li>
                </template>
            </template>
        </ul>
    </nav>
</template>
