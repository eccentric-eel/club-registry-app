<template>
    <div id="pageContainer">
        <div id="pageInner">
            <h3>Administrator Login</h3>

            <div class="errorNotice" v-show="showError">{{errorText}}</div>

            <input  @keydown.enter.prevent="submitLogin()" type="text"     placeholder="username" v-model="username" />
            <input  @keydown.enter.prevent="submitLogin()" type="password" placeholder="password" v-model="password" />
            <button @click="submitLogin()">Login</button>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                username:  '',
                password:  '',
                errorText: '',
                showError: false,
            }
        },
        methods: { 
            submitLogin() {
                if(this.username.length && this.password.length) {

                    this.showError = false;

                    axios.get('/sanctum/csrf-cookie').then(response => {
                        axios.post('/api/login', { username: this.username, password: this.password })
                        .then((response) => this.$router.push('admin/records'))
                        .catch((error) =>  {
                            this.username = '';
                            this.password = '';

                            if(error.response.status === 401) {
                                this.showError = true;
                                this.errorText = "Username/password combination invalid. Try again.";
                            }
                            else {
                                this.showError = true;
                                this.errorText = "A server error occured. Try again later."
                            }
                        });
                    }); 
                }
                else {
                    this.showError = true;

                    this.username = '';
                    this.password = '';
                    this.errorText = 'A valid username and password are required.';
                }
            }
        },
    }
</script>

<style lang="scss" scoped>
    #pageContainer {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        background: #d9d9d9;
    }
    #pageInner {
        width: 30rem;
        max-width: 100vw;
        padding: 1rem 2rem 2rem 2rem;
        margin: .5rem;
        background: #fff;
 
        border: 1px solid #999;
    }

    h3 {
        height: 5rem;
        line-height: 5rem;
        margin-bottom: .5rem;
        font-weight: normal;
        text-align: center;
        background: url('../../../public/assets/18-footers-logo.png') no-repeat center left / auto 100%;
    }
    input {
        width: 100%;
        font-size: 1rem;
        padding: .7rem 2rem;
        margin: .3rem 0;
    }
    button {
        float: right;
        border: none;
        margin-top: .3rem;
        padding: .7rem 2rem;
        background: #333;
        border-radius: 3px;
        color: #fff;
        cursor: pointer;
    }
</style>