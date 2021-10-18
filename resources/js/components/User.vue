<template>
    <div class="row">
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Users</h3>

                    <div class="card-tools">
                        <button class="btn btn-success" @click="newModal">Create New</button>

                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="user in users" :key="user.id">
                            <td>{{user.id}}</td>
                            <td>{{user.name}}</td>
                            <td>{{user.email}}</td>
                            <td><span class="tag tag-success">{{user.phone | capitalize}}</span></td>
                            <td><span class="tag tag-success">{{user.created_at | mydate}}</span></td>
                            <td>
<!--                                <button @click="changeSignature(user)" class="btn btn-primary">Change eSignature</button>-->
<!--                                <button @click="emailSignature(user)" class="btn btn-success">Email <div v-bind:id="'spinner' +user.id" class="spinner-border spinner-border-sm  d-none" role="status">-->
<!--                                </div></button>-->
                               <button @click="editModal(user)" class="btn btn-primary">Edit</button>
                               <button @click="deleteUser(user.id)" class="btn btn-danger">Delete</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>


        <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 v-show="!editMode" class="modal-title" id="exampleModalLongTitle">Add New</h5>
                        <h5 v-show="editMode" class="modal-title" id="exampleModalLongTitle">Update User</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="editMode ? updateUser() : createUser()" >
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input v-model="form.name" type="text" name="name"
                                   class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                            <has-error :form="form" field="name"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input v-model="form.email" type="text" name="email"
                                   class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                            <has-error :form="form" field="email"></has-error>
                        </div>

                        <div class="form-group">
                            <label>Phone</label>
                            <input v-model="form.phone" type="number" name="phone"
                                      class="form-control" :class="{ 'is-invalid': form.errors.has('phone') }">
                            <has-error :form="form" field="phone"></has-error>
                        </div>

                        <div class="form-group">
                            <label>Password</label>
                            <input v-model="form.password" type="password" name="password"
                                   class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                            <has-error :form="form" field="password"></has-error>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button v-show="editMode" type="submit" class="btn btn-success">Update</button>
                        <button v-show="!editMode" type="submit" class="btn btn-primary">Create</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="changeSignature" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5  class="modal-title" id="exampleModalLongTitle">Upload Email Signature</h5>
                    </div>
                                <div class="card">
<!--                                    <div class="card-header">Upload Image</div>-->

                                    <div class="card-body">

                                        <div v-if="success != ''" class="alert alert-success">
                                            {{success}}
                                        </div>

                                        <form @submit="formSubmit" enctype="multipart/form-data">
                                            <label class="text-grey text-sm" for="signatureImage">Supported formats:jpg, jpeg, png, pdf</label>
                                            <input id="signatureImage" ref="signatureImage" type="file" class="form-control" v-on:change="onChange">
                                            <br>
                                            <button class="btn btn-primary btn-block">Upload </button>
                                        </form>
                                    </div>
                                </div>
                </div>
            </div>
        </div>


    </div>
</template>

<script>
    export default {
        name: "User",
        data(){
            return{
                editMode : false,
                users : {},
                form : new Form({
                    id : '',
                    name : '',
                    email : '',
                    phone : '',
                    password : '',
                }),
                name: '',
                file: '',
                success: '',
                user: ''
            }
        },
        methods:{
            onChange(e) {
                this.file = e.target.files[0];
            },
            formSubmit(e) {
                e.preventDefault();
                let existingObj = this;

                const config = {
                    headers: {
                        'content-type': 'multipart/form-data'
                    }
                }

                let data = new FormData();
                data.append('file', this.file);
                data.append('user_id', this.user.id);

                axios.post('/upload', data, config)
                    .then(function (res) {
                        toast.fire({
                            type: 'success',
                            title: 'File uploaded successfully!'
                        });

                        $('#changeSignature').modal('hide');
                    })
                    .catch(function (err) {
                        toast.fire({
                            type: 'error',
                            title: 'File failed to upload!'
                        });
                        existingObj.output = err;
                        this.$refs.signatureImage.value=null;
                    });
                this.$refs.signatureImage.value=null;


            },
            emailSignature(user){
                let existingObj = this;
                this.user = user;
                let userId = $('#spinner'+this.user.id);
                userId.removeClass('d-none');

                axios.get('/emailToUser/'+this.user.id)
                    .then(function (res) {
                        existingObj.success = res.data.success;
                        userId.addClass('d-none');
                        toast.fire({
                            type: 'success',
                            title: 'Email sent successfully!'
                        });
                    })
                    .catch(function (err) {
                        existingObj.output = err;
                        userId.addClass('d-none');
                        toast.fire({
                            type: 'error',
                            title: 'Something went wrong!'
                        });
                    });

            },

            updateUser(){
                this.form.put('api/user/'+this.form.id)
                    .then(() => {
                        Fire.$emit('Afteredited');
                        $('#addNew').modal('hide');
                        toast.fire({
                            type: 'success',
                            title: 'User Updated successfully'
                        });
                    })
                    .catch(() => {
                        this.$Progress.fail();
                    });
            },

            editModal(user){
                this.editMode = true;
                this.form.reset();
                $('#addNew').modal('show');
                this.form.fill(user);
            },
            changeSignature(user){
                this.user = user;
                $('#changeSignature').modal('show');
            },
            newModal(){
                this.editMode = false;
                this.form.reset();
                $('#addNew').modal('show');
            },

            deleteUser(id){
                swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                    this.form.delete('api/user/'+id)
                        .then(() => {

                                swal.fire(
                                    'Deleted!',
                                    'Your file has been deleted.',
                                    'success'
                                )

                            Fire.$emit('Afterdelete');
                        })
                        .catch(() => {

                        })
                    }
                })
            },


            loadUsers(){
                axios.get('api/user')
                    .then(({data}) => (this.users = data.data))
            },

            createUser(){
                this.$Progress.start();
                this.form.post('api/user')
                    .then(() => {
                        Fire.$emit('AfterCreated');
                        $('#addNew').modal('hide');
                        toast.fire({
                            type: 'success',
                            title: 'User created successfully'
                        });

                        this.$Progress.finish();
                    })
                    .catch(() => {

                    });

            }
        },

        created(){
            this.loadUsers();
            Fire.$on('AfterCreated',() => this.loadUsers());
            Fire.$on('Afterdelete',() => this.loadUsers());
            Fire.$on('Afteredited',() => this.loadUsers());
            // setInterval( () => this.loadUsers(),3000);
        },

        filters: {
            capitalize: function (value) {
                if (!value) return ''
                value = value.toString()
                return value.charAt(0).toUpperCase() + value.slice(1)
            },
        }
    }
</script>

<style scoped>

</style>
