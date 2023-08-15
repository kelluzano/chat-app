<script setup>
const props = defineProps(['modalTitle'])

defineEmits(['modalSave'])
</script>

<template>
    <div tabindex="-1" class="modal fade" id="modal" aria-modal="true"
        role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ props.modalTitle }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                   <slot />
                </div>
                <div class="modal-footer justify-content-between">
                    <slot name="buttons"></slot>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
</template>
