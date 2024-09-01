<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faEye, faEyeSlash } from '@fortawesome/free-regular-svg-icons';

library.add(
    faEye,
    faEyeSlash,
)

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

let seePassword = ref(false);
const passwordToggle = () => {
    seePassword.value = !seePassword.value;
}
</script>

<template>
    <Head title="Log in" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div class="mb-3 row">
                <InputLabel for="email" value="Email" class="col-sm-3 col-form-label" />
                <div class="col-sm-9">
                    <TextInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="form-control"
                        required
                        autofocus
                        autocomplete="username"
                    />
                    <InputError :message="form.errors.email" />
                </div>
            </div>

            <div class="mb-3 row">
                <InputLabel for="password" value="Password" class="col-sm-3 col-form-label" />
                <div class="col-sm-9">
                    <div class="input-group">
                        <TextInput
                            v-if="seePassword"
                            id="password"
                            v-model="form.password"
                            type="text"
                            class="form-control"
                            required
                            autocomplete="current-password"
                        />
                        <TextInput
                            v-else
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="form-control"
                            required
                            autocomplete="current-password"
                        />
                        <button class="btn btn-outline-secondary" type="button" @click="passwordToggle">
                            <font-awesome-icon :icon="['far', seePassword ? 'eye-slash' : 'eye']" />
                        </button>
                    </div>
                    <InputError :message="form.errors.password" />
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-sm-3"></div>
                <div class="col-sm-9">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" v-model="form.remember" name="remember" id="flexCheckDefault">
                        <label class="form-check-label" for="flexCheckDefault">
                            Remember me
                        </label>
                    </div>
                </div>
                <!-- <label class="flex items-center">
                    <Checkbox v-model:checked="form.remember" name="remember" />
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label> -->
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <Link v-if="canResetPassword" :href="route('password.request')" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Forgot your password?
                </Link>

                <button class="btn btn-outline-light" type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Log in
                </button>
            </div>
        </form>
    </AuthenticationCard>
</template>
