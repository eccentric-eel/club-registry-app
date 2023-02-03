<template>
    <div id="modalContain">
        <div id="modalInner">
            <button @click="$emit('hideModal')">&#10006;</button>

            <h2 v-if="recordID">Existing Record - #{{ recordID }}</h2>
            <h2 v-else>Manual Record Entry</h2>

            <div v-if="isActiveErrorMsg" id="newRecordError">{{ errorText }}</div>

            <div id="recordForm">
                <div id="checkInContain">
                    <span>Check-in Time:</span> {{ (recordID) ? record.timestamp : currentTimestamp }}
                </div>

                <div class="name">
                    <input v-model="record.fname" type="text" :class="{ 'invalid': !isValidfname }" :disabled="recordID" placeholder="First Name" required>
                    <input v-model="record.sname" type="text" :class="{ 'invalid': !isValidsname }" :disabled="recordID" placeholder="Surname"    required>
                </div>
                <input v-model="record.address" type="text" :class="{ 'invalid': !isValidaddress }" :disabled="recordID" placeholder="Current Address" required>

                <div class="guestType">
                    <div @click="updateGuestType(1)">
                        <input type="radio" :checked="record.guestType === 1" :disabled="recordID" name="guestType" value="tempMember">
                        <label for="tempMember">Temporary Member</label>
                    </div>
                    <div @click="updateGuestType(2)">
                        <input type="radio" :checked="record.guestType === 2" :disabled="recordID" name="guestType" value="memberGuest">
                        <label for="memberGuest">Guest of a Member</label>
                    </div>
                </div>

                <div class="accompGuest">
                    <input v-model="record.accompName" :class="{ 'invalid': !isValidaccompName }" type="text" :disabled="(record.guestType !== 2) || recordID" placeholder="Accompanying Member Name" required>
                    <input v-model="record.accompNum"  :class="{ 'invalid': !isValidaccompNum }"  type="text" :disabled="(record.guestType !== 2) || recordID" placeholder="Accompanying Member Number" required>
                </div>
            </div>

            <button v-if="recordID" @click="$emit('deleteRecord', recordID)" class="delete">Delete Record</button> 
            <button v-else @click="validateRecord()">Create Record</button>            
        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        props: ['recordError', 'recordID'],
        data() {
            return {
                record: {
                    fname:      '',
                    sname:      '',
                    address:    '',
                    accompName: '',
                    accompNum:  '',
                    guestType:  1,
                    timestamp:  null,
                },
                isActiveErrorMsg: false,
                errorText: 'Record contains invalid or empty data. Please fix and resubmit.',
            }
        },
        mounted() {
            if(this.recordID) {
                this.getSingleRecord();
            }
        },
        computed: {
            isValidfname()      { return (this.isActiveErrorMsg && !this.record.fname)   ? false : true },
            isValidsname()      { return (this.isActiveErrorMsg && !this.record.sname)   ? false : true },
            isValidaddress()    { return (this.isActiveErrorMsg && !this.record.address) ? false : true },
            isValidaccompName() { return (this.isActiveErrorMsg && this.record.guestType === 2 && !this.record.accompName) ? false : true },
            isValidaccompNum()  { return (this.isActiveErrorMsg && this.record.guestType === 2 && !this.record.accompNum)  ? false : true },
            
            currentTimestamp() {
                let timestamp  = new Date();
                let options = { 
                    weekday: "long",
                    year:    "numeric",
                    month:   "numeric",
                    day:     "numeric",
                    hour:    "numeric",
                    minute:  "numeric",
                    second:  "numeric",
                };
                return timestamp.toLocaleString("en-AU", options);
            },
        },
        methods: {
            validateRecord() {
                if(!this.recordID) {
                    if((this.record.fname && this.record.sname && this.record.address) &&
                    (this.record.guestType === 1 || (this.record.guestType === 2 && (this.record.accompName && this.record.accompNum)))) {

                        this.isActiveErrorMsg = false;
                        this.$emit('addRecord', this.record);
                    }   
                    else {
                        this.errorText = 'Record contains invalid or empty data. Please fix and resubmit.'
                        this.isActiveErrorMsg = true;
                    }
                }
            },
            getSingleRecord() {
                let tmpContext = this;

                axios.get(`/api/single-record/${this.recordID}`)
                     .then(function (response) {
                        if(response.status === 200) {
                            tmpContext.record.fname      = response.data.firstname;
                            tmpContext.record.sname      = response.data.surname;
                            tmpContext.record.address    = response.data.address;
                            tmpContext.record.guestType  = (response.data.guest_type === 'Temporary Member') ? 1 : 2;
                            tmpContext.record.accompName = response.data.accompanying_member_name;
                            tmpContext.record.accompNum  = response.data.accompanying_member_number;
                            tmpContext.record.timestamp  = response.data.created_at;
                        }
                     })
                     .catch(function (error) { console.log(error) });
            },
            updateGuestType(typeID) { this.record.guestType = (!this.recordID) ? typeID : this.record.guestType },
        },
        watch: {
            recordError(val) {
                if(val) { 
                    this.isActiveErrorMsg = true;
                    this.errorText = 'An unknown server error has occured. Please try again later.';
                }
            },
            'record.guestType': function(val) {
                if(val !== 2) {
                    this.record.accompName = '';
                    this.record.accompNum  = '';
                }
            }
        }
    }
</script>

<style lang="scss" scoped>
    #modalContain {
        display: block;
        position: fixed;
        height: 100vh;
        width: 100vw;
        background: rgba(0, 0, 0, 0.6);
        z-index: 10;
    }
    #modalInner {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 35rem;
        max-width: 90%;
        padding: 2rem;
        border: 1px solid #666;
        background: #fff;
        text-align: center;
        transform: translate(-50%, -50%);

        h2 { padding-bottom: 1rem }
        input { 
            width: 100%;
            height: 2.5rem;
            padding: 0 1rem;
            font-size: .9rem;
        }
        input[type=radio] {
            height: 1.5rem;
            width: 1.5rem;
            padding: 0;
            cursor: pointer
        }

        button:first-of-type {
            position: absolute;    
            top: 0;
            right: 0;
            height: 2rem;
            width: 2rem;
            font-size: 1.2rem;
            line-height: 1.8rem;
            font-weight: bold;
            text-align: center;
            border: none;
            background: transparent;
            color: red;
            cursor: pointer;
        }
        button:last-of-type {
            float: right;
            border: none;
            border-radius: 2px;
            background: #3366cc;
            font-size: .9rem;
            color: #fff;
            height: 2.5rem;
            padding: 0 1rem;
            margin-top: 1rem;
            cursor: pointer;

            &.delete { background: #ff3333 }
        }
    }
    #recordForm {
        display: flex;
        flex-direction: column;
        gap: 5px;

        .name, .guestType, .accompGuest { 
            display: flex;
            gap: 5px;
        }
        .guestType {
            gap: 1.5rem;
            padding: 1rem 0;

            div {
                display: flex;
                align-items: center;

                label { margin-left: .5rem }
            }
        }
        .accompGuest { flex-direction: column }
    }
    #checkInContain {
        text-align: left;
        font-size: .9rem;

        span {
            font-weight: bold;
            padding-right: 1rem;
        }
    }
    #newRecordError {
        margin-bottom: 1rem;
        text-align: left;
        color: red;
    }
</style>