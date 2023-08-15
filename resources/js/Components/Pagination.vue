<script setup>
import { Link } from '@inertiajs/vue3';
defineProps(['links']);
</script>

<template>
    <ul class="pagination pagination-sm float-right">
        <li class="page-item" v-for="link in links">
            <span v-if="link.url == null" class="page-link disabled" v-html="link.label"></span>
            <Link v-else class="page-link" :href="link.url" v-html="link.label"></Link>
        </li>
    </ul>
</template>
