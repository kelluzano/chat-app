<script setup>
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import MainLayout from '@/Layouts/MainLayout.vue';
import Table from "@/Components/Table.vue";
import TableDataCell from "@/Components/TableDataCell.vue";
import TableHeader from "@/Components/TableHeader.vue";
import TableRow from "@/Components/TableRow.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, ref, reactive } from "vue";
import Modal from "@/Components/Modal.vue";
import showFlashMessage from "@/Composables/showFlashMessage";

const props = defineProps(['users', 'flash']);

const isShowFlashMessage = showFlashMessage(props.flash.message)
const modalData = useForm({
    'id': "",
    'name': ""
});

let modal;

function save() {
    router.get(route('users.destroy', modalData.id));
    $("#btnClose").trigger('click');
}

function showModal(e) {

    var id = $(e.currentTarget).attr('data-id');
    var name = $(e.currentTarget).attr('data-name');

    modalData.id = id;
    modalData.name = name;

    modal.modal({ show: true })
}

onMounted(() => {
    modal = $("#modal").modal({ show: false });
})

</script>

<script>
export default {
    layout: MainLayout
}
</script>

<template>
    <Head title="Users" />
    <Modal :modalTitle="'Delete User'">
        <template #default>
            <div class="form-group">
                <p>Delete this user <span class="font-weight-bold">{{ modalData.name }}</span>?</p>

            </div>
        </template>

        <template #buttons>
            <button id="btnClose" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button class="btn btn-primary" @click="save()">Save changes</button>
        </template>
    </Modal>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Default box -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">Users</h3>
                            <Link :href="route('users.create')" class="btn btn-primary btn-sm">Add User</Link>
                        </div>
                    </div>
                    <div class="card-body">

                        <div v-if="isShowFlashMessage" class="alert" :class="flash.class">{{ flash.message }}</div>

                        <Table>
                            <template #header>
                                <TableRow>
                                    <TableHeader>ID</TableHeader>
                                    <TableHeader>Name</TableHeader>
                                    <TableHeader>Action</TableHeader>
                                </TableRow>
                            </template>

                            <template #default>
                                <TableRow v-for="user in users.data" :key="user.id">
                                    <TableDataCell>{{ user.id }}</TableDataCell>
                                    <TableDataCell>{{ user.name }}</TableDataCell>
                                    <TableDataCell>
                                        <div class="d-flex">
                                            <Link :href="route('users.edit', user.id)"
                                                class="btn btn-secondary btn-sm mr-2">Edit</Link>
                                            <button :data-id="user.id" :data-name="user.name" @click="showModal($event)"
                                                class="btn btn-danger btn-sm">Delete</button>
                                        </div>
                                    </TableDataCell>
                                </TableRow>
                            </template>

                        </Table>
                    </div>
                    <div class="card-footer">
                        <Pagination :links="users.links" />
                    </div>
                </div>


                <!-- /.card -->
            </div>
        </div>
    </div>
</template>
