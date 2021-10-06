<template>
    <div>
        <div class="row gutters">

            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

                <div class="card m-0">

                    <!-- Row start -->
                    <div class="row no-gutters">
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-3 col-3">
                            <div class="users-container bg-light">
                                <ul class="users">
                                    <li v-for="group1 in groups"   :data-chat="'group' + group.id" @click="loadChat(group1.id)">
                                        <div :class="{highlight:selected == group1.id}" class="person">
                                        <div class="user">
                                            <img alt="Retail Admin" src="https://www.bootdey.com/img/Content/avatar/avatar3.png">
                                            <span class="status busy"></span>
                                        </div>
                                        <p class="name-time">
                                            <span class="name">{{ group1.name }}</span>
                                            <span class="time">15/02/2010</span>
                                        </p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-xl-8 col-lg-8 col-md-8 col-sm-9 col-9">
                            <div class="chat-container">
                                <v-overlay v-if="overlay"  :value="overlay">
                                    <v-progress-circular
                                        indeterminate
                                        size="64"
                                    ></v-progress-circular>
                                </v-overlay>
                                <div class="selected-user">
                                    <h5 class="name">{{ groupName }}</h5>
                                  <span v-show="isLoading" class=" is-overlay">
                                    <i class="fa fa-spinner fa-spin fa-2x fa-fw float-right"></i>
                                  </span>
                                </div>
<!--                                        <span v-show="isLoading" class="loading-overlay is-overlay">-->
<!--                                        <i class="fa fa-cog fa-spin fa-3x fa-fw"></i>-->
<!--                            </span>-->
                                <ul :id="'panel-body-' + group.id" class="chat-box chatContainerScroll">
                                    <li class="text-center">
                                        <button v-if="this.hasMore" class="btn btn-link" type="button" @click="loadMore()">Load more...</button>
                                    </li>
                                    <li v-for="conversation in conversations" v-if="isLoaded"  :class="getClass(conversation.user.id)">
                                        <div class="chat-avatar">
                                            <div v-show="getClass(conversation.user.id) == 'chat-left'">
                                            <img alt="Retail Admin" src="https://www.bootdey.com/img/Content/avatar/avatar3.png">
                                                <div @click="loadUser(conversation.user.id)" class="chat-name link-info">{{ conversation.user.name }}</div>
                                            </div>
                                        </div>
                                        <div v-show="getClass(conversation.user.id) == 'chat-right'" class="chat-hour">{{ conversation.created_at.split(' ')[1]}} <span class="fa fa-check-circle"></span></div>
                                        <div class="chat-text" v-bind:style="getClass(conversation.user.id) == 'chat-right' ? 'margin-right: 1rem;' : ''" >
                                            <span v-if="conversation.type == 'media'"><img :src="getIcon(conversation.message.split('.').pop())"/><a :href="conversation.message" target="_blank">{{conversation.message.slice(38,44)}}...{{ conversation.message.split(".").pop()}}</a></span> <span v-else>{{ conversation.message }}</span>
                                        </div>
                                        <div v-show="getClass(conversation.user.id) != 'chat-right'" class="chat-hour">{{ conversation.created_at.split(' ')[1]}} <span class="fa fa-check-circle"></span></div>
                                    </li>
                                </ul>
                                <div class="form-group mt-3 mb-0">
                                    <div class="input-group">
                                        <input id="btn-input" v-model="message" autofocus class="form-control input-sm" placeholder="Type your message here..." type="text" @keyup.enter="store()" />

                                        <div class='file file--upload'>
                                            <label for='input-file'>
                                                <i class="fas fa-file-upload"></i>
                                            </label>
                                            <input id='input-file' type='file' @change="sendMediaMessage" />
                                        </div>
                                        <span class="input-group-btn">
                            <button id="btn-chat" class="btn btn-warning btn-md" @click.prevent="store()">
                                Send  <span v-if="loading" aria-hidden="true" class="spinner-border spinner-border-sm" role="status"></span></button>
                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Row end -->
                </div>
            </div>
        </div>
        <div class="modal fade" tabindex="-1" id="addNew" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">

                        <h5 class="modal-title" id="exampleModalLongTitle">User Profile</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Name</label>
                                <input v-model="form.name" readonly type="text" name="name"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                <has-error :form="form" field="name"></has-error>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input v-model="form.email" readonly type="text" name="email"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                                <has-error :form="form" field="email"></has-error>
                            </div>

                            <div class="form-group">
                                <label>Phone</label>
                                <input v-model="form.phone" readonly type="number" name="phone"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('phone') }">
                                <has-error :form="form" field="phone"></has-error>
                            </div>

                            <div class="form-group">
                                <label>Password</label>
                                <input v-model="form.password" readonly type="password" name="password"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                                <has-error :form="form" field="password"></has-error>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button v-show="isAdmin" type="submit" class="btn btn-danger">Ban User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


</template>

<script>
    export default {
        props: ['allgroups','group','user'],

        data() {
            return {
                conversations: [],
                message: '',
                group_id: this.group.id,
                loading : false,
                next_page_url:'',
                hasMore : false,
                overlay: false,
                isLoaded : false,
                isLoading: false,
                btnLoading: false,
                selected: undefined,
                loader: null,
                loading3: false,
                user1:{},
                groups:[],
                groupName:'',
                isAdmin: false,
                form : new Form({
                    name : '',
                    email : '',
                    phone : '',
                }),
            }
        },

        mounted() {
            this.user1 = this.user;
            this.groups = this.allgroups;
            this.loadChat(this.allgroups[0].id);
            this.listenForNewMessage();
        },
        watch: {
            loader () {
                const l = this.loader
                this[l] = !this[l]

                setTimeout(() => (this[l] = false), 3000)

                this.loader = null
            },
        },

        methods: {
            getIcon(extension){
                const icon = [];
                icon["png"] = "https://img.icons8.com/ios-filled/50/000000/png.png";
                icon["PNG"] = "https://img.icons8.com/ios-filled/50/000000/png.png";
                icon["pdf"] = "https://img.icons8.com/ios/50/000000/pdf--v1.png";
                icon["PDF"] = "https://img.icons8.com/ios/50/000000/pdf--v1.png";
                icon["jpeg"] = "https://img.icons8.com/ios-filled/50/000000/jpeg.png";
                icon["JPEG"] = "https://img.icons8.com/ios-filled/50/000000/jpeg.png";
                icon["jpg"] = "https://img.icons8.com/ios-filled/50/000000/jpg.png";
                icon["JPG"] = "https://img.icons8.com/ios-filled/50/000000/jpg.png";
                return icon[extension];
            },
            getClass(id){
                if(this.user1.id == id){
                    return 'chat-right';
                }
                return 'chat-left';
            },
            store() {
                this.loading = true;
                axios.post('/conversations', {message: this.message, group_id: this.group.id})
                .then((response) => {
                    this.message = '';
                    // this.conversations.push(response.data);
                    this.loading = false;
                    // this.scrollToEnd();
                })
                .catch((error) => {
                    this.message = '';
                    this.loading = false;
                    // this.conversations.push(response.data);
                    this.scrollToEnd();
                });

            },
            sendMediaMessage({ target }) {
                this.overlay = true;
                const config = {
                    headers: {
                        'content-type': 'multipart/form-data'
                    }
                }
                let data = new FormData();
                data.append('file', target.files[0]);
                data.append('group_id', this.group.id);
                data.append('type', 'media');

                axios.post('/upload', data, config)
                    .then((res)=> {
                        toast.fire({
                            type: 'success',
                            title: 'File Uploaded Successfully!'
                        });
                        this.overlay = false;
                    })
                    .catch((err) => {
                        toast.fire({
                            type: 'error',
                            title: err.message
                        });
                        this.overlay = false;
                    });

            },
            scrollToEnd: function() {
                var container = this.$el.querySelector("#panel-body-"+this.group.id);
                container.scrollTop = container.scrollHeight;
            },
            loadMore(){
                this.isLoading = true;

                if(this.next_page_url !== null){

                axios.get(this.next_page_url)
                    .then((response) => {
                        if(response.data.next_page_url != null){
                            this.hasMore = true;
                        }else{
                            this.hasMore = false;
                        }
                        for (var i=0; i<response.data.data.length; i++)
                        {
                            this.conversations.unshift(response.data.data[i]);
                        }
                        this.next_page_url = response.data.next_page_url;
                        this.isLoading = false;
                    });

                }
            },
            loadChat(id){
                this.isLoading = true;
              axios.get('/chat/'+id )
              .then((response) => {
                 this.conversations = (response.data.data.reverse());
                 this.next_page_url = response.data.next_page_url;
                 this.next_page_url != null ? this.hasMore = true: this.hasMore = false;
                  this.groupName = response.data.data[0].group.name;

                  this.isLoading = false;
                  this.selected = id;
              });

              this.isLoaded = true;
            },
            loadUser(id){
                this.isLoading = true;
                axios.get('/api/user/'+id )
                    .then((response) => {
                        this.form.fill(response.data.user);
                        this.isLoading = false;
                        if(this.$userId == 1 ){this.isAdmin =true };
                        $('#addNew').modal('show');
                    });
            },
            listenForNewMessage() {

                Echo.private('groups.' + this.group.id)
                    .listen('NewMessage', (e) => {
                        // toast.fire({
                        //     type: 'success',
                        //     title: e.message
                        // });
                        this.conversations.push(e);
                        setTimeout(()=>{
                            console.log('scrolling');
                            this.scrollToEnd();
                        },100);
                    });

            }
        }
    }
</script>
<style scope>

</style>
