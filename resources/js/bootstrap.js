import 'bootstrap';

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

axios.interceptors.request.use(
    config => {
        //definir para todas as requisições os parâmetros de acessos
        config.headers.Accept = 'application/json'
        let token = document.cookie.split(';').find(indice => {
            return indice.includes('token=')
        })
        token = token.split('=')[1]
        token = 'Bearer ' + token
        config.headers.Authorization = token
        return config
    },
    error => {
        console.log('Erro na requisição', error)
        return Promise.reject(error)
    }
)

//interceptando as responsees da aplicação
axios.interceptors.response.use(
    response => {
        console.log('Interceptando a resposta', response)
        return response
    },
    error => {
        console.log('Erro na resposta', error.response)
        if(error.response.status == 401 && error.response.data.message == 'Token has expired'){
            console.log('Nova requisição para refresh')
            axios.post('http://localhost:8000/api/refresh')
                .then(response => {
                    console.log('Refresh feito com sucesso: ', response)
                    document.cookie = 'token='+response.data.token
                    console.log('Token atualizado: ', response.data.token)
                    window.location.reload()
                })
                .catch(errors => {

                })
        }
        return Promise.reject(error)
    }
)

