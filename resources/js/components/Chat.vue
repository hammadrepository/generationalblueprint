<template>
    <div>
        <div class="container">
        <div class="row">
            <div class="col-12">
                <groups v-if="loaded" :initial-groups="this.groups" :currentUser="this.user"></groups>
            </div>
        </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            groups: [],
            user:{},
            loaded : false
        }
    },

    mounted() {
        // this.groups = this.initialGroups;
        //
        // Fire.$on('groupCreated', (group) => {
        //     this.groups.push(group);
        // });
        this.loadGroups();
        this.listenForNewGroups();
    },

    methods: {
        listenForNewGroups() {
            Echo.private('users.' + this.user.id)
                .listen('GroupCreated', (e) => {
                    this.groups.push(e.group);
                });
        },

        loadGroups(){
            axios.get('/groupsList')
                .then((response) =>
                    {
                        this.groups =  response.data.groups,
                        this.user = response.data.user
                        this.loaded = true;
                    }
                );
        }
    }
}
</script>
