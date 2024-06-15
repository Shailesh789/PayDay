<template>
    <div class="content-wrapper">
        <div class="d-flex align-items-center justify-content-between">
            <app-breadcrumb :page-title="$t('break_time')"/>

            <div>
                <a :href="urlGenerator('/administration/work-shifts')" class="btn btn-success mr-2 mb-4">
                    <app-icon class="size-20 mr-2" name="arrow-left"/>
                    {{ $t('back_to_workshift') }}
                </a>

                <button class="btn btn-with-shadow btn-info mb-4"
                        type="button"
                        @click="openModal()">
                    <app-icon class="size-20 mr-2" name="plus"/>
                    {{ $t('add_break_time') }}
                </button>
            </div>
        </div>

        <app-table :id="tableId" :options="options" @action="triggerActions"/>

        <break-time-creat-edit-modal
            v-if="isModalActive"
            v-model="isModalActive"
            :selected-url="selectedUrl"
            @close="isModalActive = false"
        />

        <app-confirmation-modal
            v-if="confirmationModalActive"
            modal-id="app-confirmation-modal"
            @confirmed="confirmed('break-time-table')"
            @cancelled="cancelled"
            icon="trash-2"
            sub-title=""
            :message="message"
            modal-class="danger"
        />
    </div>
</template>

<script>

import DatatableHelperMixin from "../../../../../../common/Mixin/Global/DatatableHelperMixin";
import {BREAK_TIME} from "../../../../../Config/ApiUrl";
import {urlGenerator} from "../../../../../../common/Helper/AxiosHelper";
import BreakTimeCreatEditModal from "./BreakTimeCreateEditModal";

export default {
    name: "BreakTime",
    mixins: [DatatableHelperMixin],
    components: {BreakTimeCreatEditModal},
    data() {
        return {
            urlGenerator,
            isModalActive: false,
            selectedUrl: '',
            tableId: 'break-time-table',
            message: '',
            options: {
                url: BREAK_TIME,
                name: 'break-time-table',
                columns: [
                    {
                        title: this.$t('name'),
                        type: 'text',
                        key: 'name',
                    },
                    {
                        title: this.$t('duration'),
                        type: 'text',
                        key: 'duration',
                    },
                    {
                        title: this.$t('actions'),
                        type: 'action'
                    }
                ],
                filters: [],
                actions: [
                    {
                        title: this.$t('edit'),
                        name: 'edit',
                        icon: 'edit'
                    },
                    {
                        title: this.$t('delete'),
                        icon: 'trash-2',
                        message: this.$t('you_are_going_to_delete_message', {resource: this.$t('break_time')}),
                        name: 'delete',
                        modifier: row => true
                    }
                ],
                rowLimit: 10,
                orderBy: 'desc',
                responsive: true,
                showHeader: true,
                showFilter: true,
                showSearch: true,
                showAction: true,
                tableShadow: true,
                actionType: 'default',
                datatableWrapper: true,
                paginationType: 'pagination'
            },
        }
    },
    methods: {
        openModal() {
            this.selectedUrl = '';
            this.isModalActive = true;
        },
        triggerActions(row, action, active) {
            if (action.name === 'edit') {
                this.selectedUrl = `${BREAK_TIME}/${row.id}`;
                this.isModalActive = true;
            } else if (action.name === 'delete') {
                this.delete_url = `${BREAK_TIME}/${row.id}`;
                this.message = action.message;
                this.confirmationModalActive = true;
            }
        }
    }
}
</script>