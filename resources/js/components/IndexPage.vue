<template>
    <div id="pageContainer">
        <section id="formContainer" v-if="!checkInConfirmed">
            <form id="guestRegisterForm" @submit.prevent="sendForm()">

                <div id="logoInfo">
                    <p>Australian 18 Footers League Ltd</p>
                    <p>ABN 46 001 071 558</p>
                    <p>
                        77 Bay Street, Double Bay 2028<br>
                        PH: 9363 2995<br>
                        www.18footers.com.au
                    </p>
                </div>

                <h1>Temporary member & guest member sign in</h1>

                <div class="inputContain">
                    <input type="text" placeholder="First Name" name="fname" maxlength="20" required>
                    <input type="text" placeholder="Surname" name="sname" maxlength="20" required>
                </div>
                <input type="text" placeholder="Full Address" name="address" required>

                <h3>Please select an appropriate category below</h3>

                <div class="guestCategory" :class="{ 'active': (guestType === 1) }" @click="guestType = 1">
                    <div class="checkbox" :class="{ 'active': (guestType === 1) }"></div>
                    <h4>Temporary Member</h4>
                    <p>I declare that i reside outside a 5km radius of this club.</p>
                </div>
                <div class="guestCategory" :class="{ 'active': (guestType === 2) }" @click="guestType = 2">
                    <div class="checkbox" :class="{ 'active': (guestType === 2) }"></div>
                    <h4>Guest of a Member</h4>
                    <p>I declare that i will remain in the reasonable company of the member at all times and will not remain on club premises longer than that of the member.</p>
                
                    <h5>Accompanying Member Details</h5>

                    <div class="inputContain">
                        <input type="text" placeholder="Surname" name="accompName" maxlength="20">
                        <input type="text" placeholder="Membership Number" name="accompNum" maxlength="10">
                    </div>
                </div>

                <div id="guestRules">
                    <p>All visitors must adhere to the directions of the management of the club.</p>
                    <p>All visitors must be suitably attired.</p>
                    <p>This ticket must be carried whilst on club premises</p>
                    <p>This card is valid on the day of issue only</p>
                </div>

                <button type="submit">SUBMIT</button>
            </form>
        </section>

        <success-ticket :ticketID="ticketNumber" v-else />
    </div>
</template>

<script>
    import axios from 'axios';
    import SuccessTicket from './SuccessTicket.vue';
    
    export default {
        components: { SuccessTicket },
        data() {
            return {
                guestType: 1,
                checkInConfirmed: false,
                ticketNumber: '000000',
            }
        },
        methods: {
            async sendForm() {
                let fd = new FormData(document.getElementById('guestRegisterForm'));
            
                fd.append('guestType', this.guestType)

                await axios.post('/upload-form', fd)
                    .then((response) => {
                        if(response.status === 200) {
                            this.checkInConfirmed = true;
                            this.ticketNumber = response.data;
                        }
                    })
                    .catch((error) => alert("upload error: " + error));
            }
        },
    }
</script>

<style lang="scss" scoped>
    h1 {
        text-transform: uppercase;
        text-align: center;
        font-size: 1.4rem;
        margin: 1.5rem 0;
    }
    h3 {
        margin: 2rem 0 .5rem 0;
        font-weight: normal;
        text-align: center;
    }
    h4 { text-transform: uppercase }
    input {
        width: 100%;
        font-size: 1.1rem;
        margin: 5px 0;
        padding: .8rem 1rem;
    }
    button[type=submit] {
        width: 100%;
        margin-top: .5rem;
        padding: 1rem;
        border: none;
        color: #fff;
        background: #e60000;
        cursor: pointer;
    }

    #pageContainer {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        background: #d9d9d9 url('../../../public/assets/18-footers-splash.jpg') no-repeat center left / cover;
    }
    #formContainer {
        width: 30rem;
        max-width: 100%;
        margin: 2rem 1rem;
        padding: 1rem;
        background: #fff;
        border: 1px solid #999;
        box-shadow: 5px 5px 15px rgba(0,0,0, .6);
    }
    #guestRules {
        margin-top: 2rem;
        padding: .7rem;
        font-size: .8rem;
        background: #333;
        color: #e6e6e6;
        line-height: 1.5;
    }
    #logoInfo {
        height: 7rem;
        padding-left: 6.5rem;
        background: url('../../../public/assets/18-footers-logo.png') no-repeat center left / auto 100%;
        
        p:first-of-type {
            font-size: 1.3rem;
            font-weight: bold;
            font-family: 'Times New Roman', Times, serif;
        }
        p:nth-of-type(2) {
            font-size: .8rem;
            margin-bottom: .5rem;
            color: #999;
        }
    }

    .inputContain {
        display: flex;

        input:first-of-type { margin-right: .5rem }
        input { flex: 1 }
    }
    .guestCategory {
        position: relative;
        margin: 5px 0; 
        padding: .5rem .5rem .5rem 3rem;
        border: 1px solid #ccc;
        cursor: pointer;
        color: #999;

        p { 
            margin-top: .4rem;
            font-size: .8rem;
            line-height: 1.3;
        }
        h5 {
            margin-top: 1rem;
            text-align: center;
        }
        input[type=text] {
            font-size: .8rem;
            padding: .5rem 1rem;
        }
        .checkbox {
            position: absolute;
            top: 50%;
            left: .5rem;
            height: 1.7rem;
            width: 1.7rem;
            margin-right: 1rem;
            border-radius: 5px;
            background: #f2f2f2;
            transform: translatey(-50%);

            &.active { background: #17cb91 url('../../../public/assets/checkmark.svg') no-repeat center / auto 2.3rem }
        }

        &.active { 
            border: 1px solid transparent;
            background: #e6e6e6;
            color: #1a1a1a;
        }
    }
</style>