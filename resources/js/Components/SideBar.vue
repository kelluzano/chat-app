<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { onMounted } from "vue";
import { usePermission } from "@/Composables/permission";

onMounted(() => {
    $('[data-widget = "treeview"]').Treeview('init');

    $(document).on('click', '[data-widget="treeview"] .nav-link', function (e) {
        e.stopImmediatePropagation();
    });
});

const { hasRole } = usePermission();

</script>
<template>
    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="" class="brand-link">
            <img src="/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
                style="opacity: .8">
            <span class="brand-text font-weight-light">AdminLTE 3</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" class="d-block">{{ $page.props.auth.user.name }}</a>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                    <li class="nav-item">
                        <Link href="/dashboard" class="nav-link" :class="{ 'active': $page.component === 'Dashboard' }">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                        </Link>
                    </li>

                    <li class="nav-item">
                        <Link :href="route('messages.index')" class="nav-link" :class="{ 'active': $page.component === 'Messages/Index' }">
                        <i class="nav-icon fas fa-message"></i>
                        <p>
                            Messages
                        </p>
                        </Link>
                    </li>

                    <li v-if="hasRole('admin')" class="nav-item">
                        <a href="" class="nav-link" :class="{ 'active': usePage().url.indexOf('system-settings') != -1 }">
                            <i class="nav-icon far fa-plus-square"></i>
                            <p>
                                System Settings
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <Link :href="route('users.index')" class="nav-link"
                                    :class="{ 'active': $page.component === 'SystemSettings/Users' }">
                                <i class="far fa-solid fa-users mr-2"></i>
                                <p>Users</p>
                                </Link>
                            </li>

                            <li class="nav-item">
                                <Link :href="route('roles.index')" class="nav-link"
                                    :class="{ 'active': $page.component === 'SystemSettings/Roles/Index' }">
                                <i class="far fa-solid fa-user-secret mr-2"></i>
                                <p>Roles</p>
                                </Link>
                            </li>

                            <li class="nav-item">
                                <Link :href="route('permissions.index')" class="nav-link"
                                    :class="{ 'active': $page.component === 'SystemSettings/Permissions/Index' }">
                                <i class="far fa-solid fa-user-shield mr-2"></i>
                                <p>Permissions</p>
                                </Link>
                            </li>

                        </ul>
                    </li>

                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>
</template>
