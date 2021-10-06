<template >
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card my-4">
                    <div class="card-header">My Profile</div>
                    <form @submit.prevent="updateUser()" >
                    <div class="card-body">

                            <div class="form-group">
                                <label>Name</label>
                                <input v-model="form.name" type="text" name="name"
                                       class="form-control" >

                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input v-model="form.email" type="text" name="email"
                                       class="form-control" >

                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <input v-model="user.phone" type="number" name="phone"
                                       class="form-control" >
                            </div>

                            <div class="form-group">
                                <label>Password</label>
                                <input v-model="user.password" type="password" name="password"
                                       class="form-control" >

                            </div>

                            <button type="submit" class="btn btn-success float-right">Update</button>

                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Profile",
        data(){
            return{
                editMode : false,
                users : null,
                form : new Form({
                    id : '',
                    name : '',
                    email : '',
                    phone : '',
                    password : '',
                })
            }
        },
        props:[
            'user'
        ],
        methods:{
            loadUsers(){
                axios.get('api/profile')
                    .then((data) => (console.log(this.user)));

            },
            updateUser(){
                this.form.put('api/profiles/'+this.form.id)
                    .then(() => {
                        this.form.reset();
                        this.$forceUpdate();
                        this.form.fill(this.user);
                        toast.fire({
                            type: 'success',
                            title: 'User Updated successfully'
                        });
                    })
                    .catch(() => {
                        console.log("Error!");
                    });
            },
        },
        mounted(){
            this.form.reset();
            this.$forceUpdate();
            this.form.fill(this.user);
        },
    }
</script>

<style scoped>

</style>
