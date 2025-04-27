export default {
    getAuthHeaders() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        return {
            'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken,
        }
    }
}
