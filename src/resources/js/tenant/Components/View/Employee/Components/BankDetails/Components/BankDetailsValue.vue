<template>
    <div v-if="value" class="d-flex align-items-center justify-content-between text-size-16 mb-2">
<!--        <div class="mr-2">-->
<!--            <app-icon-->
<!--                name="bold"-->
<!--                class="primary-text-color"-->
<!--                width="16"-->
<!--                height="16"-->
<!--            />-->
<!--        </div>-->
        <label class="text-left d-block mb-0 font-weight-bold">
            {{ label }} :
        </label>
        <div>
            <p class="mb-0 ">{{ value }}</p>
        </div>
    </div>
</template>

<script>
export default {
    name: "BankDetailsValue",
    props: ['label', 'value']
}
</script>