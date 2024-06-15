<template>
    <div>
        <template v-for="file in value">
            <a :href="file.full_url" target="_blank">
                <app-icon name="external-link"/>
            </a>
        </template>
        <div v-if="value.length === 0">
            {{ '-' }}
        </div>
    </div>
</template>

<script>

export default {
    name: "AttachmentsColumn",
    props: {
        value: {},
        rowData: {}
    },
}
</script>
