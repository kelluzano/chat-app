<script setup>
import { Head, Link, usePage } from "@inertiajs/vue3";
import MainLayout from '@/Layouts/MainLayout.vue';
import Table from "@/Components/Table.vue";
import TableDataCell from "@/Components/TableDataCell.vue";
import TableHeader from "@/Components/TableHeader.vue";
import TableRow from "@/Components/TableRow.vue";
import showFlashMessage from "@/Composables/showFlashMessage";

const props = defineProps(['roles', 'flash'])
const isShowFlashMessage = showFlashMessage(props.flash.message)

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
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">Roles</h3>
                            <Link :href="route('roles.create')" class="btn btn-primary btn-sm">Add Role</Link>
                        </div>
                    </div>
                    <div class="card-body">

                        <div v-if="isShowFlashMessage" class="alert alert-success">{{ flash.message }}</div>

                        <Table>
                            <template #header>
                                <TableRow>
                                    <TableHeader>ID</TableHeader>
                                    <TableHeader>Name</TableHeader>
                                    <TableHeader>Roles</TableHeader>
                                    <TableHeader>Action</TableHeader>
                                </TableRow>
                            </template>

                            <template #default>
                                <TableRow v-for="role in roles" :key="role.id">
                                    <TableDataCell>{{ role.id }}</TableDataCell>
                                    <TableDataCell>{{ role.name }}</TableDataCell>
                                    <TableDataCell>
                                        <div class="d-flex">
                                        <span v-for="permission in role.permissions" class="badge badge-info mr-2">{{ permission.name }}</span>
                                        </div>
                                    </TableDataCell>
                                    <TableDataCell>
                                        <div class="d-flex">
                                            <Link :href="route('roles.edit', role.id)"
                                                class="btn btn-secondary btn-sm mr-2">Edit</Link>
                                            <Link :href="route('roles.destroy', role.id)" class="btn btn-danger btn-sm"
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
