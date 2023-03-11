<template>
    <link rel="icon" :href="faviconPath" />
</template>

<script>
export default {
    data() {
        return {
            isDarkMode: window.matchMedia('(prefers-color-scheme: dark)').matches,
            faviconPath: '',
        };
    },
    mounted() {
        this.setFaviconPath();
    },
    methods: {
        setFaviconPath() {
            this.faviconPath = this.isDarkMode ? '/favicon-light.ico' : '/favicon-dark.ico';
        },
    },
    watch: {
        isDarkMode() {
            this.setFaviconPath();
        },
    },
};
</script>
