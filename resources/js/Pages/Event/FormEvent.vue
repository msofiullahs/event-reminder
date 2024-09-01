<script setup>
import { onMounted, reactive, ref, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { Modal } from 'bootstrap';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import axios from 'axios';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

let modalEle = ref(null);
let thisModalObj = null;

const emit = defineEmits(['posted']);

const form = useForm({
    title: null,
    type: null,
    location: null,
    event_date: null,
    send_reminder_time: null,
    receivers: null,
});

const formatDt = 'yyyy-MM-dd HH:mm:ss'
const onCheck = ref(false);

onMounted(() => {
    thisModalObj = new Modal(modalEle.value);
});

let modalTitle = 'Add Event Reminder';
let eventId = 0;

const showModal = (eventData) => {
    if (eventData !== undefined) {
        modalTitle = 'Edit Event Reminder';
        eventId = eventData.id;
        form.title = eventData.title;
        form.type = eventData.type;
        form.location = eventData.location;
        form.event_date = eventData.event_date;
        form.send_reminder_time = eventData.send_reminder_time;
        const receivers = [];
        form.receivers = null;
        if (eventData.receivers.length > 0) {
            eventData.receivers.forEach(val => {
                receivers.push(val.email);
            });
            form.receivers = receivers.toString();
        }
    }
    thisModalObj.show();
}

const closeModal = () => {
    eventId = 0;
    form.reset();
    onCheck.value = false;
    thisModalObj.hide();
}

defineExpose({ showModal: showModal });

const submitForm = () => {
    if (eventId !== 0 ) {
        router.post(route('update', {id: eventId}), {
            _method: 'put',
            forceFormData: true,
            title: form.title,
            type: form.type,
            location: form.location,
            event_date: form.event_date,
            send_reminder_time: form.send_reminder_time,
            receivers: form.receivers,
        }, {
            onSuccess: () => {
                form.reset();
                postedData();
            }
        })
    } else {
        form.post(route('store'), {
            onSuccess: () => {
                form.reset();
                postedData();
            }
        })
    }
}

const postedData = () => {
    emit('posted', true);
    closeModal();
}
</script>

<template>
    <form @submit.prevent="submitForm">
        <div class="modal fade" tabindex="-1" aria-hidden="true" ref="modalEle">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ modalTitle }}</h5>
                        <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <InputLabel for="title" value="Title" />
                            <TextInput
                                id="title"
                                v-model="form.title"
                                class="mt-1 form-control"
                                type="text"
                                required
                            />
                            <InputError class="mt-1" :message="form.errors.title" />
                        </div>
                        <div class="mb-3">
                            <InputLabel for="type" value="Type" />
                            <div class="row mt-1">
                                <div class="col">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" v-model="form.type" id="offline" value="offline" required>
                                        <label class="form-check-label" for="offline">
                                            Offline
                                        </label>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" v-model="form.type" id="online" value="online">
                                        <label class="form-check-label" for="online">
                                            Online
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <InputError class="mt-1" :message="form.errors.type" />
                        </div>
                        <div class="mb-3">
                            <InputLabel for="location" value="Location" />
                            <textarea
                                id="location"
                                v-model="form.location"
                                class="mt-1 form-control"
                                required
                            ></textarea>
                            <InputError class="mt-1" :message="form.errors.location" />
                        </div>
                        <div class="mb-3">
                            <InputLabel for="eventDate" value="Event Date/Time" />
                            <VueDatePicker
                                id="eventDate"
                                v-model="form.event_date"
                                :format="formatDt"
                                dark
                                auto-apply
                                input-class-name="mt-1 form-control"
                                required
                                :timezone="$inertia.page.props.timezone"
                                :readonly="isReadOnly">
                            </VueDatePicker>
                            <InputError class="mt-1" :message="form.errors.event_date" />
                        </div>
                        <div class="mb-3">
                            <InputLabel for="reminderTime" value="Start Send Reminder Date/Time" />
                            <VueDatePicker
                                id="reminderTime"
                                v-model="form.send_reminder_time"
                                :format="formatDt"
                                dark
                                auto-apply
                                input-class-name="mt-1 form-control"
                                required
                                :timezone="$inertia.page.props.timezone"
                                :readonly="isReadOnly">
                            </VueDatePicker>
                            <small class="text-warning">Reminder email will be sent on above selected time.</small>
                            <InputError class="mt-1" :message="form.errors.send_reminder_time" />
                        </div>
                        <div class="mb-3">
                            <InputLabel for="receivers" value="Receiver Emails (separate by comma)" />
                            <textarea
                                id="receivers"
                                v-model="form.receivers"
                                class="mt-1 form-control"
                                required
                            ></textarea>
                            <InputError class="mt-1" :message="form.errors.receivers" />
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
