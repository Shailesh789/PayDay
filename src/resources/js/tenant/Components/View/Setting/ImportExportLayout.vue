<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('import_/_export')" />
        <app-custom-tab :tabs="tabs" icon="settings" />
    </div>
</template>

<script>
export default {
    data() {
        return {
            tabs: [
                {
                    name: this.$t('import'),
                    title: this.$t('import'),
                    component: "app-import-employees",
                    children: [
                        {
                            name: this.$t('employees'),
                            title: this.$t('import_employee'),
                            component: "app-import-employees",
                            permission: !!this.$can('import_employees')
                        },
                        {
                            name: this.$t('attendance'),
                            title: this.$t('import_attendance'),
                            component: "app-import-attendances",
                            permission: this.$can('import_attendances'),
                        }
                    ],
                    permission: !!this.$can('import_employees')
                },
                {
                    name: this.$t('export'),
                    title: this.$t('export'),
                    component: "app-export",
                    permission: this.$can('export_module'),
                },
            ]
            // tabs: [
            //     {
            //         name: this.$t('employees'),
            //         title: this.$t('import_employee'),
            //         component: "app-import-employees",
            //         permission: !!this.$can('import_employees')
            //     },
            //     {
            //         name: this.$t('attendance'),
            //         title: this.$t('import_attendance'),
            //         component: "app-import-attendances",
            //         permission: this.$can('import_attendances'),
            //     },
            // ]
        }
    }
}
</script>

<style scoped></style>