<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { useForm } from '@inertiajs/vue3';

const prop = defineProps(['permission'])
const form = useForm({
    name: prop.permission.name
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
                        <p class="card-title">Edit Permission</p>
                    </div>

                    <div class="card-body">
                        <form @submit.prevent="form.post(route('permissions.update', permission.id))">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" v-model="form.name" class="form-control">
                                <span v-if="form.errors.name" class="text-red">{{ form.errors.name }}</span>
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
