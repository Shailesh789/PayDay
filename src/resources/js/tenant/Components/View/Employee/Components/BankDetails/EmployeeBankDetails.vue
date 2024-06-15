<template>
    <div>
        <app-overlay-loader v-if="preloader"/>
        <template v-else>
            <bank-details-data
                :address="bankDetails"
                @add="openModal"
                @edit="openModal"
                @delete="getConfirmations"
            />
        </template>

        <bank-details-add-edit-modal
            v-if="isModalActive"
            v-model="isModalActive"
            :address="bankDetails"
            @close="isModalActive = false"
            @reload="getAddresses()"
            :url="url"
        />

        <app-confirmation-modal
            v-if="confirmationModalActive"
            icon="trash-2"
            modal-id="app-confirmation-modal"
            @confirmed="deleteModal"
            @cancelled="cancelled"
            @reload="getAddresses()"
        />
    </div>
</template>

<script>
import {axiosGet, axiosDelete} from "../../../../../../common/Helper/AxiosHelper";
import BankDetailsData from "./Components/BankDetailsData";
import BankDetailsAddEditModal from "./BankDetailsAddEditModal";

export default {
    name: "EmployeeBankDetails",
    props: ['props'],
    components: {BankDetailsData, BankDetailsAddEditModal},
    data() {
        return {
            confirmationModalActive: false,
            bankDetails: {},
            preloader: true,
            isModalActive: false,
            title: '',
            selectedUrl: '',

        }
    },
    mounted() {
        this.getAddresses();
    },
    methods: {
        getAddresses() {
            axiosGet(this.url).then(({data}) => {
                this.bankDetails = data ?? {};
            }).finally(() => {
                this.preloader = false;
            });
        },
        openModal(type) {
            this.isModalActive = true;
            if (type === 'edit')
                this.selectedUrl = `${this.url}/${this.bankDetails.id}`;

        },
        getAddress(type) {
            return this.addresses.find(address => address.key === type) || {}
        },
        deleteModal() {
            axiosDelete(`${this.url}/${this.bankDetails.id}`).then(({data}) => {
                this.$toastr.s('', data.message);
                this.getAddresses();
                this.confirmationModalActive = false;
            }).catch(({response}) => {
                this.$toastr.e(response.data.message);
            })
        },
        getConfirmations(type) {
            this.title = type;
            this.confirmationModalActive = true;
        },
        cancelled() {
            this.confirmationModalActive = false;
        },
    },
    computed: {
        url() {
            return `${this.apiUrl.EMPLOYEES}/${this.props.id}/bank-details`;
        }
    }
}
</script>