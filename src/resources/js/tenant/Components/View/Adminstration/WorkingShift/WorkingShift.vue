<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('work_shifts')">
            <button
                type="button"
                class="btn btn-success btn-with-shadow mr-1"
                @click="viewBreakTimePage">
                <app-icon name="clock" class="size-20 mr-2"/>
                {{ $t('break_time') }}
            </button>
            <app-default-button
                :title="$fieldTitle('add', 'work_shift', true)"
                v-if="$can('create_working_shifts')"
                @click="openModal()"
            />
        </app-page-top-section>

        <app-table
            id="working-shift-table"
            :options="options"
            @action="triggerActions"
        />

        <app-working-shift-modal
            v-if="isModalActive"
            v-model="isModalActive"
            :selected-url="selectedUrl"
            @close="isModalActive = false"
        />

        <app-confirmation-modal
            v-if="confirmationModalActive"
            modal-id="app-confirmation-modal"
            @confirmed="confirmed('working-shift-table')"
            @cancelled="cancelled"
            icon="trash-2"
            sub-title=""
            :message="message"
            modal-class="danger"
        />

        <app-employee-to-work-shift
            v-if="isAddEmployeeModalActive"
            v-model="isAddEmployeeModalActive"
            :id="workingShiftId"
        />

        <manage-break-time-modal
            v-if="isBreakTimeModalActive"
            v-model="isBreakTimeModalActive"
            :work-shift="selectedWorkShift"
        />

    </div>
</template>

<script>

import HelperMixin from "../../../../../common/Mixin/Global/HelperMixin";
import WorkingShiftMixin from "../../../Mixins/WorkingShiftMixin";
import {WORKING_SHIFTS} from "../../../../Config/ApiUrl";
import {urlGenerator} from "../../../../../common/Helper/AxiosHelper";
import ManageBreakTimeModal from "./ManageBreakTimeModal";

export default {
    name: "WorkingShift",
    mixins: [HelperMixin, WorkingShiftMixin],
    components: {ManageBreakTimeModal},
    data() {
        return {
            isModalActive: false,
            workingShiftId: '',
            selectedUrl: '',
            message: '',
            isAddEmployeeModalActive: false,
            isBreakTimeModalActive: false,
            selectedWorkShift: {},
        }
    },

    methods: {
        openModal() {
            this.selectedUrl = '';
            this.isModalActive = true;
        },
        triggerActions(row, action, active) {
            if (action.name === 'edit') {
                this.selectedUrl = `${WORKING_SHIFTS}/${row.id}`;
                this.isModalActive = true;
            } else if (action.name === 'delete') {
                this.delete_url = `${WORKING_SHIFTS}/${row.id}`;
                this.message = action.message;
                this.confirmationModalActive = true;
            }else if (action.name === 'add-employee') {
                this.isAddEmployeeModalActive = true;
                this.workingShiftId = row.id
            }else if (action.name === 'break-time') {
                this.selectedWorkShift = row;
                this.isBreakTimeModalActive = true;
            }
        },
        viewBreakTimePage() {
            window.location = urlGenerator('work-shift/break-time');
        },
    },
}
</script>
