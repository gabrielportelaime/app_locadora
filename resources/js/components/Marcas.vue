<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- início do card de busca de marcas -->
                <card-component titulo="Busca de marcas">
                    <template v-slot:conteudo>
                        <div class="row g-3">
                            <div class="col mb-3">
                                <input-container-component titulo="ID" id="inputId" id-help="idHelp"
                                    texto-ajuda="Informe o ID da marca (opcional)">
                                    <input type="number" class="form-control" id="inputId" aria-describedby="idHelp"
                                        placeholder="ID" v-model="busca.id">
                                </input-container-component>
                            </div>
                            <div class="col mb-3">
                                <input-container-component titulo="Nome da marca" id="inputNome" id-help="nomeHelp"
                                    texto-ajuda="Informe o nome da marca (opcional)">
                                    <input type="text" class="form-control" id="inputNome" aria-describedby="nomeHelp"
                                        placeholder="Nome da marca" v-model="busca.nome">
                                </input-container-component>
                            </div>
                        </div>
                    </template>
                    <template v-slot:rodape>
                        <button type="submit" class="btn btn-primary btn-sm float-end"
                            @click="pesquisar()">Pesquisar</button>
                    </template>
                </card-component>
                <!-- fim do card de busca de marcas -->
                <!-- inicio do card de listagem de marcas -->
                <card-component titulo="Listagem de marcas">
                    <template v-slot:conteudo>
                        <table-component :dados="marcas.data" 
                            :visualizar= "{
                                visivel: true,
                                dataToggle: 'modal',
                                dataTarget: '#modalMarcaVisualizar'
                            }"
                            :atualizar="{
                                visivel: true,
                                dataToggle: 'modal',
                                dataTarget: '#modalMarcaAtualizar'
                            }"
                            :remover="{
                                visivel: true,
                                dataToggle: 'modal',
                                dataTarget: '#modalMarcaRemover'
                            }" 
                            :titulos="{
                                id: { titulo: 'ID', tipo: 'texto' },
                                nome: { titulo: 'Nome da Marca', tipo: 'texto' },
                                imagem: { titulo: 'Logo da Marca', tipo: 'imagem' },
                                created_at: { titulo: 'Data de Criação', tipo: 'data' }
                            }">
                        </table-component>
                    </template>
                    <template v-slot:rodape>
                        <div class="row">
                            <div class="col-10">
                                <paginate-component>
                                    <li v-for="l, key in marcas.links" :key="key"
                                        :class="l.active ? 'page-item active' : 'page-item'" @click="paginacao(l)">
                                        <a class="page-link" v-html="l.label"></a>
                                    </li>
                                </paginate-component>
                            </div>
                            <div class="col">
                                <button type="button" class="btn btn-primary btn-sm float-end" data-bs-toggle="modal"
                                    data-bs-target="#modalMarca">Adicionar</button>
                            </div>
                        </div>
                    </template>
                </card-component>
                <!-- fim do card de listagem de marcas -->
            </div>
        </div>
        <!-- INICIO DO MODAL DE INCLUSÃO DE MARCA -->
        <modal-component id="modalMarca" titulo="Adicionar Marca">
            <template v-slot:alertas>
                <alert-component tipo="success" :detalhes="transacaoDetalhes" titulo="Cadastro realizado com sucesso!"
                    v-if="transacaoStatus == 'adicionado'"></alert-component>
                <alert-component tipo="danger" :detalhes="transacaoDetalhes" titulo="Erro ao tentar cadastrar a marca!"
                    v-if="transacaoStatus == 'erro'"></alert-component>
            </template>
            <template v-slot:conteudo>
                <div class="form-group">
                    <input-container-component titulo="Nome da marca" id="novoNome" id-help="novoNomeHelp"
                        texto-ajuda="Informe o nome da marca">
                        <input type="text" class="form-control" id="novoNome" aria-describedby="novoNomeHelp"
                            placeholder="Nome da marca" v-model="nomeMarca">
                    </input-container-component>
                </div>
                <div class="form-group">
                    <input-container-component titulo="Imagem" id="novoImagem" id-help="novoImagemHelp"
                        texto-ajuda="Selecione uma imagem no formato PNG">
                        <input type="file" class="form-control" id="novoImagem" aria-describedby="novoImagemHelp"
                            placeholder="Selecione uma imagem" @change="carregarImagem($event)">
                    </input-container-component>
                </div>
            </template>
            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" @click="salvar()">Salvar</button>
            </template>
        </modal-component>
        <!-- FIM DO MODAL DE INCLUSÃO DE MARCA -->
        <!-- INICIO DO MODAL DE VISUALIZAR MARCA -->
        <modal-component id="modalMarcaVisualizar" titulo="Visualizar Marca">
            <template v-slot:alertas>
            </template>
            <template v-slot:conteudo>
                <input-container-component titulo="ID">
                    <input type="text" class="form-control" :value="$store.state.item.id" disabled>
                </input-container-component>
                <input-container-component titulo="Nome da Marca">
                    <input type="text" class="form-control" :value="$store.state.item.nome" disabled>
                </input-container-component>
                <input-container-component titulo="Data de criação">
                    <input type="text" class="form-control" :value="formatarData($store.state.item.created_at)" disabled>
                </input-container-component>
                <input-container-component titulo="Logo da marca">
                    <img :src="'storage/'+$store.state.item.imagem" v-if="$store.state.item.imagem">
                </input-container-component>
            </template>
            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </template>
        </modal-component>
        <!-- FIM DO MODAL DE VISUALIZAR MARCA -->
        <!-- INICIO DO MODAL DE REMOVER MARCA -->
        <modal-component id="modalMarcaRemover" titulo="Remover Marca">
            <template v-slot:alertas>
                <alert-component tipo="success" titulo="Sucesso!" :detalhes="$store.state.transacao" v-if="$store.state.transacao.status == 'sucesso'"></alert-component>
                <alert-component tipo="danger" titulo="Erro!" :detalhes="$store.state.transacao" v-if="$store.state.transacao.status == 'erro'"></alert-component>
            </template>
            <template v-slot:conteudo v-if="$store.state.transacao.status != 'sucesso'">
                <input-container-component titulo="ID">
                    <input type="text" class="form-control" :value="$store.state.item.id" disabled>
                </input-container-component>
                <input-container-component titulo="Nome da Marca">
                    <input type="text" class="form-control" :value="$store.state.item.nome" disabled>
                </input-container-component>
            </template>
            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-danger" @click="remover()" v-if="$store.state.transacao.status != 'sucesso'">Excluir</button>
            </template>
        </modal-component>
        <!-- FIM DO MODAL DE REMOVER MARCA -->
        <!-- INICIO DO MODAL DE ATUALIZAÇÃO DE MARCA -->
        <modal-component id="modalMarcaAtualizar" titulo="Atualizar Marca">
            <template v-slot:alertas>
                <alert-component tipo="success" titulo="Sucesso!" :detalhes="$store.state.transacao" v-if="$store.state.transacao.status == 'sucesso'"></alert-component>
                <alert-component tipo="danger" titulo="Erro!" :detalhes="$store.state.transacao" v-if="$store.state.transacao.status == 'erro'"></alert-component>
            </template>
            <template v-slot:conteudo>
                <div class="form-group">
                    <input-container-component titulo="Nome da marca" id="atualizarNome" id-help="atualizarNomeHelp"
                        texto-ajuda="Informe o nome da marca">
                        <input type="text" class="form-control" id="atualizarNome" aria-describedby="atualizarNomeHelp"
                            placeholder="Nome da marca" v-model="$store.state.item.nome">
                    </input-container-component>
                </div>
                <div class="form-group">
                    <input-container-component titulo="Imagem" id="atualizarImagem" id-help="atualizarImagemHelp"
                        texto-ajuda="Selecione uma imagem no formato PNG">
                        <input type="file" class="form-control" id="atualizarImagem" aria-describedby="atualizarImagemHelp"
                            placeholder="Selecione uma imagem" @change="carregarImagem($event)">
                    </input-container-component>
                </div>
            </template>
            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" @click="atualizar()">Atualizar</button>
            </template>
        </modal-component>
        <!-- FIM DO MODAL DE ATUALIZAÇÃO DE MARCA -->
    </div>
</template>

<script>
import axios from 'axios'

export default {
    mounted() {
        this.carregarLista()
    },
    data() {
        return {
            urlBase: 'http://localhost:8000/api/v1/marca',
            urlPaginacao: '',
            urlFiltro: '',
            nomeMarca: '',
            arquivoImagem: [],
            transacaoStatus: '',
            transacaoDetalhes: {},
            marcas: { data: [] },
            busca: { id: '', nome: '' },
        }
    },
    methods: {
        atualizar(){
            let formData = new FormData()
            formData.append('_method', 'patch')
            formData.append('nome', this.$store.state.item.nome)
            if(this.arquivoImagem[0]){
                formData.append('imagem', this.arquivoImagem[0])
            }
            let url = this.urlBase + '/' + this.$store.state.item.id
            let config = {
                headers: {
                    'Content-Type': 'multipart/form-data',
                }
            }
            axios.post(url, formData, config)
                .then(response => {
                    console.log(response)
                    //Limpar o campo de seleção de arquivos
                    atualizarImagem.value = ''
                    this.$store.state.transacao.status = 'sucesso'
                    this.$store.state.transacao.mensagem = 'Registro de marca atualizado com sucesso!'
                    this.carregarLista()
                }).catch(errors => {
                    this.$store.state.transacao.status = 'erro'
                    this.$store.state.transacao.mensagem = errors.response.data.message
                    this.$store.state.transacao.dados = errors.response.data.errors
                })
        },
        remover(){
            let confirmacao = confirm('Deseja realmente remover o registro?')
            if(!confirmacao){
                return false
            }
            let formData = new FormData()
            formData.append('_method', 'delete')
            let url = this.urlBase + '/' + this.$store.state.item.id
            axios.post(url, formData)
                .then(response => {
                    this.$store.state.transacao.status = 'sucesso'
                    this.$store.state.transacao.mensagem = response.data.msg
                    this.carregarLista()    
                }).catch(errors => {
                    this.$store.state.transacao.status = 'erro'
                    this.$store.state.transacao.mensagem = errors.response.data.erro
                })
        },
        formatarData(data){
            const dataCriacao = new Date(data)
            return dataCriacao.toLocaleDateString('pt-BR')
        },
        pesquisar() {
            console.log(this.busca)
            let filtro = ''
            for (let chave in this.busca) {
                if (this.busca[chave]) {
                    if (filtro != '') {
                        filtro += ';'
                    }
                    filtro += chave + ':like:' + this.busca[chave]
                }
            }
            if (filtro != '') {
                this.urlPaginacao = 'page=1'
                this.urlFiltro = '&filtro=' + filtro
            } else {
                this.urlFiltro = ''
            }
            console.log(this.urlFiltro)
            this.carregarLista()
        },
        paginacao(l) {
            if (l.url) {
                this.urlPaginacao = l.url.split('?')[1]
                this.carregarLista()
            }
        },
        carregarLista() {
            let url = this.urlBase + '?' + this.urlPaginacao + this.urlFiltro
            axios.get(url)
                .then(response => {
                    this.marcas = response.data
                }).catch(errors => {
                    console.log(errors)
                })
        },
        carregarImagem(e) {
            this.arquivoImagem = e.target.files
        },
        salvar() {
            let formData = new FormData()
            formData.append('nome', this.nomeMarca)
            formData.append('imagem', this.arquivoImagem[0])
            let config = {
                headers: {
                    'Content-type': 'multipart/form-data',
                }
            }
            axios.post(this.urlBase, formData, config)
                .then(response => {
                    this.transacaoStatus = 'adicionado'
                    this.transacaoDetalhes = {
                        mensagem: 'ID do registro: ' + response.data.id
                    }
                    console.log(response)
                    this.carregarLista()   
                })
                .catch(errors => {
                    this.transacaoStatus = 'erro'
                    this.transacaoDetalhes = {
                        mensagem: errors.response.data.message,
                        dados: errors.response.data.errors
                    }
                })
        }
    },
}
</script>
