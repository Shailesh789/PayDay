<template>
    <modal id="break-time-start-modal"
           size="default"
           v-model="showModal"
           :title="onBreak ? $t('end_break') : $t('start_break')"
           @submit="submit"
           :btn-label="onBreak ? $t('end') : $t('start')"
           :cancel-btn-label="$t('close')"
           :loading="loading">

        <div v-if="onBreak" class="d-flex flex-column align-items-center">
            <h4>{{ onBreak.break_time.name }}</h4>
            <h1 class="text-warning">{{ convertSecondToHourMinutes(duration, true) }}</h1>
        </div>
        <app-input
            v-else
            type="radio"
            :list="breakTimeList"
            v-model="selected"
            custom-radio-type="mb-2"
            radio-checkbox-wrapper="d-flex flex-column"
            custom-checkbox-type="checkbox-default"
            :error-message="$errorMessage(errors, 'break_time')"
        />
    </modal>
</template>

<script>

import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import {TENANT_BASE_URL} from "../../../../../common/Config/UrlHelper";
import {
    convertSecondToHourMinutes,
    getDifferentTillNow,
    getDurationFromTime
} from "../../../../../common/Helper/Support/DateTimeHelper";

export default {
    name: "BreakTimeStartModal",
    mixins: [ModalMixin, FormHelperMixins],
    props: {
        breakTimes: {
            type: Array
        },
        detailsId: {
            type: Number
        },
        onBreak: {}
    },
    data() {
        return {
            convertSecondToHourMinutes,
            selected: this.onBreak ? this.onBreak.break_time?.id : null,
            duration: '00:00',
            interval: null,
        }
    },
    mounted() {
        this.breakDuration();
        this.interval = setInterval(this.breakDuration,  1000);
    },
    methods: {
        submit() {
            this.loading = true;
            let url = `${TENANT_BASE_URL}employees/${this.detailsId}/${this.onBreak ? 'break-end' : 'break-start'}`
            this.submitFromFixin(`patch`, url, {
                break_time: this.selected,
                attendance_details: this.detailsId,
            });
        },
        afterSuccess({data}) {
            this.$toastr.s('', data.message);
            this.$hub.$emit('reload-punch-in-out-button');
            this.$hub.$emit('reload-dashboard');
            $('#break-time-start-modal').modal('hide');
        },
        breakDuration() {
            if (this.onBreak) {
                this.duration = getDifferentTillNow(this.onBreak.start_at).asSeconds();
            }
        }
    },
    computed: {
        breakTimeList(){
            return this.breakTimes.map((bt) => {
                return {
                    id: bt.id,
                    value: `${bt.name} (${getDurationFromTime(bt.duration)})`
                }
            })
        }
    },
    destroyed() {
        clearInterval(this.interval);
    },
}
</script>