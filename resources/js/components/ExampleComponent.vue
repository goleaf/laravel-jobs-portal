<template>
    <div class="container mx-auto px-4 mx-auto px-4">
        <div class="flex flex-wrap justify-center">
            <div class="flex-1 -md-8">
                <div class="bg-white overflow-hidden shadow rounded -lg">
                    <div class="bg-white overflow-hidden shadow rounded -lg -header">Example Component</div>

                    <div class="bg-white overflow-hidden shadow rounded -lg -body">
                        I'm an example component.
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
