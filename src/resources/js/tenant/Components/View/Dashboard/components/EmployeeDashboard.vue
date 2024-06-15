<template>
    <div class="row">
        <div class="col-12 col-lg-8 mb-4 mb-lg-0">
            <div class="card card-with-shadow border-0 py-primary mb-primary">
                <app-overlay-loader v-if="preloader"/>
                <div class="card-body p-0" :class="{'loading-opacity': preloader}">
                    <div class="d-flex justify-content-between pl-primary mb-4">
                        <div>
                            <div class="d-flex align-items-end">
                                <h5 class="mr-2">{{ greetings }},</h5>  <h4>{{ employee.full_name }}.</h4>
                            </div>
                            <h4>
                                <span class="text-muted font-size-80">It's</span> {{ localTime }} <span class="text-muted font-size-80">({{localTimeZone}})</span>
                            </h4>
                            <p class="text-muted mb-0">{{ behaviourComment }}</p>
                        </div>
                        <div>
                            <span
                                class="badge radius-right-0 radius-left-4 text-size-14 text-white padding-x-14 padding-y-9"
                                :class="behaviourClass">
                                        {{ this.$t(attendance.behavior) }}
                            </span>
                        </div>
                    </div>
                    <div class="d-flex px-primary">
                        <div class="d-flex align-items-center mr-5">
                            <div class="icon-box width-60 height-60 mr-2">
                                <app-icon name="log-in" class="text-success"/>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ inTime }}</h6>
                                <p class="text-secondary mb-1">{{ $t('punch_in_only_time') }}</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="icon-box width-60 height-60 mr-2">
                                <app-icon name="log-out" class="text-warning"/>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ outTime }}</h6>
                                <p class="text-secondary mb-1">{{ $t('punch_out_only_time') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <employee-leave-summaries/>
        </div>
        <div class="col-12 col-lg-4">
            <employee-monthly-time-log/>
        </div>

        <div class="col-12 mb-4 mb-lg-0">
            <app-employee-announcement/>
        </div>
    </div>
</template>

<script>
import {axiosGet} from "../../../../../common/Helper/AxiosHelper";
import {APP_DASHBOARD} from "../../../../Config/ApiUrl";
import {
    onlyTime,
    getGreetingTime,
    differentInTime,
    today,
    getUtcToLocalTimeOnly,
    getHoursAndMinutesInString,
    localTimeZone,
    formattedLocalTime,
} from "../../../../../common/Helper/Support/DateTimeHelper";
import EmployeeMonthlyTimeLog from "./EmployeeMonthlyTimeLog"
import EmployeeLeaveSummaries from "./EmployeeLeaveSummaries";

export default {
    name: "EmployeeDashboard",
    components: {EmployeeLeaveSummaries, EmployeeMonthlyTimeLog},
    data() {
        return {
            attendance: {},
            attendanceDetails: {},
            workingShiftDetails: {},
            preloader: false,
            employee: window.user,
            punchIn: false,
            localTimeZone,
            localTime: window.settings.time_format === 'h' ? formattedLocalTime() : `${formattedLocalTime()} O'Clock`,
        }
    },
    created() {
        this.getAttendanceData();
    },
    mounted() {
        this.$hub.$on('reload-dashboard', () => this.getAttendanceData());
        setInterval(() => {
            this.setLocalTime();
        }, 20000);
    },
    methods: {
        getAttendanceData() {
            this.preloader = true;
            axiosGet(`${APP_DASHBOARD}/employee/attendance`).then(({data}) => {
                this.attendance = data ? data : {};
                this.attendanceDetails = data.details ? data.details : {};
                this.workingShiftDetails = data.working_shift?.details ? data.working_shift.details : {};
                this.punchIn = true;
            }).finally(() => {
                this.preloader = false;
            })
        },
        setLocalTime() {
            this.localTime = window.settings.time_format === 'h' ? formattedLocalTime() : `${formattedLocalTime()} O'Clock`;
        },
    },
    computed: {
        inTime() {
            if (this.collection(this.attendanceDetails).last()['in_time']) {
                return onlyTime(this.collection(this.attendanceDetails).last()['in_time']);
            }
            return this.$t('not_yet')
        },
        outTime() {
            if (this.collection(this.attendanceDetails).first()['out_time']) {
                return onlyTime(this.collection(this.attendanceDetails).first()['out_time']);
            }
            return this.$t('not_yet')
        },
        behaviourClass() {
            if (this.attendance.behavior === 'early')
                return 'badge-warning';
            if (this.attendance.behavior === 'late')
                return 'badge-danger';
            if (this.attendance.behavior === 'regular')
                return 'badge-success';
        },
        greetings() {
            return getGreetingTime();
        },
        behaviourComment() {
            if (this.attendance.behavior === 'early') {
                const time = getHoursAndMinutesInString(
                    differentInTime(
                        getUtcToLocalTimeOnly(this.collection(this.attendanceDetails).last()['in_time']),
                        getUtcToLocalTimeOnly(today().format('YYYY-MM-DD') + ' ' + this.collection(this.workingShiftDetails).first()['start_at']),
                    ).asSeconds(),
                    false,
                    'ceil'
                )
                return `You came ${time} early today!`;
            }
            if (this.attendance.behavior === 'late') {
                const time = getHoursAndMinutesInString(
                    differentInTime(
                        getUtcToLocalTimeOnly(today().format('YYYY-MM-DD') + ' ' + this.collection(this.workingShiftDetails).first()['start_at']),
                        getUtcToLocalTimeOnly(this.collection(this.attendanceDetails).last()['in_time']),
                    ).asSeconds()
                )
                return `You are ${time} late!`;
            }
            if (this.attendance.behavior === 'regular') {
                return 'You are regular today!';
            }

            if (!this.punchIn) {
                return this.$t('you_did_not_punch_in');
            }
        }
    }

}
</script>

<style scoped>

</style>