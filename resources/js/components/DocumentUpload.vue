<template>
    <div>
        <div class="container bg-white py-3 shadow border mt-3">
            <h3>Upload Group Documents</h3>

    <uploader :options="options" class="document-uploaded" @file-error="onFileError" @file-added="FileListChanged">
        <uploader-unsupport></uploader-unsupport>
        <div class="form-group">
            <label>Select Group</label>
            <select class="form-control"  :class="{'is-invalid': this.selected }" v-model="options.query.group_id"  @change="FileListChanged()">
                <option v-for="group in groups" :value="group.id" :key="group.id">
                    {{ group.name }}
                </option>
            </select>
            <!--        <has-error :form="form" field="name"></has-error>-->
        </div>
        <uploader-drop class="text-center p-4">
            <p>Drop files here to upload or</p>
            <uploader-btn><i class="text-primary fas fa-cloud-upload-alt"></i>  Select files</uploader-btn>
<!--            <uploader-btn :attrs="attrs">Select images</uploader-btn>-->
            <uploader-btn :directory="true"><i class="text-warning fas fa-upload"></i>  Select folder</uploader-btn>
        </uploader-drop>
        <uploader-list ref="files" ></uploader-list>
    </uploader>
        </div>
        <b-container class="bg-white shadow border mt-3">
            <!-- User Interface controls -->
            <b-row>
                <b-col lg="3" class="my-1">
                    <b-form-group
                        label="Sort"
                        label-for="sort-by-select"
                        label-cols-sm="2"
                        label-align-sm="right"
                        label-size="sm"
                        class="mb-0"
                        label-class="pt-3"
                        v-slot="{ ariaDescribedby }"
                    >
                        <b-input-group size="sm">
                            <b-form-select
                                id="sort-by-select"
                                v-model="sortBy"
                                :options="sortOptions"
                                :aria-describedby="ariaDescribedby"
                                class="w-75"
                            >
                                <template #first>
                                    <option value="">-- none --</option>
                                </template>
                            </b-form-select>

                            <b-form-select
                                v-model="sortDesc"
                                :disabled="!sortBy"
                                :aria-describedby="ariaDescribedby"
                                size="sm"
                                class="w-25"
                            >
                                <option :value="false">Asc</option>
                                <option :value="true">Desc</option>
                            </b-form-select>
                        </b-input-group>
                    </b-form-group>
                </b-col>

                <b-col lg="6" class="my-1">
                    <b-form-group
                        label="Filter"
                        label-for="filter-input"
                        label-cols-sm="3"
                        label-align-sm="right"
                        label-size="sm"
                        label-class="pt-3"
                        class="mb-0"
                    >
                        <b-input-group size="sm">
                            <b-form-input
                                id="filter-input"
                                v-model="filter"
                                type="search"
                                placeholder="Type to Search by title or group"
                            ></b-form-input>

                            <b-input-group-append>
                                <b-button :disabled="!filter" @click="filter = ''">Clear</b-button>
                            </b-input-group-append>
                        </b-input-group>
                    </b-form-group>
                </b-col>


                <b-col sm="5" md="3" class="my-1">
                    <b-form-group
                        label="Per page"
                        label-for="per-page-select"
                        label-cols-sm="6"
                        label-cols-md="4"
                        label-cols-lg="3"
                        label-align-sm="right"
                        label-size="sm"
                        label-class="pt-3"
                        class="mb-0"
                    >
                        <b-form-select
                            id="per-page-select"
                            v-model="perPage"
                            :options="pageOptions"
                            size="sm"
                        ></b-form-select>
                    </b-form-group>
                </b-col>

            </b-row>

            <!-- Main table element -->
            <b-table
                :items="items"
                :fields="fields"
                :current-page="currentPage"
                :per-page="perPage"
                :filter="filter"
                :filter-included-fields="filterOn"
                :sort-by.sync="sortBy"
                :sort-desc.sync="sortDesc"
                :sort-direction="sortDirection"
                stacked="md"
                :busy="isBusy"
                show-empty
                small
                @filtered="onFiltered"
            >
                <template #table-busy>
                    <div class="text-center text-info my-2">
                        <b-spinner class="align-middle"></b-spinner>
                        <strong>Loading...</strong>
                    </div>
                </template>
                <template #cell(group)="row">
                    <p class="btn" :class="getBtns(row.value.id)" v-html="row.value.name"></p>
                </template>
                <template #cell(new)="row">
                    <p v-html="row.value"></p>
                </template>

                <template #cell(actions)="row">
                    <b-button size="sm" @click="deleteFile(row.item)" class="mr-1 bg-danger text-white">
                        Delete File
                    </b-button>
                </template>

                <template #row-details="row">
                    <b-card>
                        <ul>
                            <li v-for="(value, key) in row.item" :key="key">{{ key }}: {{ value }}</li>
                        </ul>
                    </b-card>
                </template>
            </b-table>
            <b-col sm="12" md="12" class="my-1">
                <b-pagination
                    v-model="currentPage"
                    :total-rows="totalRows"
                    :per-page="perPage"
                    align="center"
                    size="sm"
                    class="my-0"
                ></b-pagination>
            </b-col>
        </b-container>
    </div>
</template>

<script>
export default {
    name: "DocumentUpload",
    data(){
        return {
            renderComponent:true,
            groups:{},
            options: {
                target: '/api/document-upload',
                testChunks: false,
                query: { group_id: '' },

            },
            attrs: {
                accept: 'image/*',
            },
            fileList : {},
            selected:false,
            isBusy: false,
            items: [],
            fields: [
                { key: 'group', label: 'Group', sortable: true, class: 'text-left' },
                { key: 'new', label: 'File', sortable: true, sortDirection: 'desc' },
                { key: 'file_type', label: 'File Type', sortable: true, class: 'text-center' },
                { key: 'actions', label: 'Actions' }
            ],
            totalRows: 1,
            currentPage: 1,
            perPage: 5,
            pageOptions: [5, 10, 15, 20],
            sortBy: '',
            sortDesc: false,
            sortDirection: 'asc',
            filter: null,
            filterOn: [],
        }
    },
    mounted(){

            this.loadGroups();
            this.loadGroupDocuments();
        // Set the initial number of items
        this.totalRows = this.items.length

        },
    computed: {
        sortOptions() {
            // Create an options list from our fields
            return this.fields
                .filter(f => f.sortable)
                .map(f => {
                    return { text: f.label, value: f.key }
                })
        },
    },
    methods:{

        loadGroups(){
            axios.get('api/session/groups')
                .then((data) => this.groups = data.data.data );

        },
        loadGroupDocuments(){
            this.isBusy = true;
            axios.get('api/documents')
                .then((data) => {this.items = (data.data.data); this.isBusy = false; this.totalRows = this.items.length; });

        },
        onFileError(rootFile, file, response, chunk) {
           var res = JSON.parse(response);
            toast.fire({
                type: 'error',
                title: res.message,
                timer: 4000,
            });
            this.selected = true;
        },
        deleteFile(item){

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
                    axios.delete('api/document/'+item.id)
                        .then(() => {

                            swal.fire(
                                'Deleted!',
                                'Your file has been deleted.',
                                'success'
                            )

                            this.loadGroupDocuments();
                        })
                        .catch(() => {

                        })
                }
            })
        },
        FileListChanged(){
            this.$refs.files.fileList = [];
            this.selected=false;
        },

        onFiltered(filteredItems) {
            // Trigger pagination to update the number of buttons/pages due to filtering
            this.totalRows = filteredItems.length
            this.currentPage = 1
        },
        getBtns(extension){
            const icon = [];
            icon[1] = "btn-primary";
            icon[2] = "btn-light";
            icon[3] = "btn-success";
            icon[4] = "btn-warning";
            icon[5] = "btn-danger";;
            return icon[extension];
        },

    }
}
</script>

<style scoped>
.document-uploaded {
    /*width: 880px;*/
    padding: 15px;
    margin: 40px auto 0;
    /*font-size: 12px;*/
    box-shadow: 0 0 10px rgba(0, 0, 0, .4);
}
.document-uploaded .uploader-btn {
    margin-right: 4px;
}
.document-uploaded .uploader-list {
    max-height: 440px;
    overflow: auto;
    overflow-x: hidden;
    overflow-y: auto;
}
</style>
