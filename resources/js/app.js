/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue';
import { createStore } from 'vuex'

// Cria uma nova instância do store.
const store = createStore({
    state() {
        return {
            item: {},
            transacao: { status: '', mensagem: '' , dados: ''}
        }
    }
})

const app = createApp({ /* seu componente raiz */ })

app.use(store)

import ExampleComponent from './components/ExampleComponent.vue';
import Login from './components/Login.vue';
import Home from './components/Home.vue';
import Marcas from './components/Marcas.vue';
import InputContainer from './components/InputContainer.vue';
import Table from './components/Table.vue';
import Card from './components/Card.vue';
import Modal from './components/Modal.vue';
import Alert from './components/Alert.vue';
import Paginate from './components/Paginate.vue';

app.component('example-component', ExampleComponent);
app.component('login-component', Login);
app.component('home-component', Home);
app.component('marcas-component', Marcas);
app.component('input-container-component', InputContainer);
app.component('table-component', Table);
app.component('card-component', Card);
app.component('modal-component', Modal);
app.component('alert-component', Alert);
app.component('paginate-component', Paginate);

app.mount('#app');

