<template>
    <div class="mt-3">
        <h2 class="display-5 mb-4">Usuários</h2>

        <button class="btn btn-success mb-2" @click="openModal('cadastrar')">Adicionar Usuário</button>
        <div class="card mb-4 p-3" >
        <!-- Área tabela -->
            <table class="table table-striped table-hover" v-if="usuarios.data.length > 0">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Usuário</th>
                    <th>E-mail</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody class="table-group-divider">
                    <tr v-for="usuario in usuarios.data" :key="usuario.id">
                        <td>{{ usuario.id }}</td>
                        <td>{{ usuario.name }}</td>
                        <td>{{ usuario.email }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary text-danger" style="width: 40px;"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>              
                </tbody>
            </table>              
            <div class="d-flex justify-content-center" v-else-if="load">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div> 
            <div class="container my-2" v-else>
                <div class="p-4 text-center bg-body-tertiary rounded-3">
                    <p class="lead">
                        Nenhum usuário cadastrado!
                    </p>
                </div>
            </div>
            <paginate-component v-if="exibePagination">
                <li v-for="l, k in usuarios.links" :key="k" :class="l.active ? 'page-item active' : 'page-item'" @click="paginacao(l)">
                    <a v-if="usuarios.links[k].label.includes('Previous')" class="page-link" >Anterior</a>
                    <a v-else-if="usuarios.links[k].label.includes('Next')" class="page-link" >Próximo</a>
                    <a v-else class="page-link" >{{ usuarios.links[k].label }}</a>
                </li>
            </paginate-component>   
        </div>
    </div>

    <!-- Modal de cadastro de usuarios -->
    <div class="modal fade" id="usuarioModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">{{ btnModalCadastro == 'cadastrar' ? 'Inserir' : 'Editar' }} Usuario</h1>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                        <div class="col">
                            <div class="mt-2">
                                <label for="usuario" class="form-label">Usuario: <span style="color: red;">*</span> </label>
                                <input id="usuario" type="text" :class="['form-control', classInvalid.usuario]" v-model="usuario" @blur="verificaUsuario()">
                            </div>
                            <div class="invalid-feedback" style="display: block;">
                                {{ typeof errorsForm.usuario !== "undefined" ? errorsForm.usuario[0] : ''  }}
                            </div>    
                        </div>                      
                    </div>   

                    <div class="row">
                        <div class="col">
                            <div class="mt-2">
                                <label for="email" class="form-label">Email: <span style="color: red;">*</span> </label>
                                <input id="email" type="email" :class="['form-control', classInvalid.email]" v-model="email">
                            </div>
                            <div class="invalid-feedback" style="display: block;">
                                {{ typeof errorsForm.email !== "undefined" ? errorsForm.email[0] : ''  }}
                            </div>    
                        </div>                      
                    </div>                     
                    
                    <div class="row">
                        <div class="col">
                            <div class="mt-2">
                                <label for="tp_usuario" class="form-label">Tipo de Usuário: <span style="color: red;">*</span> </label>
                                <select :class="['form-select', classInvalid.tp_usuario]" v-model="tp_usuario" id="tp_usuario">
                                    <option value="" disabled selected>Selecione</option>
                                    <option value="Administrador">Administrador</option>
                                    <option value="Colaborador">Colaborador</option>
                                </select>  
                            </div>
                            <div class="invalid-feedback" style="display: block;">
                                {{ typeof errorsForm.tp_usuario !== "undefined" ? errorsForm.tp_usuario[0] : ''  }}
                            </div>    
                        </div>                      
                    </div>                        
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="mt-2">
                                <label for="usuario" class="form-label">Senha: <span style="color: red;">*</span> </label>
                                <input id="password" type="password" :class="['form-control', classInvalid.password]" v-model="password">
                            </div>
                            <div class="invalid-feedback" style="display: block;">
                                {{ typeof errorsForm.password !== "undefined" ? errorsForm.password[0] : ''  }}
                            </div>    
                        </div>           
                        <div class="col-6">
                            <div class="mt-2">
                                <label for="usuario" class="form-label">Confirmação da Senha: <span style="color: red;">*</span> </label>
                                <input id="password_confirmation" type="password" class="form-control" v-model="password_confirmation">
                            </div> 
                        </div>                                      
                    </div>                    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button v-if="btnModalCadastro == 'cadastrar'" type="button" class="btn btn-success" @click="salvar()">Salvar</button>
                <button v-if="btnModalCadastro == 'editar'" type="button" class="btn btn-warning" @click="editar()">Editar</button>
            </div>
            </div>
        </div>

        <!-- Excluir usuario -->
        <div class="modal fade" id="excluirUsuario" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Excluir Feriado</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Não</button>
                    <button type="button" class="btn btn-danger" @click="excluir()">Sim</button>
                </div>
                </div>
            </div>
        </div>          
    </div>        
</template>

<script>
    export default {
        data() {
            return {
                baseApi: 'http://127.0.0.1:8000/api/v1/usuarios',
                usuarios: {data: []},
                load: false,
                exibePagination: false,
                btnModalCadastro: '',
                errorsForm: {},
                usuario: '',
                password_confirmation: '',
                password: '',
                tp_usuario: '',
                loadInput: false,
                email: '',
                usuario_excluir: ''
            }
        },
        methods: {
            carregarUsuarios() {
                this.load = true;
                let url = this.baseApi + '?' + this.urlPaginacao;

                axios.get(url)
                    .then(response => { 
                        this.usuarios = response.data.usuarios 
                        this.exibePagination = this.usuarios.links.length > 3 ? true : false
                        this.load = false;

                    })
                    .catch(errors => {
                        console.log(errors)
                    })                    
            },
            paginacao(l) {
                if(l.url) {
                    this.urlPaginacao = l.url.split('?')[1]
                    this.carregarUsuarios()
                }
            }, 
            openModal(tipo, usuario = null){
                this.limparModal();
                if(usuario != null) {
                    this.usuario = usuario.id
                    this.tp_usuario = usuario.tp_usuario;
                }

                this.btnModalCadastro = tipo;
                this.errorsForm = {};
                $('#usuarioModal').modal('show');
            },        
            limparModal() {
                this.usuario = '';
                this.password = '';
                this.password_confirmation = '';
                this.tp_usuario = '';
            },   
            verificaUsuario() {
                this.loadInput = true;
                let url = this.baseApi  + '/consulta-usuario';

                let formData = new FormData();
                formData.append('usuario', this.usuario);

                let config = {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    }
                }

                axios.post(url, formData, config)
                    .then(response => { 
                        if(!response.data) {
                            this.errorsForm.usuario = ['Usuário já cadastrado'];
                        } else {
                            this.errorsForm = {};
                        }

                        this.loadInput = false;

                    })
                    .catch(errors => {
                        console.log(errors)
                    }) 
            },       
            salvar() {
                let url = this.baseApi;
                
                let formData = new FormData();
                formData.append('usuario', this.usuario);
                formData.append('email', this.email);
                formData.append('tp_usuario', this.tp_usuario);
                formData.append('password', this.password);
                formData.append('password_confirmation', this.password_confirmation);

                let config = {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    }
                }   

                axios.post(url, formData, config)
                    .then(response => { 
                        this.errorsForm = {};
                        this.carregarUsuarios();
                        $('#usuarioModal').modal('hide');
                    })
                    .catch(errors => {
                        this.errorsForm = errors.response.data.errors
                        console.log(errors.response.data)
                        console.log(errors)
                    }) 
            }                                   
        },
        computed: {
            classInvalid() {
                return {
                    usuario : {
                        'is-invalid': this.errorsForm && this.errorsForm.usuario
                    },
                    password : {
                        'is-invalid': this.errorsForm && this.errorsForm.password
                    },
                    tp_usuario : {
                        'is-invalid': this.errorsForm && this.errorsForm.tp_usuario
                    },
                    email : {
                        'is-invalid': this.errorsForm && this.errorsForm.email
                    },                    
                }
            }
        },        
        mounted() {
            this.carregarUsuarios();
        }
    }
</script>
