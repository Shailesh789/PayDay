<template>
    <modal id="break-time-modal"
           size="default"
           v-model="showModal"
           :title="$t(this.selectedUrl ? 'edit_break_time' : 'add_break_time')"
           @submit="submit"
           :loading="loading"
           :preloader="preloader">

        <form :data-url='selectedUrl ? selectedUrl : apiUrl.BREAK_TIME'
              method="POST"
              ref="form"
              @submit.prevent="submit"
              style="height: 200px"
        >
            <app-form-group
                :label="$t('name')"
                type="text"
                v-model="formData.name"
                :placeholder="$placeholder('name', '')"
                :required="true"
                :error-message="$errorMessage(errors, 'name')"
            />
            <app-form-group
                :label="$t('duration')"
                type="time"
                :hour-format="24"
                v-model="formData.duration"
                :placeholder="$placeholder('duration', '')"
                :required="true"
                :error-message="$errorMessage(errors, 'duration')"
            />
        </form>
    </modal>
</template>

<script>


import FormHelperMixins from "../../../../../../common/Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../../../../common/Mixin/Global/ModalMixin";
import {formatDateForServer} from "../../../../../../common/Helper/Support/DateTimeHelper";

export default {
    name: "BreakTimeCreateEditModal",
    mixins: [FormHelperMixins, ModalMixin],

    data() {
        return {

        }
    },
    computed: {

    },
    methods: {
        submit() {
            let formData = {...this.formData};
            this.loading = true;
            this.message = '';
            this.errors = {};
            this.save(formData);
        },
        afterSuccess({data}) {
            this.formData = {};
            $('#break-time-modal').modal('hide');
            this.$emit('input', false);
            this.toastAndReload(data.message, 'break-time-table');
        },
    }
}
</script>