<template>
    <router-view></router-view>
</template>

<style lang="scss">
    * {
        margin: 0;
        box-sizing: border-box;
    }
    body {
        width: calc(100vw - (100vw - 100%));
        font-family: sans-serif;
        color: #1a1a1a;
    }
    
    .errorNotice {
        color: red;
        background: #ffe6e6;
        padding: 5px 1rem;
        border-radius: 5px;
        font-size: .9rem;
        margin-bottom: .5rem;
    }
    .invalid {
        background: #ffe6e6;
        border: 1px solid red;
    }
</style>