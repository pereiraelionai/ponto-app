<template>
    <div class="mt-3">
        <h2 class="display-5 mb-4">Feriados</h2>

        <button class="btn btn-success mb-2" @click="openModal('cadastrar')">Adicionar Feriado</button>
        <div class="card mb-4 p-3" >
        <!-- Área tabela -->
            <table class="table table-striped table-hover" v-if="feriados.data.length > 0">
                <thead>
                <tr>
                    <th>Data</th>
                    <th>Descrição</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody class="table-group-divider">
                    <tr v-for="feriado in feriados.data" :key="feriado.id">
                        <td>{{ formatarData(feriado.data) }}</td>
                        <td>{{ feriado.descricao }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary text-primary me-1" style="width: 40px;" @click="openModal('editar', feriado)"><i class="bi bi-pencil-square"></i></button>
                            <button class="btn btn-sm btn-outline-secondary text-danger" style="width: 40px;" @click="modalExcluir(feriado)"><i class="bi bi-trash"></i></button>
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
                        Nenhum feriado cadastrado!
                    </p>
                </div>
            </div>
            <paginate-component v-if="exibePagination">
                <li v-for="l, k in feriados.links" :key="k" :class="l.active ? 'page-item active' : 'page-item'" @click="paginacao(l)">
                    <a v-if="feriados.links[k].label.includes('Previous')" class="page-link" >Anterior</a>
                    <a v-else-if="feriados.links[k].label.includes('Next')" class="page-link" >Próximo</a>
                    <a v-else class="page-link" >{{ feriados.links[k].label }}</a>
                </li>
            </paginate-component>   
        </div>


        <!-- Modal de cadastro de feriados -->
        <div class="modal fade" id="feriadoModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">{{ btnModalCadastro == 'cadastrar' ? 'Inserir' : 'Editar' }} Feriado</h1>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row">
                            <div class="col-3 mt-2">
                                <label for="data" class="form-label">Data :<span style="color: red;">*</span> </label>
                                <input id="data" type="date" :class="['form-control', classInvalid.data]" v-model="data">
                                <div class="invalid-feedback" style="display: block;">
                                    {{ typeof errorsForm.data !== "undefined" ? errorsForm.data[0] : ''  }}
                                </div>                                   
                            </div>
                            <div class="col-9">
                                <div class="mt-2">
                                    <label for="descricao" class="form-label">Descrição: <span style="color: red;">*</span> </label>
                                    <input id="descricao" type="text" :class="['form-control', classInvalid.descricao]" v-model="descricao">
                                </div>
                                <div class="invalid-feedback" style="display: block;">
                                    {{ typeof errorsForm.descricao !== "undefined" ? errorsForm.descricao[0] : ''  }}
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
        </div>    
        
        <!-- Excluir feriado -->
        <div class="modal fade" id="excluirFeriado" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Excluir Feriado</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Tem certeza que deseja excluir o feriado de  <strong>{{ formatarData(feriado_excluir.data) }}</strong>?
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
                baseApi: 'http://127.0.0.1:8000/api/v1/feriados',
                feriados: {data: []},
                load: false,
                btnModalCadastro: '',
                data: '',
                descricao: '',
                errorsForm: {},
                id: '',
                exibePagination: false,
                feriado_excluir: ''
            }
        },
        methods: {
            carregarFeriados() {
                this.load = true;
                let url = this.baseApi + '?' + this.urlPaginacao;

                axios.get(url)
                    .then(response => { 
                        this.feriados = response.data.feriados 
                        this.exibePagination = this.feriados.links.length > 3 ? true : false
                        this.load = false;

                    })
                    .catch(errors => {
                        console.log(errors)
                    })                    
            },
            paginacao(l) {
                if(l.url) {
                    this.urlPaginacao = l.url.split('?')[1]
                    this.carregarFeriados()
                }
            }, 
            formatarData(data) {
                if (!data) {
                    return;
                }

                const [ano, mes, dia] = data.split('-');
                return `${dia}/${mes}/${ano}`;
            },
            openModal(tipo, feriado = null){
                this.limparModal();
                if(feriado != null) {
                    this.id = feriado.id;
                    this.data = feriado.data;
                    this.descricao = feriado.descricao;
                }

                this.btnModalCadastro = tipo;
                this.errorsForm = {};
                $('#feriadoModal').modal('show');
            },        
            limparModal() {
                this.data = '';
                this.descricao = '';
            },  
            salvar() {
                let url = this.baseApi;
                
                let formData = new FormData();
                formData.append('data', this.data);
                formData.append('descricao', this.descricao);

                let config = {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    }
                }   

                axios.post(url, formData, config)
                    .then(response => { 
                        this.errorsForm = {};
                        this.carregarFeriados();
                        $('#feriadoModal').modal('hide');
                    })
                    .catch(errors => {
                        this.errorsForm = errors.response.data.errors
                        console.log(errors.response.data)
                        console.log(errors)
                    }) 
            }, 
            modalExcluir(feriado) {
                this.feriado_excluir = feriado;
                $('#excluirFeriado').modal('show');
            },
            excluir() {

                let url = this.baseApi + '/' + this.feriado_excluir.id;
                
                let formData = new FormData();
                formData.append('_method', 'delete');

                let config = {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    }
                }   

                axios.post(url, formData, config)
                    .then(response => { 
                        this.carregarFeriados();
                        $('#excluirFeriado').modal('hide');
                        this.feriado_excluir = '';
                    })
                    .catch(errors => {
                        console.log(errors.response.data.errors)
                    }) 

            },    
            editar() {
                let url = this.baseApi + '/' + this.id;
                
                let formData = new FormData();
                formData.append('_method', 'put');
                formData.append('data', this.data);
                formData.append('descricao', this.descricao);

                let config = {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    }
                }   

                axios.post(url, formData, config)
                    .then(response => { 
                        console.log(response.data)
                        this.errorsForm = {};
                        this.carregarFeriados();
                        $('#feriadoModal').modal('hide');
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
                    data : {
                        'is-invalid': this.errorsForm && this.errorsForm.data
                    },
                    descricao : {
                        'is-invalid': this.errorsForm && this.errorsForm.descricao
                    }      
                }
            }
        },        
        mounted() {
            this.carregarFeriados();
        }
        
    }
</script>