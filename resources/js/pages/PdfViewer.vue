<template>
    <div class="pdf-container">
        <!-- Loading state -->
        <div v-if="loading" class="pdf-status">Loading PDF...</div>

        <!-- Error state -->
        <div v-if="error" class="pdf-error">
            ❌ {{ error }}
        </div>

        <!-- Canvas -->
        <canvas ref="canvas" v-show="!loading && !error"></canvas>

        <!-- Controls -->
        <div class="pdf-controls" v-if="numPages > 1">
            <button @click="prevPage" :disabled="pageNumber === 1">Назад</button>
            <span>{{ pageNumber }} / {{ numPages }}</span>
            <button @click="nextPage" :disabled="pageNumber === numPages">Вперед</button>
        </div>
    </div>
</template>

<script>
import * as pdfjsLib from 'pdfjs-dist/build/pdf';

pdfjsLib.GlobalWorkerOptions.workerSrc = new URL(
    'pdfjs-dist/build/pdf.worker.min.mjs',
    import.meta.url
).toString();

export default {
    props: {
        pdfUrl: { type: String, required: true }
    },
    data() {
        return {
            pdf: null,
            pageNumber: 1,
            numPages: 0,
            scale: 1.0,
            loading: false,
            error: null
        };
    },
    async mounted() {
        if (!this.pdfUrl) return;
        await this.loadPdf();
        window.addEventListener('resize', this.renderPage);
    },
    beforeUnmount() {
        window.removeEventListener('resize', this.renderPage);
        if (this.pdf) this.pdf.destroy();
    },
    watch: {
        pdfUrl(newVal) {
            if (newVal) {
                this.pageNumber = 1;
                this.loadPdf();
            }
        }
    },
    methods: {
        async loadPdf() {
            this.loading = true;
            this.error = null;

            try {
                const loadingTask = pdfjsLib.getDocument({
                    url: this.pdfUrl,
                    withCredentials: false,
                    disableAutoFetch: false,
                    disableStream: false,
                });

                loadingTask.onProgress = (data) => {
                    console.log(`Loading PDF: ${data.loaded} / ${data.total}`);
                };

                this.pdf = await loadingTask.promise;
                this.numPages = this.pdf.numPages;
                console.log(`PDF loaded: ${this.numPages} pages`);
                await this.renderPage();
            } catch (err) {
                console.error('PDF load error:', err);

                if (err.name === 'MissingPDFException') {
                    this.error = 'File not found (404). Check if the URL is correct.';
                } else if (err.name === 'UnexpectedResponseException') {
                    this.error = `Server error: ${err.message}. Possibly CORS or wrong URL.`;
                } else if (err.message?.includes('CORS')) {
                    this.error = 'CORS error: The server must allow cross-origin requests.';
                } else {
                    this.error = `Error: ${err.message || err.name}`;
                }
            } finally {
                this.loading = false;
            }
        },

        async renderPage() {
            if (!this.pdf) return;
            try {
                const page = await this.pdf.getPage(this.pageNumber);
                const containerWidth = this.$el.clientWidth || 800;
                const viewport = page.getViewport({ scale: 1 });
                this.scale = containerWidth / viewport.width;
                const scaledViewport = page.getViewport({ scale: this.scale });

                const canvas = this.$refs.canvas;
                if (!canvas) return;
                const context = canvas.getContext('2d');
                canvas.width = scaledViewport.width;
                canvas.height = scaledViewport.height;

                await page.render({
                    canvasContext: context,
                    viewport: scaledViewport
                }).promise;
            } catch (err) {
                console.error('Render error:', err);
                this.error = `Render error: ${err.message}`;
            }
        },

        async nextPage() {
            if (this.pageNumber < this.numPages) {
                this.pageNumber++;
                await this.renderPage();
            }
        },
        async prevPage() {
            if (this.pageNumber > 1) {
                this.pageNumber--;
                await this.renderPage();
            }
        }
    }
};
</script>

<style scoped>
.pdf-container {
    width: 100%;
    overflow-x: auto;
}
.pdf-status {
    text-align: center;
    padding: 20px;
    color: #666;
}
.pdf-error {
    text-align: center;
    padding: 16px;
    color: #c00;
    background: #fff0f0;
    border: 1px solid #fcc;
    border-radius: 6px;
    margin: 10px 0;
    font-size: 14px;
}
.pdf-controls {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin-top: 10px;
}
canvas {
    display: block;
    margin: 0 auto;
}
</style>
