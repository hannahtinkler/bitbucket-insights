<template>
    <div>
        <p class="mx-3">
            This data is refreshed every 10 minutes.
            <span v-if="disabled">The data is currently refreshing; this may take a few minutes.</span>
            <span v-else>The last refresh was at {{ refreshedTime }}.</span>
        </p>

        <button class="btn btn-lg btn-primary mt-3 mx-3" href="#" role="button" @click="refreshData" :disabled="disabled">
            {{ disabled ? 'Refreshing...' : text }}
        </button>
    </div>
</template>

<script>
    export default {
        props: {
            text: {
                default: 'Force refresh data',
            },
            refreshing: {
                default: false,
            },
            lastRefreshed: {
                required: true,
            }
        },

        data() {
            return {
                disabled: this.refreshing,
                refreshedTime: this.lastRefreshed,
            }
        },

        mounted() {
            setInterval(this.watchSettings, 5000);
        },

        methods: {
            refreshData() {
                axios.get('/ajax/refresh');
            },

            watchSettings() {
                axios.get('/ajax/settings')
                    .then(({ data }) => {
                        this.disabled = data.currently_refreshing;
                        this.refreshedTime = data.last_refresh;
                    });
            },
        },
    }
</script>
