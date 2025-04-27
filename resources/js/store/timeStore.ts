import { defineStore } from 'pinia'
import axios from 'axios'

export const useTimeStore = defineStore('time', {
    state: () => ({
        timeItems: [] as string[],
    }),

    actions: {
        async getDefaultTimeItems() {
            try {
                const response = await axios.post('/item/get-default-times')
                this.timeItems = response.data.availableTimes
            } catch (error) {
                console.error('Ошибка при получении времени:', error)
            }
        },
    },
})
