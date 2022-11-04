<template>
    <div id="modalContain" :class="{ 'active': isActiveModal }">
        <div id="modalInner">
            <button @click="hideModal">&#10006;</button>
            <h2>Manually Add Record</h2>
            <div style="display: flex; flex-direction: column; gap: 5px">
                <div style="display: flex; gap: 5px;">
                    <input v-model="newRecord.fname" type="text" :class="{ 'invalid': isActiveErrorMsg }" placeholder="First Name" required>
                    <input v-model="newRecord.sname" type="text" :class="{ 'invalid': isActiveErrorMsg }" placeholder="Surname" required>
                </div>
                <input v-model="newRecord.address" type="text" :class="{ 'invalid': isActiveErrorMsg }" placeholder="Address" required>

                <div style="display: flex; gap: 1.5rem; padding: 1rem 0;">

                    <div @click="newRecord.guestType = 1" style="display: flex; align-items: center;">
                        <input type="radio" :checked="newRecord.guestType === 1" name="guestType" value="tempMember">
                        <label style="margin-left: .5rem;" for="tempMember">Temporary Member</label>
                    </div>
                    <div @click="newRecord.guestType = 2" style="display: flex; align-items: center;">
                        <input type="radio"  :checked="newRecord.guestType === 2" name="guestType" value="memberGuest">
                        <label style="margin-left: .5rem;" for="memberGuest">Guest of a Member</label>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 5px;">
                    <input v-model="newRecord.accompName" :class="{ 'invalid': (isActiveErrorMsg && (newRecord.guestType === 2)) }" type="text" :disabled="(newRecord.guestType !== 2)" placeholder="Accompanying Member Name" required>
                    <input v-model="newRecord.accompNum" :class="{ 'invalid': (isActiveErrorMsg && (newRecord.guestType === 2)) }" type="text" :disabled="(newRecord.guestType !== 2)" placeholder="Accompanying Member Number" required>
                </div>
            </div>

            <div v-if="isActiveErrorMsg" id="newRecordError">
                Record contains invalid or empty data. Please fix and resubmit.
            </div>

            <button @click="addRecord()">Create Record</button>
        </div>
    </div>

    <div id="queryContain">
        <div :class="{ 'active': searchByParams.Surname }" @click="changeSearchParam('Surname')">Surname</div>
        <div :class="{ 'active': searchByParams.Date }"    @click="changeSearchParam('Date')">Date</div>
        <div :class="{ 'active': searchByParams.ID }"      @click="changeSearchParam('ID')">Ticket ID</div>
    </div>

    <div id="searchContain">
        <input type="text" :placeholder="placeholderText" v-model="inputText">
        <button id="addRecordBtn" @click="showModal()">+</button>
        <button id="exportBtn" @click="exportData()"></button>
    </div>

    <div id="numRecordsContain" :class="{ 'active': records.length }">
        <span>{{totalRecords}} records (displaying {{fromRecords}} - {{toRecords}})</span>
    </div>

    <table>
        <tr>
            <th>First Name</th>
            <th>Surname</th>
            <th>Address</th>
            <th>Guest Type</th>       
            <th>Member Name</th>
            <th>Member Number</th>
            <th>Check In Time</th>
            <th>Ticket Number</th>
        </tr>

        <template v-if="records.length > 0">
            <tr v-for="record in records">
                <td>{{ record.firstname }}</td>
                <td>{{ record.surname }}</td>
                <td>{{ record.address }}</td>
                <td>{{ record.guest_type }}</td>
                <td>{{ record.accompanying_member_name }}</td>
                <td>{{ record.accompanying_member_number }}</td>
                <td>{{ record.created_at }}</td>
                <td>{{ record.id }}</td>
            </tr>
        </template>
        
        <template v-else>
            <tr> <td class="noResults noBorder" colspan="100%">No Results Found</td> </tr>
        </template>
    </table>

    <div id="pagesContain">
        <button v-for="link in processedLinks" @click="updatePage(link.url)" :class="{'active': link.active}"> {{link.label}} </button>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                searchByParams: {
                    Surname: true,
                    Date:    false,
                    ID:      false,
                },
                newRecord: {
                    fname:      null,
                    sname:      null,
                    address:    null,
                    accompName: null,
                    accompNum:  null,
                    guestType:  1,
                },

                isActiveModal: false,
                isActiveErrorMsg: false,
            
                placeholderText: 'Filter by Surname',
                inputText: '',

                records: [],
                links:   [],
            }
        },
        created() { this.updatePage('./records/query-records') },
        computed: {
            processedLinks() {
                let navLinks = [];

                this.links.forEach((link, index) => {

                    if(index === 0)
                        link.label = '‹';
                    if(index === (this.links.length - 1))
                        link.label = '›';

                    navLinks.push(link)
                })

                return navLinks;
            }, 
            searchCategory() {
                if(this.searchByParams.Surname)
                    return 'surname';
                else if(this.searchByParams.Date)
                    return 'date';
                else if(this.searchByParams.ID)
                    return 'id';
            },
        },
        methods: {
            showModal() { this.isActiveModal = true },
            hideModal() { 
                this.isActiveModal = false;
                this.resetRecord();
            },
            addRecord() { 
                this.isActiveErrorMsg = false;

                if(this.validateRecord()) {
                    axios.post('/upload-form', this.newRecord)
                         .then((response) => {
                            this.updatePage('./records/query-records');
                            this.hideModal();
                         })
                         .catch((error) => alert("upload error: " + error));
                }
            },
            updatePage(url) {
                if(url) {
                    let tmpContext = this;
                    
                    axios.post(url, { queryString: this.inputText, category: this.searchCategory })
                         .then(function (response) {
                            if(response.status === 200) {
                                tmpContext.records = response.data.data;
                                tmpContext.links = response.data.links;
                                tmpContext.totalRecords = response.data.total;
                                tmpContext.fromRecords = response.data.from;
                                tmpContext.toRecords = response.data.to;
                            }
                         })
                         .catch(function (error) { console.log(error) });
                }
            },
            changeSearchParam(param) {
                for (const prop in this.searchByParams) { this.searchByParams[prop] = false }
    
                this.searchByParams[param] = true;
                this.placeholderText = `Filter by ${param}`;
                this.inputText = '';
            },
            validateRecord() {
                for(let prop in this.newRecord) {
                    if((prop === 'accompName' || prop === 'accompNum') && this.newRecord['guestType'] === 1) {
                        console.log('inside');
                        continue;
                    }

                    if(this.newRecord[prop] === null) {
                        this.isActiveErrorMsg = true;
                        return false;
                    }
                }
                return true;
            },
            resetRecord() {
                for(let prop in this.newRecord) { this.newRecord[prop] = null }

                this.newRecord.guestType = 1;
                this.isActiveErrorMsg = false;
            },
            exportData() {
                axios.post('./records/export-records', { queryString: this.inputText, category: this.searchCategory }, { responseType: 'blob' })
                     .then((response) => {
                        let link = document.createElement('a');
                        
                        link.href = window.URL.createObjectURL(new Blob([response.data]));
                        link.setAttribute('download', `record-export-{data}.xlsx`);
                        link.style.visibility = 'hidden';

                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                     })
                    //  .catch((error) => alert("export error: " + error));
            }
        },
        watch: {
            inputText(val) { this.updatePage('./records/query-records') }
        },
    }
</script>

<style lang="scss" scoped>
    table {
        border-spacing: 0;
        width: calc(100% - 1rem);
        margin: .5rem;
        font-size: .8rem;
    
        tr {
            td {
                border: 1px solid #ccc;
                border-top: none;
                padding-left: .5rem;
            }
            th {   
                font-size: .7rem;
                padding: .5rem 0;
            }

            &:not(:first-child):hover { background: #ebf0fa }
            &:nth-child(odd) { background: #f2f2f2 }
            &:first-child {
                background: #333;
                color: #fff;
                text-transform: uppercase;
                text-align: center;
            }
        }

        .noResults {
            text-align: center;
            padding: 1rem 0;
        }
        .noBorder { border: none }
    }
    #modalContain {
        display: none;
        position: fixed;
        height: 100vh;
        width: 100vw;
        background: rgba(0, 0, 0, 0.6);

        &.active { display: block }
    }
    #modalInner {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 30rem;
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

            &.invalid {
                background: #ffe6e6;
                border: 1px solid red;
            }
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
            cursor: pointer
        }
    }
    #queryContain {
        display: flex;
        justify-content: right;
        align-items: center;
        height: 5rem;
        padding: 1rem 1rem 1rem 12rem;
        border-bottom: 1px solid #ccc;    
        background: #333 url('../../../public/assets/18-footers-logo-wide.png') no-repeat center left 1rem / auto 4rem;
    
        div {
            display: flex;
            align-items: flex-end;
            justify-content: center;
            height: 4rem;
            width: 4rem;
            padding: 2px;
            margin: 0 5px;
            font-size: .7rem;
            border-radius: 3px;
            user-select: none;
            cursor: pointer;

            &:nth-of-type(1) { background: #d9d9d9 url('../../../public/assets/person-icon.svg') no-repeat top .5rem center / 2rem }
            &:nth-of-type(2) { background: #d9d9d9 url('../../../public/assets/calendar.svg') no-repeat top .5rem center / 2rem  }
            &:nth-of-type(3) { background: #d9d9d9 url('../../../public/assets/hash.svg') no-repeat top .8rem center / 1.5rem }
            &.active { background-color: #fff }
        }
    }
    #addRecordBtn {
        line-height: 2rem;
        font-size: 2rem;
        color: #333;
    }
    #exportBtn {
        background: url('../../../public/images/excel.svg') no-repeat center / auto 1.5rem;
    }
    #searchContain {
        display: flex;
        width: 100%;
        justify-content: right;
        padding: .5rem;
        height: 3.2rem;
        gap: 10px;

            input { 
                flex: 1;
                height: 100%;
                padding: 0 1rem;
            }
            button {
                width: 3.5rem;
                border: 1px solid #ccc;
                background-color: #f2f2f2;
                cursor: pointer;
            }
        
    }
    #numRecordsContain {
        display: flex;
        justify-content: right;
        padding: 0 .5rem;
        font-size: .9rem;
        margin-top: 1rem;
        color: #999;
        visibility: hidden;

        &.active { visibility: visible }
    }
    #pagesContain {
        display: flex;
        justify-content: right;
        margin: .5rem;
        gap: 5px;

        button {
            height: 30px;
            width: 30px;
            line-height: 20px;
            background: #333;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;

            &.active {
                background: #fff;
                color: #333;
            }
        }
    }
    #newRecordError {
        margin: 1rem 0;
        font-size: .9rem;
        text-align: left;
        color: red;
    }
</style>