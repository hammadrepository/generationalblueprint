<template>
    <div>
        <group-chat :allgroups="groups" :group="groups[0]" :key="groups[0].id" :user="user"></group-chat>
    </div>

</template>

<script>
    export default {
        props: ['initialGroups', 'currentUser'],

        data() {
            return {
                groups: [],
                user:{}
            }
        },

        mounted() {
            this.groups = this.initialGroups;
            this.user = this.currentUser;

            Fire.$on('groupCreated', (group) => {
                this.groups.push(group);
            });

            this.listenForNewGroups();
        },

        methods: {
            listenForNewGroups() {
                Echo.private('users.' + this.user.id)
                    .listen('GroupCreated', (e) => {
                        this.groups.push(e.group);
                    });
            }
        }
    }
</script>
