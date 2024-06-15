<template>
    <app-overlay-loader v-if="preloader"/>
    <form v-else method="post" ref="form" :data-url="`${this.apiUrl.APP_SETTINGS}/attendance-ip`">

        <app-form-group
            :label="$t('ip_validation')"
            v-model="formData.ip_validation"
            :recommendation="$t('punch_in_ip_validation')"
            page="page"
            type="radio"
            radio-checkbox-name="ip_validation"
            radio-checkbox-wrapper="row"
            label-alignment="align-item-start"
            :error-message="$errorMessage(errors, 'ip_validation')"
            :list="ip_validation_options"
        />

        <div class="form-group pt-0" v-if="formData.ip_validation !== 'none'">
            <div class="row align-items-center">
                <div class="col-lg-3 col-xl-3 col-md-3 col-sm-12 col-xs-12 ">
                    <label class="text-left d-block mb-lg-0">
                        {{ $t(formData.ip_validation) }}
                    </label>
                </div>
                <div class="col-lg-8 col-xl-8 col-md-8 col-sm-12 col-xs-12">
                    <div v-for="(ip, index) in formData.ip_list" :key="index" class="d-flex">
                        <div class="col-10 pl-0">
                            <app-input
                                type="text"
                                v-model="formData.ip_list[index]"
                                :placeholder="$t('enter_ip')"
                            />
                        </div>
                        <div class="col-1 pl-0 ml-0" v-if="formData.ip_list.length !== 1" :key="index+1">
                            <a href="#" @click.prevent="removeSelectedIp(ip, index)">
                                <app-icon name="trash" class="text-primary mt-2" width="22"/>
                            </a>
                        </div>
                        <div class="col-1 pl-0 ml-0" v-if="formData.ip_list.length === index+1" :key="index+2">
                            <a href="#" @click.prevent="addNewIp()">
                                <app-icon name="plus" class="text-primary mt-2" width="22"/>
                            </a>
                        </div>
                    </div>
                    <div v-if="Object.values(errors).length" class="pl-0">
                        <small class="text-warning">{{ Object.values(errors)[0][0] }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <app-submit-button :label="$t('save')" :loading="loading" @click="submitData"/>
            </div>
        </div>
    </form>
</template>

<script>
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import {axiosGet} from "../../../../../common/Helper/AxiosHelper";

export default {
    name: "AttendanceIpSettings",
    mixins: [FormHelperMixins],
    data() {
        return {
            ip_validation_options: [
                {id: 'allow_ip', value: this.$t('allow_ip')},
                {id: 'restrict_ip', value: this.$t('restrict_ip')},
                {id: 'none', value: this.$t('none')},
            ],
            formData: {
                ip_validation: '',
                ip_list: [],
            },
        }
    },
    methods: {
        submitData() {
            this.formData.ip_list = this.formData.ip_list.filter((ip) => ip !== '')
            if (this.formData.ip_validation === 'none') {
                this.formData.ip_list = null;
            }
            this.setBasicFormActionData()
                .save(this.formData);
        },
        afterSuccess(response) {
            this.toastAndReload(response.data.message);
            if (!this.formData.ip_list) {
                this.formData.ip_list = ['']
            }
        },
        afterError(response) {
            if (this.formData.ip_list.length < 1) {
                this.formData.ip_list = ['']
            }
            this.message = '';
            this.loading = false;
            this.errors = response.data.errors || {};
            if (response.status != 422)
                this.$toastr.e(response.data.message || response.statusText)
        },
        getIpSetting() {
            this.preloader = true;
            axiosGet(`${this.apiUrl.APP_SETTINGS}/attendance-ip`).then(({data}) => {
                this.formData.ip_validation = data.ip_validation ? data.ip_validation : 'none';
                this.formData.ip_list = data.ip_list ? JSON.parse(data.ip_list) : [''];
            }).finally(() => this.preloader = false)
        },
        removeSelectedIp(ip, index) {
            this.formData.ip_list.splice(index, 1);
        },
        addNewIp() {
            this.formData.ip_list.push('');
        }
    },
    mounted() {
        this.getIpSetting();
    }
}
</script>
