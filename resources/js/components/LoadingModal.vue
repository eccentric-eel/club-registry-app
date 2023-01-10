<template>
    <div id="wrapper">
        <div id="inner">
            <div class="lds-ring">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>

            <h3>Exporting Records</h3>
        </div>
    </div>
</template>

<style lang="scss" scoped>
    h3 { 
        user-select: none;
        font-weight: normal;
        margin-top: 1rem;
    }

    #wrapper {
        position: fixed;
        display: flex;
        justify-content: center; 
        align-items: center;
        height: 100%;
        width: 100%; 
        background: rgba(0,0,0,0.2);
        z-index: 100;
    }
    #inner {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background: #333;
        color: #fff;
        padding: 5rem 5rem; 
        border-radius: .5rem; 
        box-shadow: 5px 5px 10px #333;
    }

    .lds-ring {
        display: inline-block;
        position: relative;
        width: 80px;
        height: 80px;

        div {
            box-sizing: border-box;
            display: block;
            position: absolute;
            width: 64px;
            height: 64px;
            margin: 8px;
            border: 8px solid #fff;
            border-radius: 50%;
            animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
            border-color: #fff transparent transparent transparent;

            &:nth-child(1) { animation-delay: -0.45s }
            &:nth-child(2) { animation-delay: -0.3s  }
            &:nth-child(3) { animation-delay: -0.15s }
        }
    }

    @keyframes lds-ring {
        0%   { transform: rotate(0deg)   }
        100% { transform: rotate(360deg) }
    }
</style>