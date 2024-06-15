<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('dashboard')">
            <template v-if="isTenantExist || tenant.is_single">
                <div class="d-flex">
                    <app-punch-in-out @open-modal="openPunchInModal" @open-break-time="openBreakTimeModal"/>
                    <button class="btn btn-outline-primary ml-3"
                            v-if="adminSummaryPermissions || employeeStatisticsPermissions || attendancePermissions"
                            @click="isAdmin = !isAdmin">
                        {{ dashboardButtonLabel }}
                    </button>
                </div>
            </template>
        </app-page-top-section>

        <app-employee-dashboard v-if="!isAdmin"/>

        <app-admin-dashboard v-if="isAdmin"/>

        <punch-in-out-modal
            v-if="isModalActive"
            v-model="isModalActive"
            :punch="punchStatus"
            @close="isModalActive = false"/>

        <break-time-start-modal
            v-if="breakTimeModalActive"
            v-model="breakTimeModalActive"
            :break-times="breakTimes"
            :on-break="onBreak"
            :details-id="attendanceDetailsId"
        />
    </div>
</template>

<script>

import AppPunchInOut from "../Attendance/Component/AppPunchInOut";
import PunchInOutModal from "../Attendance/Component/PunchInOutModal";
import BreakTimeStartModal from "../Attendance/Component/BreakTimeStartModal";

export default {
    name: "Dashboard",
    components: {BreakTimeStartModal, PunchInOutModal, AppPunchInOut},
    data() {
        return {
            punchStatus: '',
            isModalActive: false,
            isAdmin: this.$isAdmin(),
            breakTimeModalActive: false,
            breakTimes: [],
            attendanceDetailsId: null,
            onBreak: null,
        }
    },
    methods: {
        openPunchInModal(value) {
            this.punchStatus = value;
            this.isModalActive = true;
        },
        openBreakTimeModal(value, detailsId, onBreak) {
            this.breakTimes = value;
            this.onBreak = onBreak;
            this.attendanceDetailsId = detailsId;
            this.breakTimeModalActive = true;
        },
    },
    computed: {
        isTenantExist() {
            return !!window.tenant && !window.tenant.is_single;
        },
        tenant() {
            return window.tenant || {};
        },
        dashboardButtonLabel() {
            return this.isAdmin ? this.$t('view_as_employee')
                : (this.$isAdmin() ? this.$t('view_as_admin') : this.$t('view_as_manager'));
        },
        adminSummaryPermissions() {
            return this.$can('view_employees') ||
                this.$can('view_departments') || this.$can('view_all_leaves')
        },
        employeeStatisticsPermissions() {
            return this.$can('view_employment_statuses') ||
                this.$can('view_designations') || this.$can('view_departments')
        },
        attendancePermissions() {
            return this.$can('view_all_attendance')
        }
    }
}
</script>

