<template>

<div class="col-md-8">
            <div class="card">
                <div class="card-header">Login</div>
                
                <div class="card-body">
                    <form method="POST" :action="urlAction" @submit.prevent="login($event)">
                        
                        <input type="hidden" name="_token" :value="csrf_token">
                        <input type="hidden" name="token" v-model="token">

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">Email</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" required autocomplete="email" autofocus v-model="email">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">Senha</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required autocomplete="current-password" v-model="password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Login
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>                   
</template>

<script>
    export default {
        props: ['csrf_token'],
        data() {
            return {
                email: '',
                password: '',
                token: '',
                urlAction: 'http://127.0.0.1:8000/login',
                urlReset: 'http://127.0.0.1:8000/password/reset'
            }
        },
        methods: {
            login(event) {

                let url = 'http://127.0.0.1:8000/api/login';
                let configFetch = {
                    method: 'post',
                    body: new URLSearchParams({
                        'email' : this.email,
                        'password' : this.password
                    })
                    
                }
                
                fetch(url, configFetch)
                    .then(response => response.json())
                    .then(data => {
                        if(data.token) {
                            this.token = data.token
                            document.cookie = 'token='+data.token+';SameSite=Lax'
                        }
                    })
                    .then(
                            setTimeout(() => {
                            event.target.submit(); // Submete o formulário após um pequeno atraso
                        }, 1500)
                    )

            }
        }
    }
</script>
