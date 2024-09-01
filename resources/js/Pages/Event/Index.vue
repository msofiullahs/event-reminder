<script setup>
import { ref, nextTick, defineModel } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import moment from 'moment-timezone';
import FormEvent from './FormEvent.vue';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faMagnifyingGlassArrowRight } from '@fortawesome/free-solid-svg-icons';
import { faPenToSquare, faTrashCan, faEye } from '@fortawesome/free-regular-svg-icons';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

library.add(
    faMagnifyingGlassArrowRight,
    faPenToSquare,
    faTrashCan,
    faEye
)

const props = defineProps({
    events: Object,
});

const currTime = moment();
const formatDt = 'yyyy-MM-dd HH:mm:ss';

let search = ref();
let startDate = ref();
let endDate = ref();

let formModal = ref(null);
const formEvent = (itemId) => {
    if (itemId !== 0) {
        axios.get(route('edit', {id: itemId}))
            .then(resp => {
                formModal.value.showModal(resp.data);
            });
    } else {
        formModal.value.showModal();
    }
}

const renderComponent = ref(true);
const reloadTable = async () => {
    renderComponent.value = false;
    await nextTick();
    renderComponent.value = true;
}

const deleteItem = (id) => {
    router.delete(route('destroy', {id: id}), {preserveScroll: true});
}
</script>

<template>
    <AppLayout title="Dashboard">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Event Reminders</h1>
            <button type="button" class="btn btn-outline-light" @click="formEvent(0)">Add</button>
        </div>

        <div class="d-flex justify-content-between mb-3">
            <input type="text" class="form-control" placeholder="Search event ID or title" aria-label="Search Keyword" v-model="search">
            <VueDatePicker
                id="startDate"
                v-model="startDate"
                :format="formatDt"
                dark
                auto-apply
                input-class-name="form-control"
                placeholder="Start date for event date/time"
                class="ms-3"
                required
                :timezone="$inertia.page.props.timezone"
                :readonly="isReadOnly">
            </VueDatePicker>
            <VueDatePicker
                id="endDate"
                v-model="endDate"
                :format="formatDt"
                dark
                auto-apply
                input-class-name="form-control"
                placeholder="End date for event date/time"
                class="ms-3"
                required
                :timezone="$inertia.page.props.timezone"
                :readonly="isReadOnly">
            </VueDatePicker>
            <Link :href="route('home')" :data="{ search: search, startDate: startDate, endDate: endDate }" preserve-state class="btn btn-outline-secondary ms-3">
                <font-awesome-icon :icon="['fas', 'magnifying-glass-arrow-right']" />
            </Link>
        </div>

        <div class="row">
            <div class="col">
                <div v-if="events.data.length > 0" class="table-responsive">
                    <table class="table table-dark table-stripped">
                        <thead>
                            <tr>
                                <!-- <th>Select</th> -->
                                <th>ID</th>
                                <th>Event</th>
                                <th>Date/Time</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Location</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in events.data" :key="item.id">
                                <!-- <td><input type="checkbox" class="form-control"/></td> -->
                                <td>{{ item.unique_id }}</td>
                                <td>{{ item.title }}</td>
                                <td>{{ moment.tz(item.event_date, $inertia.page.props.timezone).format('DD/MM/YYYY HH:mm:ss') }}</td>
                                <td>{{ item.type }}</td>
                                <td>{{ moment().isAfter(item.event_date) ? 'Completed' : 'Upcoming' }}</td>
                                <td>{{ item.location }}</td>
                                <td class="text-center">
                                    <template v-if="moment().isBefore(item.event_date)">
                                        <button v-if="moment().isBefore(item.event_date)" type="button" class="btn btn-sm p-0" style="line-height: 1;" @click="formEvent(item.id)">
                                            <font-awesome-icon :icon="['far', 'pen-to-square']" />
                                        </button>
                                        <span class="text-secondary mx-1">&#9475;</span>
                                    </template>
                                    <button type="button" class="btn btn-sm p-0" style="line-height: 1;" @click="deleteItem(item.id)">
                                        <font-awesome-icon :icon="['far', 'trash-can']" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center" v-else>
                    <h2 class="text-center text-mute">No Available Events</h2>
                </div>
            </div>
        </div>

        <Pagination v-if="events.data.length > 0" class="mt-5" :links="events.links" />
        <FormEvent ref="formModal" @posted="reloadTable"></FormEvent>
    </AppLayout>
</template>
