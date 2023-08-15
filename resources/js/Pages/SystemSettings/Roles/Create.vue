<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { useForm } from '@inertiajs/vue3';
import VueMultiselect from 'vue-multiselect';

defineProps(['permissions'])
const form = useForm({
    name: "",
    permissions: []
})

</script>

<script>
export default {
    layout: MainLayout
}
</script>

<template>
    <div class="container-fluid">
        <div class="d-flex justify-content-center">
            <div class="w-50">
                <div class="card">
                    <div class="card-header">
                        <p class="card-title">Add Role</p>
                    </div>

                    <div class="card-body">
                        <form @submit.prevent="form.post(route('roles.save'))">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" v-model="form.name" class="form-control">
                                <span v-if="form.errors.name" class="text-red">{{ form.errors.name }}</span>
                            </div>

                            <div class="mb-3">
                                <label>Permissions</label>
                                <VueMultiselect v-model="form.permissions" :options="permissions" :multiple="true"
                                    :close-on-select="true" placeholder="Assign Permissions" label="name" track-by="id">
                                </VueMultiselect>
                            </div>

                            <button type="submit" class="float-right btn-sm btn-primary rounded"
                                :disabled="form.processing">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
