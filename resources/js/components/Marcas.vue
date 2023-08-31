<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- início do card de busca de marcas -->
                <card-component titulo="Busca de marcas">
                    <template v-slot:conteudo>
                        <div class="row g-3">
                            <div class="col mb-3">
                                <input-container-component titulo="ID" id="inputId" id-help="idHelp" texto-ajuda="Informe o ID da marca (opcional)">
                                    <input type="number" class="form-control" id="inputId" aria-describedby="idHelp" placeholder="ID">
                                </input-container-component>
                            </div>
                            <div class="col mb-3">
                                <input-container-component titulo="Nome da marca" id="inputNome" id-help="nomeHelp" texto-ajuda="Informe o nome da marca (opcional)">
                                    <input type="text" class="form-control" id="inputNome" aria-describedby="nomeHelp" placeholder="Nome da marca">
                                </input-container-component>
                            </div>
                        </div>
                    </template>   
                    <template v-slot:rodape>
                        <button type="submit" class="btn btn-primary btn-sm float-end">Pesquisar</button>
                    </template>
                </card-component>
                <!-- fim do card de busca de marcas -->
                <!-- inicio do card de listagem de marcas -->
                <card-component titulo="Listagem de marcas">
                    <template v-slot:conteudo>
                        <table-component :dados="marcas.data" 
                            :titulos="{
                                id: {titulo: 'ID', tipo: 'texto'}, 
                                nome: {titulo: 'Nome da Marca', tipo: 'texto'}, 
                                imagem: {titulo: 'Logo da Marca', tipo: 'imagem'},
                                created_at: {titulo: 'Data de Criação', tipo: 'data'}}">
                        </table-component>
                    </template>
                    <template v-slot:rodape>
                        <div class="row">
                            <div class="col-10">
                                <paginate-component>
                                    <li v-for="l, key in marcas.links" :key="key" class="page-item" @click="paginacao(l)">
                                        <a class="page-link" href="#" v-html="l.label"></a>
                                    </li>
                                </paginate-component>
                            </div>
                            <div class="col">
                                <button type="button" class="btn btn-primary btn-sm float-end" data-bs-toggle="modal" data-bs-target="#modalMarca">Adicionar</button>
                            </div>
                        </div>
                    </template>
                </card-component>
                <!-- fim do card de listagem de marcas -->
            </div>
        </div>
        <modal-component id="modalMarca" titulo="Adicionar Marca">
            <template v-slot:alertas>
                <alert-component tipo="success" :detalhes="transacaoDetalhes" titulo="Cadastro realizado com sucesso!" v-if="transacaoStatus == 'adicionado'"></alert-component>
                <alert-component tipo="danger" :detalhes="transacaoDetalhes" titulo="Erro ao tentar cadastrar a marca!" v-if="transacaoStatus == 'erro'"></alert-component>
            </template>
            <template v-slot:conteudo>
                <div class="form-group">
                    <input-container-component titulo="Nome da marca" id="novoNome" id-help="novoNomeHelp" texto-ajuda="Informe o nome da marca">
                        <input type="text" class="form-control" id="novoNome" aria-describedby="novoNomeHelp" placeholder="Nome da marca" v-model="nomeMarca">
                    </input-container-component>
                </div>
                <div class="form-group">
                    <input-container-component titulo="Imagem" id="novoImagem" id-help="novoImagemHelp" texto-ajuda="Selecione uma imagem no formato PNG">
                        <input type="file" class="form-control" id="novoImagem" aria-describedby="novoImagemHelp" placeholder="Selecione uma imagem" @change="carregarImagem($event)">
                    </input-container-component>
                </div>
            </template>
            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" @click="salvar">Salvar</button>
            </template>
        </modal-component>
    </div>
</template>

<script>
import axios from 'axios'

    export default {
        mounted() {
            this.carregarLista()
        },
        data(){
            return {
                urlBase: 'http://localhost:8000/api/v1/marca',
                nomeMarca: '',
                arquivoImagem: [],
                transacaoStatus: '',
                transacaoDetalhes: {},
                marcas: {data: []},
            }
        },
        computed: {
                token(){
                    let token = document.cookie.split(';').find(indice => {
                        return indice.startsWith('token=')
                    })
                    token = token.split('=')[1]
                    return 'Bearer ' + token
                }
            },
        methods:{
            paginacao(l){
                this.urlBase = l.url
                this.carregarLista()
                console.log(l.url)
            },
            carregarLista(){
                let config = {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': this.token
                    }
                }
                axios.get(this.urlBase, config)
                .then(response => {
                    this.marcas = response.data
                }).catch(errors => {
                    console.log(errors)
                })
            },
            carregarImagem(e){
                this.arquivoImagem = e.target.files
            },
            salvar(){
                let formData = new FormData()
                formData.append('nome', this.nomeMarca)
                formData.append('imagem', this.arquivoImagem[0])
                let config = {
                    headers: {
                        'Content-type': 'multipart/form-data',
                        'Accept': 'application/json',
                        'Authorization': this.token
                    }
                }
                axios.post(this.urlBase, formData, config)
                    .then(response => {
                        this.transacaoStatus = 'adicionado'
                        this.transacaoDetalhes = {
                            mensagem: 'ID do registro: ' + response.data.id
                        }
                        console.log(response)
                    })
                    .catch(errors => {
                        this.transacaoStatus = 'erro'
                        this.transacaoDetalhes = {
                            mensagem: errors.response.data.message,
                            dados: errors.response.data.errors
                        }
                        // console.log(errors.response.data.message)
                    })
            }
        },
    }
</script>
