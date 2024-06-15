<template>
    <app-modal :modal-id="modalId" modal-size="default" modalAlignment="center" @close-modal="closeAddEditModal">
            <template slot="body">
                <div class="px-primary">
                    <slot name="init"></slot>
                    <slot name="process"></slot>
                    <slot name="retry"></slot>
                </div>
            </template>
    </app-modal>
</template>

<script>
    export default {
        props: ['modalId'],
        data() {
            return {
            }
        },
        methods: {
            closeAddEditModal() {
                $(`#${this.modalId}`).modal('hide');
                this.$emit('close-modal')
            }
        },
        mounted(){
            $(`#${this.modalId}`).modal('show')
        }
    }
</script>