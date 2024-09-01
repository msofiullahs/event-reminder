<template>
    <div class="authclass d-flex vh-100 justify-content-center align-items-center">
        <div class="card h-auto border-0" style="min-width: 30rem;">
            <div class="text-center mb-3">
                <slot name="logo" />
            </div>

            <div class="p-3 form rounded">
                <slot />
            </div>
        </div>
    </div>
</template>
