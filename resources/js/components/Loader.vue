<template>
    <div class="loader-overlay">
        <div class="spinner"></div>
    </div>
</template>

<script setup>
// ничего не нужно, если логика простая
</script>

<style scoped>
.loader-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(135, 135, 135, 0.7);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.spinner {
    border: 6px solid #ccc;
    border-top: 6px solid #404040;
    border-radius: 50%;
    width: 48px;
    height: 48px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}
</style>
