<template>
    <div class="mt-3">
        <h2 class="display-5 mb-4">Colaboradores</h2>

        <button class="btn btn-success mb-2" @click="openModal('cadastrar')">Adicionar Colaborador</button>
        <div class="card mb-4 p-3" >
                <!-- Área tabela -->
                    <table class="table table-striped table-hover" v-if="colaboradores.data.length > 0">
                        <thead>
                        <tr>
                            <th>Matrícula</th>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Telefone</th>
                            <th>Ações</th>
                        </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            <tr v-for="colaborador in colaboradores.data" :key="colaborador.id">
                                <td>{{ colaborador.matricula }}</td>
                                <td>{{ colaborador.nome }}</td>
                                <td>{{ colaborador.email }}</td>
                                <td>{{ colaborador.telefone }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-secondary text-primary me-1" style="width: 40px;" @click="openModal('editar', colaborador)"><i class="bi bi-pencil-square"></i></button>
                                    <button class="btn btn-sm btn-outline-secondary text-danger" style="width: 40px;" @click="modalExcluir(colaborador)"><i class="bi bi-trash"></i></button>
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
                                Nenhum colaborador cadastrado!
                            </p>
                        </div>
                    </div>
                    <paginate-component v-if="exibePagination">
                        <li v-for="l, k in colaboradores.links" :key="k" :class="l.active ? 'page-item active' : 'page-item'" @click="paginacao(l)">
                            <a v-if="colaboradores.links[k].label.includes('Previous')" class="page-link" >Anterior</a>
                            <a v-else-if="colaboradores.links[k].label.includes('Next')" class="page-link" >Próximo</a>
                            <a v-else class="page-link" >{{ colaboradores.links[k].label }}</a>
                        </li>
                    </paginate-component>   
        </div>

        <!-- Modal de cadastro de colaboradores -->
        <div class="modal fade" id="colaboradorModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">{{ btnModalCadastro == 'cadastrar' ? 'Inserir' : 'Editar' }} Colaborador</h1>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row">
                            <div class="col-4">
                                <div class="mb-3 mt-2">
                                    <label for="nome" class="form-label">CPF: <span style="color: red;">*</span> </label>
                                    <input type="text" class="form-control" v-model="cpf" id="formatarCpf" @blur="verificaCpf()">
                                </div>
                                <div class="invalid-feedback" style="display: block;">
                                    {{ typeof errorsForm.cpf !== "undefined" ? errorsForm.cpf[0] : ''  }}
                                </div>   
                            </div>
                            <div v-if="loadInput" class="col-1" style="margin-top: 40px;">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mt-2">
                                    <label for="nome" class="form-label">Matricula: <span style="color: red;">*</span> </label>
                                    <input type="text" :class="['form-control', classInvalid.matricula]" v-model="matricula" id="formatarMatricula" disabled>
                                </div>
                                <div class="invalid-feedback" style="display: block;">
                                    {{ typeof errorsForm.matricula !== "undefined" ? errorsForm.matricula[0] : ''  }}
                                </div>    
                            </div>
                            <div class="col mt-5">
                                <input id="ativo" type="checkbox" disabled :checked="true" class="form-check-input me-2" v-model="ativo">
                                <label for="nome" class="form-label">Ativo? </label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-5">
                                <div class="mt-2">
                                    <label for="nome" class="form-label">Nome: <span style="color: red;">*</span> </label>
                                    <input id="nome" name="nome" disabled type="text" :class="['form-control', classInvalid.nome]" v-model="nome">
                                </div>
                                <div class="invalid-feedback" style="display: block;">
                                    {{ typeof errorsForm.nome !== "undefined" ? errorsForm.nome[0] : ''  }}
                                </div>    
                            </div>
                            <div class="col-3 mt-2">
                                <label for="nome" class="form-label">Data de Nascimento:<span style="color: red;">*</span> </label>
                                <input id="dt_nascimento" type="date" :class="['form-control', classInvalid.dt_nascimento]" v-model="dt_nascimento" disabled>
                                <div class="invalid-feedback" style="display: block;">
                                    {{ typeof errorsForm.dt_nascimento !== "undefined" ? errorsForm.dt_nascimento[0] : ''  }}
                                </div>                                   
                            </div>
                            <div class="col-3 mt-2">
                                <label for="nome" class="form-label">Data de Admissão: <span style="color: red;">*</span> </label>
                                <input id="dt_admissao" type="date" :class="['form-control', classInvalid.dt_admissao]" v-model="dt_admissao" disabled>
                                <div class="invalid-feedback" style="display: block;">
                                    {{ typeof errorsForm.dt_admissao !== "undefined" ? errorsForm.dt_admissao[0] : ''  }}
                                </div>   
                            </div>
                        </div>       
                        
                        <div class="row">
                            <div class="col-5">
                                <div class="mt-2">
                                    <label for="email" class="form-label">Email: <span style="color: red;">*</span> </label>
                                    <input id="email" type="email" :class="['form-control', classInvalid.email]" v-model="email" disabled>
                                </div>
                                <div class="invalid-feedback" style="display: block;">
                                    {{ typeof errorsForm.email !== "undefined" ? errorsForm.email[0] : ''  }}
                                </div>   
                            </div>
                            <div class="col-3 mt-2">
                                <label for="cargo" class="form-label">Cargo: <span style="color: red;">*</span> </label>
                                <select disabled :class="['form-select', classInvalid.cargo_id]" v-model="cargo_id" id="cargo">
                                    <option value="" disabled selected>Selecione</option>
                                    <option v-for="cargo, chave in cargos" :key="chave" :value="cargo.id">{{ cargo.descricao }}</option>
                                </select>   
                                <div class="invalid-feedback" style="display: block;">
                                    {{ typeof errorsForm.cargo_id !== "undefined" ? errorsForm.cargo_id[0] : ''  }}
                                </div> 
                            </div>
                            <div class="col-3 mt-2">
                                <label for="funcao" class="form-label">Função: <span style="color: red;">*</span> </label>
                                <select id="funcao" disabled :class="['form-select', classInvalid.funcao_id]" v-model="funcao_id">
                                    <option value="" disabled selected>Selecione</option>
                                    <option v-for="funcao, chave in funcoes" :key="chave" :value="funcao.id">{{ funcao.descricao }}</option>
                                </select> 
                                <div class="invalid-feedback" style="display: block;">
                                    {{ typeof errorsForm.funcao_id !== "undefined" ? errorsForm.funcao_id[0] : ''  }}
                                </div> 
                            </div>
                        </div>     
                        
                        <div class="row">
                            <div class="col-3 mt-2">
                                <label for="telefone" class="form-label">Telefone: <span style="color: red;">*</span> </label>
                                <input  type="text" :class="['form-control', classInvalid.telefone]" v-model="telefone" id="formatarTelefone" disabled>
                                <div class="invalid-feedback" style="display: block;">
                                    {{ typeof errorsForm.telefone !== "undefined" ? errorsForm.telefone[0] : ''  }}
                                </div> 
                            </div>  

                            <div class="col-3 mt-2">
                                <label for="dt_recisao" class="form-label">Data de Recisão: </label>
                                <input id="dt_recisao" type="date" :class="['form-control', classInvalid.dt_recisao]" v-model="dt_recisao" disabled @blur="setAtivo()">
                                <div class="invalid-feedback" style="display: block;">
                                    {{ typeof errorsForm.dt_recisao !== "undefined" ? errorsForm.dt_recisao[0] : ''  }}
                                </div> 
                            </div>
                            <div class="col-3 mt-2">
                                <label for="usuario" class="form-label">Usuário: </label>
                                <input disabled type="text" class="form-control" v-model="usuario">
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
        </div>  

        <!-- Excluir colaborador -->
        <div class="modal fade" id="excluirColaborador" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Excluir Colaborador</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Tem certeza que deseja excluir o colaborador <strong>{{ colaborador_excluir.nome }}</strong>,  marticula <strong>{{ colaborador_excluir.matricula }}?</strong>
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
                baseApi: 'http://127.0.0.1:8000/api/v1/colaboradores',
                colaboradores: {data: []},
                cargos: {},
                funcoes: {},
                load: false,
                loadInput: false,
                exibePagination: false,
                urlPaginacao: '',
                btnModalCadastro: '',
                cpf: '',
                ativo: '',
                nome: '',
                dt_nascimento: '',
                dt_admissao: '',
                cargo_id: '',
                funcao_id: '',
                email: '',
                usuario: '',
                dt_recisao: '',
                telefone: '',
                matricula: '',
                errorsForm: {},
                colaborador_excluir: '',
                id: ''
            }
        },
        methods: {
            carregarColaboradores() {
                this.load = true;
                let url = this.baseApi + '?' + this.urlPaginacao;

                axios.get(url)
                    .then(response => { 
                        this.colaboradores = response.data.colaboradores 
                        this.exibePagination = this.colaboradores.links.length > 3 ? true : false
                        this.load = false;

                    })
                    .catch(errors => {
                        console.log(errors)
                    })                    
            },
            paginacao(l) {
                if(l.url) {
                    this.urlPaginacao = l.url.split('?')[1]
                    this.carregarColaboradores()
                }
            }, 
            openModal(tipo, colaborador = null){
                this.limparModal();
                this.carregarCargos();
                this.carregarFuncoes();
                if(colaborador != null) {
                    this.id = colaborador.id;
                    this.cpf = colaborador.cpf;
                    this.nome = colaborador.nome;
                    this.matricula = colaborador.matricula;
                    this.dt_nascimento = colaborador.dt_nascimento;
                    this.dt_admissao = colaborador.dt_admissao;
                    this.email = colaborador.email;
                    this.cargo_id = colaborador.cargo_id;
                    this.funcao_id = colaborador.funcao_id;
                    this.telefone = colaborador.telefone;
                    this.dt_recisao = colaborador.dt_recisao;
                    this.usuario = colaborador.usuario;
                    document.getElementById('dt_recisao').value = colaborador.dt_recisao;
                    this.desbloquarInput('editar');
                } else {
                    this.bloquarInput();
                    document.getElementById('formatarCpf').disabled = false;
                }
                this.btnModalCadastro = tipo;
                this.setAtivo();
                this.errorsForm = {};
                $('#colaboradorModal').modal('show');
            },
            verificaCpf() {
                this.loadInput = true;
                let url = this.baseApi  + '/consulta-cpf';

                let formData = new FormData();
                formData.append('cpf', this.cpf);

                let config = {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    }
                }

                axios.post(url, formData, config)
                    .then(response => { 
                        
                        if(response.data && this.cpf != '') {
                            this.desbloquarInput();
                            this.errorsForm.cpf = {};
                        }

                        if(!response.data) {
                            this.bloquarInput();
                            this.errorsForm.cpf = ['CPF já cadastrado'];
                        }

                        this.loadInput = false;

                    })
                    .catch(errors => {
                        console.log(errors)
                    }) 
            },
            desbloquarInput(tipo=null) {
                if(tipo == 'editar') {
                    document.getElementById('formatarCpf').disabled = true;
                }
                document.getElementById('nome').disabled = false;
                document.getElementById('formatarMatricula').disabled = false;
                document.getElementById('dt_nascimento').disabled = false;
                document.getElementById('dt_recisao').disabled = false;
                document.getElementById('dt_admissao').disabled = false;
                document.getElementById('formatarTelefone').disabled = false;
                document.getElementById('cargo').disabled = false;
                document.getElementById('funcao').disabled = false;
                document.getElementById('email').disabled = false;
            },
            bloquarInput() {
                document.getElementById('nome').disabled = true;
                document.getElementById('formatarMatricula').disabled = true;
                document.getElementById('dt_nascimento').disabled = true;
                document.getElementById('dt_recisao').disabled = true;
                document.getElementById('dt_admissao').disabled = true;
                document.getElementById('formatarTelefone').disabled = true;
                document.getElementById('cargo').disabled = true;
                document.getElementById('funcao').disabled = true;
                document.getElementById('email').disabled = true;
            },
            setAtivo() {
                const dtRecisaoInput = document.getElementById('dt_recisao');
                const ativoCheckbox = document.getElementById('ativo');
                
                const dataRecisao = new Date(dtRecisaoInput.value);
                dataRecisao.setDate(dataRecisao.getDate() + 1);
                const dataAtual = new Date();
                
                if (!dtRecisaoInput.value) {
                        // Se estiver vazia, habilita o checkbox
                        ativoCheckbox.checked = true;
                    } else {
                        // Verifica se a data de recisão é maior que a data atual
                        if (dataRecisao > dataAtual) {
                            // Se for maior, marca o checkbox
                            ativoCheckbox.checked = true;
                        } else {
                            // Se não for maior, desmarca o checkbox
                            ativoCheckbox.checked = false;
                        }
                }
            },
            carregarCargos() {
                let url = this.baseApi + '-cargos';

                axios.get(url)
                    .then(response => { 
                        this.cargos = response.data .cargos
                    })
                    .catch(errors => {
                        console.log(errors)
                    })  
            },
            carregarFuncoes() {
                let url = this.baseApi + '-funcoes';

                axios.get(url)
                    .then(response => { 
                        this.funcoes = response.data .funcoes
                    })
                    .catch(errors => {
                        console.log(errors)
                    })  
            },
            salvar() {
                let url = this.baseApi;
                
                let formData = new FormData();
                formData.append('nome', this.nome);
                formData.append('matricula', this.matricula);
                formData.append('cpf', this.cpf);
                formData.append('dt_nascimento', this.dt_nascimento);
                formData.append('dt_admissao', this.dt_admissao);
                formData.append('dt_recisao', this.dt_recisao);
                formData.append('email', this.email);
                formData.append('cargo_id', this.cargo_id);
                formData.append('funcao_id', this.funcao_id);
                formData.append('telefone', this.telefone);

                let config = {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    }
                }   

                axios.post(url, formData, config)
                    .then(response => { 
                        this.errorsForm = {};
                        this.carregarColaboradores();
                        $('#colaboradorModal').modal('hide');
                    })
                    .catch(errors => {
                        this.errorsForm = errors.response.data.errors
                        console.log(errors.response.data)
                        console.log(errors)
                    }) 
            },
            limparModal() {
                this.nome = '';
                this.cpf = '';
                this.matricula = '';
                this.dt_nascimento = '';
                this.dt_admissao = '';
                this.dt_recisao = '';
                this.email = '';
                this.telefone = '';
                this.cargo_id = '';
                this.funcao_id = '';
                this.usuario = '';
            },
            modalExcluir(colaborador) {
                this.colaborador_excluir = colaborador;
                $('#excluirColaborador').modal('show');
            },
            excluir() {

                let url = this.baseApi + '/' + this.colaborador_excluir.id;
                
                let formData = new FormData();
                formData.append('_method', 'delete');

                let config = {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    }
                }   

                axios.post(url, formData, config)
                    .then(response => { 
                        this.carregarColaboradores();
                        $('#excluirColaborador').modal('hide');
                        this.colaborador_excluir = '';
                    })
                    .catch(errors => {
                        console.log(errors.response.data.errors)
                    }) 

            },
            editar() {
                let url = this.baseApi + '/' + this.id;
                
                let formData = new FormData();
                formData.append('_method', 'put');
                formData.append('nome', this.nome);
                formData.append('matricula', this.matricula);
                formData.append('cpf', this.cpf);
                formData.append('dt_nascimento', this.dt_nascimento);
                formData.append('dt_admissao', this.dt_admissao);
                formData.append('dt_recisao', this.dt_recisao != null ? this.dt_recisao : '');
                formData.append('email', this.email);
                formData.append('cargo_id', this.cargo_id);
                formData.append('funcao_id', this.funcao_id);
                formData.append('telefone', this.telefone);

                let config = {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    }
                }   

                axios.post(url, formData, config)
                    .then(response => { 
                        console.log(response.data)
                        this.errorsForm = {};
                        this.carregarColaboradores();
                        $('#colaboradorModal').modal('hide');
                    })
                    .catch(errors => {
                        this.errorsForm = errors.response.data.errors
                        console.log(errors.response.data)
                        console.log(errors)
                    }) 
            }
        },
        mounted() {
          this.carregarColaboradores();
        },
        computed: {
            classInvalid() {
                return {
                    nome : {
                        'is-invalid': this.errorsForm && this.errorsForm.nome
                    },
                    cpf : {
                        'is-invalid': this.errorsForm && this.errorsForm.cpf
                    },    
                    matricula : {
                        'is-invalid': this.errorsForm && this.errorsForm.matricula
                    },
                    dt_nascimento : {
                        'is-invalid': this.errorsForm && this.errorsForm.dt_nascimento
                    },
                    dt_admissao : {
                        'is-invalid': this.errorsForm && this.errorsForm.dt_admissao
                    },
                    email : {
                        'is-invalid': this.errorsForm && this.errorsForm.email
                    },
                    telefone : {
                        'is-invalid': this.errorsForm && this.errorsForm.telefone
                    },
                    dt_recisao : {
                        'is-invalid': this.errorsForm && this.errorsForm.dt_recisao
                    },
                    cargo_id : {
                        'is-invalid': this.errorsForm && this.errorsForm.cargo_id
                    },
                    funcao_id : {
                        'is-invalid': this.errorsForm && this.errorsForm.funcao_id
                    }      
                }
            }
        }
    }
</script>