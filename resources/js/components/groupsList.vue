<template>
    <li class="person" data-chat="person1" @click="loadChat(singleGroup.id)">
        <div class="user">
            <img src="https://www.bootdey.com/img/Content/avatar/avatar3.png" alt="Retail Admin">
            <span class="status busy"></span>
        </div>
        <p class="name-time">
            <span class="name">{{ singleGroup.name }}</span>
            <span class="time">15/02/2019</span>
        </p>
    </li>
</template>

<script>
export default {
    props: ['group'],

    data() {
        return {
            singleGroup: {},
        }
    },
    mounted(){
        this.singleGroup = this.group;
    }
}
</script>

<style scoped>

</style>
