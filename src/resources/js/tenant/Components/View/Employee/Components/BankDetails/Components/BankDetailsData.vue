<template>
    <div class="row mb-primary"
         :class="{'align-items-center': !address.value}">
<!--        <div class="col-lg-4">-->
<!--            <div class="d-flex align-items-center mb-3 mb-lg-0">-->
<!--                <div>-->
<!--                    <div class="icon-box mr-2">-->
<!--                        <app-icon name="bold"/>-->
<!--                    </div>-->
<!--                </div>-->
<!--                {{ $t('bank_details') }}-->
<!--            </div>-->
<!--        </div>-->
        <div class="col-lg-8">
            <template v-if="address.value">
                <bank-details-value :value="address.value['account_holder_name']" :label="$t('account_holder_name')" />
                <bank-details-value :value="address.value['account_number']" :label="$t('account_number')" />
                <bank-details-value :value="address.value['account_title']" :label="$t('account_title')" />
                <bank-details-value :value="address.value['name']" :label="$t('bank_name')" />
                <bank-details-value :value="address.value['branch_name']" :label="$t('branch_name')" />
                <bank-details-value :value="address.value['code']" :label="$t('bank_code')" />
                <bank-details-value :value="address.value['tax_payer_id']" :label="$t('tax_payer_id')" />
            </template>

            <p v-else class="text-muted mb-0">{{ $t('not_added_yet') }}</p>
        </div>
        <div class="col-lg-3">
            <div class="text-right mt-3 mt-lg-0">
                <div v-if="address.value" class="btn-group btn-group-action" role="group"
                     aria-label="Default action">
                    <button class="btn"
                            data-toggle="tooltip"
                            data-placement="top"
                            :title="$t('edit')"
                            v-if="$can('update_employee_address')"
                            @click.prevent="$emit('edit', type)">
                        <app-icon name="edit"/>
                    </button>
                    <button class="btn"
                            data-toggle="tooltip"
                            data-placement="top"
                            :title="$t('delete')"
                            v-if="$can('delete_employee_address')"
                            @click.prevent="$emit('delete', type)">
                        <app-icon name="trash-2"/>
                    </button>
                </div>
                <template v-else>
                    <button class="btn btn-primary"
                            @click="$emit('add', type)"
                            v-if="$can('update_employee_address')">
                        {{ $t('add') }}
                    </button>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
import BankDetailsValue from "./BankDetailsValue";

export default {
    name: "Address",
    components: {BankDetailsValue},
    props: {
        address: {},
        type: {}
    },

}
</script>