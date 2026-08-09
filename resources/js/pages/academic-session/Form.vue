<template>

<form @submit.prevent="$emit('submit')">

    <AppInput
        label="Session Name"
        v-model="form.name"
    />

    <AppInput
        type="date"
        label="Start Date"
        v-model="form.start_date"
    />

    <AppInput
        type="date"
        label="End Date"
        v-model="form.end_date"
    />

    <div class="row">

        <div class="col-md-6">

            <AppSelect
                label="Status"
                v-model="form.status"
                :options="[
                    {label:'Active',value:true},
                    {label:'Inactive',value:false}
                ]"
            />

        </div>

        <div class="col-md-6">

            <AppSelect
                label="Current Session"
                v-model="form.is_current"
                :options="[
                    {label:'Yes',value:true},
                    {label:'No',value:false}
                ]"
            />

        </div>

    </div>

    <AppButton  type="submit"
            :loading="loading" 
            text="Save Session">

       

    </AppButton>

</form>

</template>

<script setup>
import AppInput from "@/components/ui/AppInput.vue";
import AppSelect from "@/components/ui/AppSelect.vue";
import AppButton from "@/components/ui/AppButton.vue";

defineProps({
    form: Object
});

defineEmits(["submit"]);
</script>