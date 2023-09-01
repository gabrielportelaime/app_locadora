<template>
    <div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col" v-for="(t, key) in titulos" :key="key">
                        {{ t.titulo }}
                    </th>
                    <th v-if="visualizar.visivel || editar.visivel || remover.visivel">
                        Opções
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="obj, chave in dadosFiltrados" :key="chave">
                    <td v-for="valor, chaveValor in obj" :key="chaveValor">
                        <span v-if="titulos[chaveValor].tipo == 'texto'">{{ valor }}</span>
                        <span v-if="titulos[chaveValor].tipo == 'data'">{{ formatarData(valor) }}</span>
                        <span v-if="titulos[chaveValor].tipo == 'imagem'">
                            <img :src="'/storage/'+valor" :alt="valor" width="50" height="50"/>
                        </span>
                    </td>
                    <td v-if="visualizar.visivel || editar.visivel || remover.visivel">
                        <button v-if="visualizar.visivel" class="btn btn-outline-primary btn-sm" style="margin:5px" :data-bs-toggle="visualizar.dataToggle" :data-bs-target="visualizar.dataTarget" @click="setMarca(obj)">Visualizar</button>
                        <button v-if="editar" class="btn btn-outline-secondary btn-sm" style="margin:5px" :data-bs-toggle="editar.dataToggle" :data-bs-target="editar.dataTarget" @click="setMarca(obj)">Editar</button>
                        <button v-if="remover" class="btn btn-outline-danger btn-sm" style="margin:5px" :data-bs-toggle="remover.dataToggle" :data-bs-target="remover.dataTarget" @click="setMarca(obj)">Remover</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    props: ["dados", "titulos", "visualizar", "remover", "editar"],
    computed: {
        dadosFiltrados(){
            let campos = Object.keys(this.titulos)
            let dadosFiltrados = []
            this.dados.map((item, chave) => {
                let itemFiltrado = {}
                campos.forEach(campo =>{
                    itemFiltrado[campo] = item[campo]
                })
                dadosFiltrados.push(itemFiltrado)
            })
            return dadosFiltrados //retorna um array de objetos
        }
    },
    methods: {
        formatarData(data){
            const dataCriacao = new Date(data)
            return dataCriacao.toLocaleDateString('pt-BR')
        },
        setMarca(obj){
            this.$store.state.item = obj
        }    
    },
};
</script>