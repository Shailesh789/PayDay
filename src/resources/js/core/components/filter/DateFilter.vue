<template>
    <div class="single-filter date-filter">
        <v-date-picker color="blue"
                       v-model="value"
                       @input="input"
                       mode="date"
                       :model-config="modelConfig"
                       :isDark="$store.state.theme.darkMode"
                       :masks="{
                       input:[dateFormat],
                       inputDateTime:[dateFormat+' h:mm A'],
                       inputDateTime24hr:[dateFormat+' HH:mm'],
                       inputTime:['h:mm A'],
                       inputTime24hr:['HH:mm']
                       }"
                       :popover="{visibility: 'click', placement: 'bottom-start'}">
            <template v-slot="{ inputValue, inputEvents }">
                <button class="btn btn-filter"
                        :class="{'applied': isApply}"
                        :id="filterId"
                        data-toggle="dropdown"
                        v-on="inputEvents"
                        aria-haspopup="true" aria-expanded="false">
                    {{ inputValue && isApply ? inputValue : (value ? $t('today') : label) }}
                    <span class="clear-icon" v-if="isApply" @click.prevent="clear($event)">
                        <app-icon :name="'x'"/>
                    </span>
                </button>
            </template>
        </v-date-picker>
    </div>
</template>

<script>
import {FilterMixin} from './mixins/FilterMixin';
import CoreLibrary from "../../helpers/CoreLibrary";
import moment from 'moment/moment'

export default {
    name: "DateFilter",
    extends: CoreLibrary,
    mixins: [FilterMixin],
    props: {
        label: {
            type: String,
            default: function () {
                return this.$t('date')
            }
        }
    },
    data() {
        return {
            isApply: false,
            value: moment(new Date()).format(),
            modelConfig: {
                type: 'string',
                mask: 'YYYY-MM-DD', // Uses 'iso' if missing
            },
        }
    },
    mounted() {
        this.$hub.$on('clearAllFilter-' + this.tableId, () => {
            this.isApply = false;
            this.value = moment(new Date()).format();
        });
    },
    methods: {
        clear(e) {
            e.stopPropagation();
            this.isApply = false;
            this.value = moment(new Date()).format('YYYY-MM-DD');
            this.sendValue(this.value);
        },
        input(value) {
            if (value) {
                this.isApply = true;
                this.sendValue(value);
            } else {
                this.isApply = false;
                this.value = null;
            }
        },
        sendValue(value) {
            this.returnValue(value);
        }
    }
}
</script>

