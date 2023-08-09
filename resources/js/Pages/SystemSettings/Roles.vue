<script setup>
import { Head } from "@inertiajs/vue3";
import MainLayout from '../../Layouts/MainLayout.vue';
</script>

<script>
export default {
    layout: MainLayout
}
</script>

<template>
    <Head title="Roles" />
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Default box -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Roles</h3>
                    </div>
                    <div class="card-body">
                        Roles here
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
</template>