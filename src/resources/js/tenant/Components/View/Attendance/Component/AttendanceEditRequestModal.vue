<template>
    <modal id="attendance-edit-request-modal"
           size="large"
           v-model="showModal"
           :title="generateModalTitle('attendance')"
           @submit="submitData"
           :scrollable="false"
           :loading="loading"
           :preloader="preloader"
    >
        <form method="post" ref="form" :data-url="`${apiUrl.ATTENDANCES}/${formData.id}/request`" @submit.prevent="submitData" >
            <app-form-group
                :label="$t('punch_in_time')"
                type="date"
                date-mode="dateTime"
                v-model="formData.in_time"
                :max-date="maxDate"
                :placeholder="$placeholder('punch_in_time')"
                :required="true"
                :error-message="$errorMessage(errors, 'in_time')"
            />
            <app-form-group
                :label="$t('punch_out_time')"
                type="date"
                date-mode="dateTime"
                :min-date="formData.in_time"
                :max-date="maxDate"
                v-model="formData.out_time"
                :placeholder="$placeholder('punch_out_time')"
                :required="true"
                :error-message="$errorMessage(errors, 'out_time')"
            />
            <app-form-group
                :label="$t('note')"
                type="textarea"
                v-model="formData.note"
                :placeholder="$placeholder('note')"
                :required="true"
                :error-message="$errorMessage(errors, 'note')"
            />
        </form>

    </modal>
</template>

<script>
import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import {
    formatDateTimeForServer,
    dateTimeToLocalWithFormat,
    timezoneDate
} from "../../../../../common/Helper/Support/DateTimeHelper";


export default {
    name: "AttendanceEditRequestModal",
    mixins: [ModalMixin, FormHelperMixins],
    props: {
      tableId: {}
    },
    data(){
        return {
            maxDate: timezoneDate()
        }
    },
    methods: {
        submitData(){
            let formData = {...this.formData};
            formData.in_time = formatDateTimeForServer(this.formData.in_time);
            formData.out_time = formatDateTimeForServer(this.formData.out_time);
            this.setBasicFormActionData()
                .save(formData);
        },
        afterSuccessFromGetEditData({data}) {
            this.formData = data;
            this.formData.in_time = new Date(dateTimeToLocalWithFormat(data.in_time));
            this.formData.out_time = new Date(dateTimeToLocalWithFormat(data.out_time));
            this.preloader = false;
        },
        afterSuccess(response) {
            this.formData = {};
            $('#attendance-edit-request-modal').modal('hide');
            this.$emit('input', false);
            this.toastAndReload(response.data.message, this.tableId)
        },

    }
}
</script>

<style scoped>

</style>