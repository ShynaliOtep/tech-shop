<template>
    <div class="mb-10">
        <input
            :type="type"
            :placeholder="placeholder"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            class="custom-input mb-10"
            :class="{ 'input-error': error }"
        />

        <small v-if="error" class="input-error-text">
            {{ error }}
        </small>
    </div>
</template>

<script>
export default {
    props: {
        modelValue: String,
        placeholder: String,
        type: {
            type: String,
            default: 'text'
        },
        error: String
    }
}
</script>

<style scoped>

.custom-input {
    width: 100%;
    border: 1px solid #404040;
    border-radius: 15px;
    background-color: #191919;
    height: 60px;
    padding: 10px 14px;
    font-size: 16px;
    font-weight: 400;
    color: #fff;
    outline: none;
}

.custom-input::placeholder {
    color: #404040;
}

.custom-input:focus {
    border-color: #FF962E;
}

.custom-input-error {
    border-color: #ff4d4f;
}

.custom-textarea {
    width: 100%;
    border: 1px solid #404040;
    border-radius: 15px;
    background-color: #191919;
    padding: 14px;
    font-size: 16px;
    color: #fff;
    outline: none;
    resize: none;
    min-height: 100px;
}

</style>
