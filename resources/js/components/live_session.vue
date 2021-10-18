<template>
    <div class="row">
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Live Sessions</h3>
                    <div class="card-tools">
                        <button class="btn btn-success" @click="newModal">Create New</button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0">
                    <table class="table ">
                        <thead>
                        <tr>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="session in sessions" :key="session.id">
                            <td class="px-2 border-0">
                                <b-card bg-variant="light" class="shadow rounded border hover mb-0" text-variant="black" :title="session.topic +' - '+ session.group.name">
                                    <b-button v-b-tooltip.hover title="Delete Session" @click="deleteUser(session.id)" class="float-right mx-2" variant="danger" size="sm">
                                        <i class="fas fa-trash text-light"></i>
                                    </b-button>&nbsp; &nbsp;
                                    <b-button v-b-tooltip.hover title="Edit Session" @click="editModal(session)" class="float-right" variant="primary" size="sm">
                                        <i class="fas fa-edit text-light"></i>
                                    </b-button>
                                    <b-card-text >
                                        {{ session.description }}
                                    </b-card-text>
                                    <br>
                                    <b-icon-text-left class="float-right pt-2"> <i class="fas fa-calendar-alt text-primary"></i> {{ session.date | mydate }}&nbsp;
                                        <i class="fas fa-clock text-success"></i> <b-text-card class="text-uppercase"> {{ session.time | time }}</b-text-card></b-icon-text-left>
                                    <b-link :href="session.link" target="blank" class="btn btn-primary  ">Go to Session</b-link>
                                </b-card>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>

        <uploader :options="options" class="uploader-example">
            <uploader-unsupport></uploader-unsupport>
            <uploader-drop>
                <p>Drop files here to upload or</p>
                <uploader-btn>select files</uploader-btn>
                <uploader-btn :attrs="attrs">select images</uploader-btn>
                <uploader-btn :directory="true">select folder</uploader-btn>
            </uploader-drop>
            <uploader-list></uploader-list>
        </uploader>

        <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 v-show="!editMode" class="modal-title" id="exampleModalLongTitle">Create Session</h5>
                        <h5 v-show="editMode" class="modal-title" id="exampleModalLongTitle">Update Session</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="editMode ? updateUser() : createUser()" >
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Select Group</label>
                                <select class="form-control" v-model="form.group_id">
                                    <option v-for="group in groups" :value="group.id" :key="group.id">
                                        {{ group.name }}
                                    </option>
                                </select>
                                <has-error :form="form" field="name"></has-error>
                            </div>
                            <div class="form-group">
                                <label>Select Date</label>
                                <b-form-datepicker id="example-datepicker" v-model="form.date" class="mb-2"></b-form-datepicker>
                            </div>
                            <div class="form-group">
                                <label>Select Time</label>
<!--                                <b-time v-model="form.time" locale="en" @context="onContext"></b-time>-->
                                <b-time v-model="form.time" locale="en"></b-time>
                            </div>
                            <div class="form-group">
                                <label>Topic</label>
                                <input v-model="form.topic" type="text" name="topic"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('topic') }">
                                <has-error :form="form" field="name"></has-error>
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <br>
                                <textarea v-model="form.description" placeholder="Description (Optiional)" class="w-100 form-control"></textarea>
                                <has-error :form="form" field="name"></has-error>
                            </div>
                            <div class="form-group">
                                <label>Link</label>
                                <b-input-group size="md">
                                    <b-input-group-prepend is-text>
                                        <i class="fa fa-link"></i>
                                    </b-input-group-prepend>
                                    <b-form-input v-model="form.link"></b-form-input>
                                </b-input-group>
                                <has-error :form="form" field="email"></has-error>
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
                group_id : 1,
                topic : '',
                description : '',
                link : '',
                date : '',
                time : '',
            }),
            selected: 'Immigrant',
            input: '',
            sessions : {},
            groups: {
                // 1: {id: 1, val: 'item1'},
                // 2: {id: 2, val: 'item2'},
                // 3: {id: 3, val: 'item3'},
            },
            options: {
                // https://github.com/simple-uploader/Uploader/tree/develop/samples/Node.js
                target: '//127.0.0.1:8000/api/conversation/sendFile',
                testChunks: false,
                query: { upload_token: 'my_token' }

            },
            attrs: {
                accept: 'image/*',
            }
        }
    },
    methods:{
        onChange(e) {
            this.file = e.target.files[0];
        },

        updateUser(){
            this.form.post('/api/session/update')
                .then(() => {
                    Fire.$emit('Afteredited');
                    $('#addNew').modal('hide');
                    toast.fire({
                        type: 'success',
                        title: 'Session updated successfully!'
                    });
                })
                .catch(() => {
                    this.$Progress.fail();
                });
        },

        editModal(session){
            this.editMode = true;
            this.form.reset();
            $('#addNew').modal('show');
            this.form.fill(session);
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
                    this.form.get('api/session/destroy/'+id)
                        .then(() => {
                            swal.fire(
                                'Deleted!',
                                'Session delete successfully!',
                                'success'
                            )
                            Fire.$emit('Afterdelete');
                        })
                        .catch(() => {

                        })
                }
            })
        },

        loadSessions(){
            axios.get('api/sessions')
                .then(({data}) => (this.sessions = data.data));

            axios.get('api/session/groups')
                .then(({data}) => (this.groups = data.data));
        },

        createUser(){
            this.$Progress.start();
            this.form.post('api/session/create')
                .then(() => {
                    Fire.$emit('AfterCreated');
                    $('#addNew').modal('hide');
                    toast.fire({
                        type: 'success',
                        title: 'Session created successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });

        }
    },

    created(){
        this.loadSessions();
        Fire.$on('AfterCreated',() => this.loadSessions());
        Fire.$on('Afterdelete',() => this.loadSessions());
        Fire.$on('Afteredited',() => this.loadSessions());
        // setInterval( () => this.loadSessions(),3000);
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
.uploader-example {
    width: 880px;
    padding: 15px;
    margin: 40px auto 0;
    font-size: 12px;
    box-shadow: 0 0 10px rgba(0, 0, 0, .4);
}
.uploader-example .uploader-btn {
    margin-right: 4px;
}
.uploader-example .uploader-list {
    max-height: 440px;
    overflow: auto;
    overflow-x: hidden;
    overflow-y: auto;
}
</style>
