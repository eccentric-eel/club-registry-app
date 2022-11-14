<template>
    <div id="pageContainer">
        <div id="pageInner">
            <h3>Administrator login</h3>
            <input type="text" v-model="username" />
            <input type="password" v-model="password" />
            <button @click="submitLogin()">Login</button>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                username: '',
                password: '',
            }
        },
        methods: { 
            submitLogin() {

                axios.get('/sanctum/csrf-cookie').then(response => {
                    axios.post('/api/login', { username: this.username, password: this.password })
                     .then((response) => {
                        if(response.status === 204)
                            this.$router.push('admin/records');
                        else
                            console.log(response);
                     })
                     .catch((error) => console.log("error: " + error));
                });
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
        padding: 3rem 2rem;
        margin: .5rem;
        background: #fff;
        border-radius: 3px;
        border: 1px solid #ccc;
    }

    h3 {
        text-align: center;
        margin-bottom: 1rem;
    }
    input {
        width: 100%;
        font-size: 1.2rem;
        padding: .5rem 2rem;
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
    }
</style>