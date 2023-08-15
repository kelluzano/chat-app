<script setup>
import { Link, Head } from "@inertiajs/vue3";
import MainLayout from '@/Layouts/MainLayout.vue';
import Table from "@/Components/Table.vue";
import TableDataCell from "@/Components/TableDataCell.vue";
import TableHeader from "@/Components/TableHeader.vue";
import TableRow from "@/Components/TableRow.vue";
import showFlashMessage from "@/Composables/showFlashMessage";

const props = defineProps(['permissions', 'flash'])

const isShowFlashMessage = showFlashMessage(props.flash.message)

</script>

<script>
export default {
    layout: MainLayout
}
</script>

<template>
    <Head title="Permissions" />
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Default box -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">Permission</h3>
                            <Link :href="route('permissions.create')" class="btn btn-primary btn-sm">Add Permission</Link>
                        </div>
                    </div>
                    <div class="card-body">

                        <div v-if="isShowFlashMessage" class="alert alert-success">{{ flash.message }}</div>

                        <Table>
                            <template #header>
                                <TableRow>
                                    <TableHeader>ID</TableHeader>
                                    <TableHeader>Name</TableHeader>
                                    <TableHeader>Action</TableHeader>
                                </TableRow>
                            </template>

                            <template #default>
                                <TableRow v-for="permission in permissions" :key="permission.id">
                                    <TableDataCell>{{ permission.id }}</TableDataCell>
                                    <TableDataCell>{{ permission.name }}</TableDataCell>
                                    <TableDataCell>
                                        <div class="d-flex">
                                            <Link :href="route('permissions.edit', permission.id)"
                                                class="btn btn-secondary btn-sm mr-2">Edit</Link>
                                            <Link :href="route('permissions.destroy', permission.id)" class="btn btn-danger btn-sm"
                                                preserve-state>Delete</Link>
                                        </div>
                                    </TableDataCell>
                                </TableRow>
                            </template>
                        </Table>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
</template>
