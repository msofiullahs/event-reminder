<script setup>
import { onMounted, ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { Modal } from 'bootstrap';
import InputLabel from '@/Components/InputLabel.vue';
import { useDropzone } from "vue3-dropzone";

let modalEle = ref(null);
let thisModalObj = null;

const emit = defineEmits(['posted']);

const form = useForm({
    file: null,
});

onMounted(() => {
    thisModalObj = new Modal(modalEle.value);
});

const showModal = () => {
    thisModalObj.show();
}

const closeModal = () => {
    form.reset();
    thisModalObj.hide();
}

defineExpose({ showModal: showModal });

const submitForm = () => {
    form.post(route('import'), {
        onSuccess: () => {
            form.reset();
            postedData();
        }
    })
}

const postedData = () => {
    emit('posted', true);
    closeModal();
}

let fileCsv = ref(null);
const onDrop = (acceptFiles, rejectReasons) => {
    const file = acceptFiles[0];
    fileCsv.value = URL.createObjectURL(file);
    form.file = acceptFiles[0];
}
const { getRootProps, getInputProps, ...rest } = useDropzone({
    onDrop: onDrop
});
</script>

<template>
    <form @submit.prevent="submitForm">
        <div class="modal fade" tabindex="-1" aria-hidden="true" ref="modalEle">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Events</h5>
                        <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <InputLabel for="file" value="CSV File" />
                            <div class="d-flex justify-content-center align-items-center custom-dropzone mt-1" v-bind="getRootProps()">
                                <input v-bind="getInputProps()" accept=".csv" />
                                <div v-if="fileCsv !== null" class="text-center">
                                    <p>{{ fileCsv }}</p>
                                </div>
                                <div v-else>
                                    <p>Drag 'n' drop a CSV file here, or click to select the CSV file</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-outline-light" :disabled="form.processing">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>
