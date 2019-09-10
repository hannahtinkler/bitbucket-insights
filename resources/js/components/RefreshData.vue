<template>
    <div>
        <p class="mx-3">
            This data is refreshed every minute or so and the last refresh was at {{ refreshedTime }}.
            <span v-if="disabled">The data is currently refreshing.</span>
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
            this.disabled = this.refreshing ? true : false;
        },

        methods: {
            refreshData() {
                axios.get('/ajax/refresh');
            },

            watchSettings() {
                axios.get('/ajax/settings')
                    .then(({ data }) => {
                        if (!data.currently_refreshing && data.last_refresh != this.refreshedTime) {
                            window.location.reload();
                        }

                        return data;
                    })
                    .then(data => {
                        this.disabled = data.currently_refreshing;
                        this.refreshedTime = data.last_refresh;
                    });
            },
        },
    }
</script>
