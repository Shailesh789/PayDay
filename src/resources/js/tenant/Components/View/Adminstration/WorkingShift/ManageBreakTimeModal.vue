<template>
    <modal id="manage-break-time-modal"
           v-model="showModal"
           :title="$t('manage_break_time')"
           @submit="submitData"
           :loading="loading"
           :preloader="preloader"
           :scrollable="false"
    >
        <form
            :data-url="`${apiUrl.WORKING_SHIFTS}/${workShift.id}/break-time`"
            method="POST"
            ref="form"
        >

            <app-form-group-selectable
                :label="$fieldLabel('break_time', '')"
                type="multi-select"
                v-model="formData.break_times"
                list-value-field="name"
                :error-message="$errorMessage(errors, 'break_time')"
                :fetch-url="apiUrl.SELECTABLE_BREAK_TIMES"
            />

        </form>
    </modal>
</template>

<script>
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";

export default {
    name: "ManageBreakTimeModal",
    mixins: [FormHelperMixins, ModalMixin],
    props: {
        workShift: {
            required: true
        }
    },
    data() {
        return {
            formData: {
                break_times: []
            },
        }
    },
    mounted() {
        this.formData.break_times = this.workShift.break_times.map(brk => brk.id);
    },
    methods: {
        afterSuccess({data}) {
            this.toastAndReload(data.message, 'working-shift-table');
            $('#manage-break-time-modal').modal('hide')
        },
    },
}
</script>