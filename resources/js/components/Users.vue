<template>
    <div class="container">
        <div class="row justify-content-center">
                       
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Users Table</h3>

                <div class="card-tools">
                    <button class="btn btn-success" @click="newModal">Add New 
                        <i class="fas fa-user-plus"></i>
                    </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <tbody><tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Registered at</th>
                    <th>Tools</th>
                  </tr>
                  <tr v-for="user in users" :key="user.id">
                    <td>{{ user.id }}</td>
                    <td>{{ user.name }}</td>
                    <td>{{ user.email }}</td>
                    <td><span class="tag tag-success">{{ user.type | upText }}</span></td>
                    <td>{{ user.created_at | formatDateId }}</td>

                    <td>
                        <a href="#" @click="editModal(user)">
                            <i class="fas fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="confirmDelete(user.id)">
                            <i class="fas fa-trash red"></i>
                        </a>
                    </td>
                  </tr>
                </tbody></table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNewLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
            <form @submit.prevent="editMode ? updateUser() : createUser() ">
            <div class="modal-header">
                <h5 class="modal-title" id="addNewLabel"> {{ editMode ? 'Edit User Info' : 'Add New' }} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input v-model="form.name" type="text" name="name" placeholder="Name"
                        class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                    <has-error :form="form" field="name"></has-error>
                </div>

                <div class="form-group">
                    <input v-model="form.email" type="text" name="email" placeholder="Email Address"
                        class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                    <has-error :form="form" field="email"></has-error>
                </div>

                <div class="form-group">
                    <textarea v-model="form.bio" name="bio" placeholder="Short bio for user (optional)"
                        class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }" />
                    <has-error :form="form" field="bio"></has-error>
                </div>

                <div class="form-group">
                    <select v-model="form.type" name="type"
                        class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
                        <option value="">Select User Role</option>
                        <option value="admin">Admin</option>
                        <option value="user">Standard User</option>
                        <option value="author">Author</option>
                    </select>
                    <has-error :form="form" field="type"></has-error>
                </div>
                
                <div v-show="!editMode">
                    <div class="form-group">
                        <input v-model="form.password" type="password" name="password" placeholder="Password"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                        <has-error :form="form" field="password"></has-error>
                    </div>

                    
                    <div class="form-group">
                        <input v-model="form.password_confirmation" type="password" 
                        class="form-control"
                        name="password_confirmation" placeholder="Confirm Password" id="password-confirm"
                        autocomplete="new-password">
                        <has-error :form="form" field="password"></has-error>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
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
        mounted() {
        },
        data () {
            return {
                editMode : false,
                form: new Form ({
                    id: '',
                    name : '',
                    email : '',
                    password : '',
                    type : '',
                    bio : '',
                    photo : '',
                    password_confirmation: ''
                }), 
                users: []
            }
        },
        methods: {
            newModal () {
                this.form.reset()
                this.form.clear()
                this.editMode = false
                $('#addNew').modal('show')
            },
            editModal (user) {
                this.form.reset()
                this.form.clear()
                this.editMode = true
                $('#addNew').modal('show')
                this.form.fill(user)                
            },
            async createUser () {
                this.$Progress.start()

                try {
                    await this.form.post('api/users')
                    Fire.$emit('AfterCreate')
                } catch (e) {
                    Swal.fire('Failed', 'There was something wrong. \n' + e, 'warning')
                }

                this.$Progress.finish()
            },
            async updateUser () {
                this.$Progress.start()
                try {
                    await this.form.put(`api/users/${this.form.id}`)
                    Fire.$emit('AfterCreate')
                } catch (e) {
                    Swal.fire('Failed', 'There was something wrong. \n' + e, 'warning')
                }
                this.$Progress.finish()
            },
            async loadUsers () {
                let res = await axios.get('/api/users')
                this.users = res.data.data
            },
            async confirmDelete (id) {
                let result = await Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                })

                if (result.value) {
                    this.deleteUser(id)
                    this.loadUsers()
                }
            },
            async deleteUser (id) {
                try {
                    // await axios.delete('/api/users/' +id)
                    let res = await this.form.delete('/api/users/' +id)
                    Swal.fire(
                        'Deleted!',
                         res.data.message,
                        'success'
                    )
                } catch (e) {
                    Swal.fire('Failed', 'There was something wrong. \n' + e, 'warning')
                }
            }
        },
        created () {
            this.loadUsers() 
            setInterval(() => this.loadUsers(), 15000)

            Fire.$on('AfterCreate', () => {
                this.loadUsers()
                $('#addNew').modal('hide')
                let mode = this.editMode ? 'updated' : 'created'
                Toast.fire({
                    type: 'success',
                    title: `User ${mode} in successfully`
                })
            })
        }
    }
</script>
