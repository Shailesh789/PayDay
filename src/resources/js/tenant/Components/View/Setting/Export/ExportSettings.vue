<template>
    <div>
        <form class="mb-0" ref="form" enctype="multipart/form-data">
            <div>
                <div class="note-title d-flex">
                    <app-icon name="book-open" />
                    <h6 class="card-title pl-2">{{ $t("remember") }}</h6>
                </div>
                <div class="note note-warning p-4 mb-2">
                    <ol>
                        <li class="my-1"> {{ $t("csv_export_guide1") }}</li>
<!--                        <li class="my-1"> {{ $t("csv_export_guide2") }}</li>-->
                    </ol>
                </div>

                <div class="my-primary">
                    <label for="fields">{{ $t('select_type') }}</label>
                    <div class="d-flex g-1">
                        <app-input v-model="formData.all" type="checkbox"
                            :list="[{ id: 'all_data', value: $t('all_data') }]" radioCheckboxWrapper="d-flex g-1"
                            @changed="allChange" />

                        <app-input v-model="formData.fields" type="checkbox" :list="list"
                            radioCheckboxWrapper="d-flex g-1" @input="inputListValue" />
                    </div>
                    <button type="button" class="btn btn-primary my-primary"
                        :disabled="![...this.formData.all, ...this.formData.fields].length" @click="exportHandler">{{
                        $t('export')
                    }}</button>
                </div>
            </div>
        </form>
        <export-modal :modal-id="exportModalId" v-if="isConfirmationModalActive" @close-modal="closeModal">
            <template v-if="stage === 'init'" #init>
                <div class="text-center big-icon mt-primary" :key="iconKey">
                    <app-icon :name="'download'" class="text-primary" />
                </div>
                <h5 class="text-center font-weight-bold mt-4">{{ $t('are_you_sure') }}</h5>
                <p class="mb-primary text-center font-size-90 p-0"> {{ $t('are_you_sure_you_want_to_download_data') }}
                </p>
                <div class="d-flex gap-2 justify-content-center mb-primary">
                    <div class="d-flex align-items-center">
                        <span class="d-flex px-3 py-2 form-control">{{ equation.field1 }}</span>
                        <span class="d-flex px-3 py-2 --form-control">+</span> 
                        <span class="d-flex px-3 py-2 form-control">{{ equation.field2 }}</span>
                    </div>
                    <span class="d-flex px-3 py-2">=</span>
                    <input type="text" class="form-control" v-model="equation.result" :placeholder="$t('answer')" style="width: 80px;">
                </div>
                <div class="text-center">
                    <button class="btn btn-secondary mr-2" data-dismiss="modal" @click.prevent="closeModal">{{ $t('no')
                        }}</button>
                    <button class="btn btn-primary" @click.prevent="confirm"
                        :disabled="(equation.field1 + equation.field2)?.toString() !== equation.result?.toString()">{{ $t('yes') }} </button>
                </div>
            </template>
            <template v-if="stage === 'process'" #process>
                <div class="text-center big-icon mt-5" :key="iconKey">
                    <app-icon :name="'refresh-cw'" class="text-primary rotating" />
                </div>
                <h5 class="text-center font-weight-bold mt-4">{{ $t('export_in_progress') }}</h5>
                <p class="mb-primary text-center font-size-90 p-0"> {{ $t('please_wait_until_progress_is_done') }}</p>
            </template>
            <template v-if="stage === 'retry'" #retry>
                <div class="text-center big-icon" :key="iconKey">
                    <app-icon :name="'alert-triangle'" class="text-danger" />
                </div>
                <h5 class="text-center font-weight-bold mt-4">{{ $t('export_data_failure') }}</h5>
                <div>
                    <p>We're sorry, but we couldn't process your export request.</p>
                    <p class="font-weight-bold">Error: {{ errorMessage }}</p>

                    <p>Please try the following:</p>
                    <ul>
                        <li>Check your internet connection and try again.</li>
                        <li>If the problem persists, please try exporting a smaller data set.</li>
                    </ul>
<!--                    <p>Need assistance? <a href="#">Contact our support</a> team using the Error Code[Error_code] for help</p>-->
                </div>

                <div class="text-center">
                    <button class="btn btn-secondary mr-2" data-dismiss="modal" @click.prevent="closeModal">{{
                        $t('cancel') }}</button>
                    <button class="btn btn-primary" @click.prevent="confirm">{{ $t('retry') }} </button>
                </div>
            </template>
        </export-modal>
    </div>
</template>

<script>
import { FormMixin } from "../../../../../core/mixins/form/FormMixin";
import { axiosPost, urlGenerator } from "../../../../../common/Helper/AxiosHelper";
import ExportModal from "./ExportModal.vue";
import {localTimeZone} from "../../../../../common/Helper/Support/DateTimeHelper";

export default {
    mixins: [FormMixin],
    components: { ExportModal },
    data() {
        return {
            preloader: false,
            exportModalId: 'export-modal',
            list: [
                { id: 'employee', value: this.$t('employee') },
                { id: 'leave', value: this.$t('leave') },
                { id: 'attendance', value: this.$t('attendance') },
                { id: 'work_shift', value: this.$t('work_shift') }
            ],
            formData: { fields: ['employee', 'leave', 'attendance', 'work_shift'], all: ['all_data'] },
            equation: { field1: this.randomValue(1,10), field2: this.randomValue(1,10) },
            isConfirmationModalActive: false,
            errorMessage: '',
            stage: 'init',
            iconKey: 0,
            title: this.$t('are_you_sure'),
        };
    },
    methods: {
        randomValue(min, max) {
            return Math.round(Math.random() * (max - min) + min);
        },
        allChange(e) {
            if (e.target.checked) {
                this.formData.fields = this.list.map(i => i.id)
            } else {
                this.formData.fields = []
            }
        },
        inputListValue(e) {
            let allList = !!(!this.list.map(i => i.id).filter(i => !e.includes(i)).length)
            if (allList) {
                this.formData.all = ['all_data']
            } else {
                this.formData.all = []
            }
        },
        exportHandler() {
            this.isConfirmationModalActive = true
            this.equation = { field1: this.randomValue(1,10), field2: this.randomValue(1,10) }
        },
        confirm() {
            this.preloader = true;
            this.stage = 'process'
            this.iconKey++
            axiosPost(this.apiUrl.EXPORT_MODULES+'?timeZone='+localTimeZone, { fields: [...this.formData.all, ...this.formData.fields] }).then(({ data }) => {
                console.log(data)
                this.$toastr.s(data.message);
                this.done(this.apiUrl.EXPORT_FILE_DOWNLOAD + '?fileName=' + data.file_name)
                this.closeModal()
            }).catch(({ response }) => {
                console.log('err', response)
                this.errorMessage = response.data.message;
                this.iconKey++;
                this.stage = 'retry'
            }).finally(() => {
                this.preloader = false;
            })
        },
        closeModal() {
            $(`#${this.exportModalId}`).modal('hide');
            this.isConfirmationModalActive = false
            this.equation = {  }
            this.stage = 'init'
        },
        done(url = null) {
            window.location = urlGenerator(url);
            // window.open(urlGenerator(url), '_blank');
        },
        retry() {
            this.confirm()
        },
    }
};
</script>
