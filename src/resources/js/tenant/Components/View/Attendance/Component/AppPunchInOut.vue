<template>
    <div class="d-flex align-items-center">
        <app-pre-loader v-if="preloader"/>
        <button v-else
                class="btn"
                :class="!punch ? 'btn-success' : 'btn-warning'"
                @click.prevent="$emit('open-modal', punch)">

            {{ !punch ? $t('punch_in') : $t('punch_out') }}
        </button>
        <button v-if="punch && breakTimes.length"
                class="btn btn-primary ml-3"
                @click.prevent="$emit('open-break-time', breakTimes, detailsId, onBreak)">

            {{ onBreak ? `${$t('on')} ${onBreak.break_time.name}` : $t('take_a_break') }}
        </button>
    </div>
</template>

<script>
    import {axiosGet} from "../../../../../common/Helper/AxiosHelper";
    import {CHECK_PUNCH_IN} from "../../../../Config/ApiUrl";

    export default {
        name: "AppPunchInOut",
        data() {
            return {
                preloader: false,
                punch: false,
                breakTimes: [],
                detailsId: null,
                onBreak: {}
            }
        },
        mounted() {
            this.checkPunchIn();

            this.$hub.$on('reload-punch-in-out-button', () => this.checkPunchIn())
        },
        methods: {
            checkPunchIn() {
                this.preloader = true;
                axiosGet(CHECK_PUNCH_IN).then(({data}) => {
                    this.breakTimes = data.break_times;
                    this.detailsId = data.details;
                    this.onBreak = data.on_break;
                    this.punch = !!(data.punched);
                }).finally(() => {
                    this.preloader = false;
                })
            }
        }
    }
</script>