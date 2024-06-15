<template>
    <modal id="employee-bank-details-modal"
           v-model="showModal"
           size="large"
           :title="generateModalTitle('bank_details', true)"
           @submit="submitData"
           :loading="loading"
           :preloader="preloader"
           :scrollable="false"
    >
        <form ref="form"
              :data-url="address ? `${url}/${address.id}` : url"
              @submit.prevent="submitData">


            <app-form-group
                page="page"
                :label="$t('bank_name')"
                :placeholder="$placeholder('bank_name')"
                v-model="formData.name"
                :error-message="$errorMessage(errors, 'name')"
            />

            <app-form-group
                page="page"
                :label="$t('bank_code')"
                :placeholder="$placeholder('bank_code')"
                v-model="formData.code"
                :error-message="$errorMessage(errors, 'code')"
            />

            <app-form-group
                page="page"
                :label="$t('branch_name')"
                :placeholder="$placeholder('branch_name')"
                v-model="formData.branch_name"
                :error-message="$errorMessage(errors, 'branch_name')"
            />
            <app-form-group
                page="page"
                :label="$t('account_title')"
                :placeholder="$placeholder('account_title')"
                v-model="formData.account_title"
                :error-message="$errorMessage(errors, 'account_title')"
            />
            <app-form-group
                page="page"
                :label="$t('account_holder_name')"
                :placeholder="$placeholder('account_holder_name')"
                v-model="formData.account_holder_name"
                :error-message="$errorMessage(errors, 'account_holder_name')"
            />


            <app-form-group
                page="page"
                :label="$t('account_number')"
                :placeholder="$placeholder('account_number')"
                v-model="formData.account_number"
                :error-message="$errorMessage(errors, 'account_number')"
            />
            <app-form-group
                page="page"
                :label="$t('tax_payer_id')"
                :placeholder="$placeholder('tax_payer_id')"
                v-model="formData.tax_payer_id"
                :error-message="$errorMessage(errors, 'tax_payer_id')"
            />

        </form>
    </modal>
</template>
<script>

import ModalMixin from "../../../../../../common/Mixin/Global/ModalMixin";
import FormHelperMixins from "../../../../../../common/Mixin/Global/FormHelperMixins";

export default {
    name: "EmployeeAddressDetailsEditModal",
    mixins: [FormHelperMixins, ModalMixin],
    props: {
        url: {},
        address: {},
    },
    data() {
        return {
            formData: {...this.address.value},
            errors: {},
            preloader: false,
        }
    },
    methods: {
        submitData() {
            this.loading = true;
            if (this.address?.id){
                this.formData._method = 'PATCH';
            }
            this.save(this.formData)
        },
        afterSuccess({data}) {
            this.loading = false;
            $('#employee-bank-details-modal').modal('hide');
            this.$toastr.s('', data.message);
            this.$emit('reload')
        },
    },
}
</script>