<template>
    <div class="container col-md-10">
        <b-container fluid class="ml-2">
            <b-alert v-model="data_deleted" variant="success" dismissible>
                Visi mokėjimų ir krepšelių duomenys buvo ištrinti!
            </b-alert>
            <b-row class="mb-2">
                <b-col md="8">
                    <div class="form-group">
                        <label class="font-weight-bold mb-0" for="username" style="font-size: 16px">Vartotojo vardas</label>
                        <b-form-group v-if="edit" id="username-group" label-for="username">
                            <b-form-input
                            id="username"
                            v-model="user.name"
                            type="text"
                            placeholder="Įveskite vartotojo vardą"
                            maxlength="100"
                            ></b-form-input>
                        </b-form-group>
                        <div v-else v-text="user.name"></div>
                        <div class="font-weight-bold text-danger" v-if="errors.name" v-text="errors.name[0]"></div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold mb-0" for="email" style="font-size: 16px">Vartotojo el.paštas</label>
                        <b-form-group v-if="edit" id="email-group" label-for="email">
                            <b-form-input
                            id="email"
                            v-model="user.email"
                            type="email"
                            placeholder="Įveskite el. paštą"
                            maxlength="100"
                            ></b-form-input>
                        </b-form-group>
                        <div v-else v-text="user.email"></div>
                        <div class="font-weight-bold text-danger" v-if="errors.email" v-text="errors.email[0]"></div>
                    </div>
                    <div class="form-group">
                        <b-form-group v-if="edit" id="subscription-group" label-for="subscription">
                            <b-form-checkbox
                            id="subscription"
                            type="checkbox"
                            v-model="user.subscription"
                            :value="true"
                            :unchecked-value="false"
                            class="d-inline"
                            >
                            </b-form-checkbox>
                            <label class="font-weight-bold mb-0 d-inline" for="subscription" style="font-size: 16px">
                                Ar sutinkate prenumeruoti el. laiškus?
                            </label>
                        </b-form-group>
                        <label v-else class="font-weight-bold mb-0" for="subscription" style="font-size: 16px">
                            El. laiškų prenumerata 
                            <span v-text="user.subscription == true ? 'įjungta' : 'išjungta'"></span>
                        </label>
                        
                    </div>
                </b-col>
                <b-col md="4">
                    <button class="btn bg-main-teal" v-on:click="editUser()" v-text="edit ? 'Išsaugoti' : 'Redaguoti'"></button>
                </b-col>
            </b-row>
            <hr>
            <button class="btn bg-main-teal" v-on:click="$refs['confirmDeleteModal'].show()">Ištrinti visus vartotojo įrašus</button>
            <br>
            <button class="btn bg-p1-red mt-1" v-on:click="$refs['confirmUserDeleteModal'].show()">Ištrinti vartotojo paskyrą</button>
        </b-container>
        <b-modal ref="confirmDeleteModal" centered hide-header ok-title="Taip" ok-variant="danger" cancel-title="Ne, atšaukti"
            v-on:ok="deleteData()">
            Patvirtinus pasirinkimą, bus ištrinti visi su jūsų paskyra susiję mokėjimai ir krepšeliai.
            <br>
            <strong>Ar tikrai norite ištrinti duomenis?</strong>
        </b-modal>
        <b-modal ref="confirmUserDeleteModal" centered hide-header ok-title="Taip" ok-variant="danger" cancel-title="Ne, atšaukti"
            v-on:ok="deleteUser()">
            Patvirtinus pasirinkimą, bus ištrinta jūsų vartotojo paskyra.
            <br>
            Atliktų pakeitimų atšaukti nebus galima.
            <br>
            <strong>Ar tikrai norite ištrinti paskyrą?</strong>
        </b-modal>
    </div>
</template>

<script>
export default {
    props: {
        user: { type: Object, required: true },
    },

    data() {
        return {
            edit: false,
            errors: [],
            data_deleted: false,
        }
    },

    methods: {
        editUser() {
            if(this.edit == false) {
                this.edit = true;
            }
            else {
                this.submit();
            }
        },

        submit() {
            axios
                .post('/apiedituser', this.user)
                .then(data => {
                    //location.href = '/stats';
                    if(data.data.errors)
                        this.errors = data.data.errors;
                    else
                        this.edit = false;
                })
                .catch(error => {
                    //console.log(error);
                    this.errors = error.response.data.errors;
                });
        },

        deleteData() {
            axios
                .post('/apidestroydata')
                .then(data => {
                    //location.href = '/stats';
                    this.data_deleted = true;
                })
                .catch(error => {
                    console.log(error);
                });
        },

        deleteUser() {
            axios
                .post('/apidestroyuser')
                .then(data => {
                    location.href = '/login';
                })
                .catch(error => {
                    console.log(error);
                });
        }
    }
}
</script>