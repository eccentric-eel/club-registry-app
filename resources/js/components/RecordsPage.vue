<template>
    <RecordModal v-if="isActiveModal"
    :recordError="showRecordError"
    :recordID="selectedRecord"
    @hideModal="hideModal"
    @addRecord="addRecord"
    @deleteRecord="deleteRecord" />

    <div id="queryContain">
        <div id="mainLogo" @click="logout()"></div>
        <div :class="{ 'active': searchMode === 1 }" @click="searchMode = 1">General</div>
        <div :class="{ 'active': searchMode === 2 }" @click="searchMode = 2">Date</div>
    </div>

    <div id="searchContain">
        <input v-if="searchMode === 1" type="text" placeholder="Name / Address / Ticket No." v-model="inputText">

        <VueCtkDateTimePicker v-if="searchMode === 2"
        style="border: 1px solid #767676"
        input-size="sm"
        color="#ac3939"
        label="Start Date (DD/MM/YYYY)"
        format="DD-MM-YYYY"
        only-date
        no-label
        no-button
        v-model="inputStartDate" />

        <VueCtkDateTimePicker v-if="searchMode === 2"
        style="border: 1px solid #767676"
        input-size="sm"
        color="#ac3939"
        label="End Date (DD/MM/YYYY)"
        format="DD-MM-YYYY"
        only-date
        no-label
        no-button
        v-model="inputEndDate" />

        <button id="addRecordBtn" @click="showModal()"></button>
        <button id="exportBtn"    @click="exportData()"></button>
    </div>

    <div id="numRecordsContain" :class="{ 'active': records.length }">
        <span>{{totalRecords}} records (displaying {{fromRecords}} - {{toRecords}})</span>
    </div>

    <table>
        <tr>
            <th :class="{ active: (sortCol === 0) }" @click="setSortCol(0)">First Name    <span v-if="sortCol === 0" v-html="arrow"></span></th>
            <th :class="{ active: (sortCol === 1) }" @click="setSortCol(1)">Surname       <span v-if="sortCol === 1" v-html="arrow"></span></th>
            <th :class="{ active: (sortCol === 2) }" @click="setSortCol(2)">Address       <span v-if="sortCol === 2" v-html="arrow"></span></th>
            <th :class="{ active: (sortCol === 3) }" @click="setSortCol(3)">Guest Type    <span v-if="sortCol === 3" v-html="arrow"></span></th>       
            <th :class="{ active: (sortCol === 4) }" @click="setSortCol(4)">Member Name   <span v-if="sortCol === 4" v-html="arrow"></span></th>
            <th :class="{ active: (sortCol === 5) }" @click="setSortCol(5)">Member Number <span v-if="sortCol === 5" v-html="arrow"></span></th>
            <th :class="{ active: (sortCol === 6) }" @click="setSortCol(6)">Check-In Time <span v-if="sortCol === 6" v-html="arrow"></span></th>
            <th :class="{ active: (sortCol === 7) }" @click="setSortCol(7)">Ticket Number <span v-if="sortCol === 7" v-html="arrow"></span></th>
            <th></th>
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
                <td @click="showModal(record.id)"> Expand </td>
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
                selectedRecord: null,

                newRecord: {
                    fname:      '',
                    sname:      '',
                    address:    '',
                    accompName: '',
                    accompNum:  '',
                    guestType:  1,
                },

                isActiveModal:   false,
                showRecordError: false,
            
                inputText:      '',
                inputStartDate: '',
                inputEndDate:   '',

                searchMode: 1,

                sortAsc: true,
                sortCol: 0,

                records: [],
                links:   [],
            }
        },
        created() { this.updatePage('/api/query-records') },
        computed: {
            payloadObj() {
                return {
                    queryString: this.inputText,
                    startDate:   this.inputStartDate,
                    endDate:     this.inputEndDate,
                    sortCol:     this.sortCol,
                    sortAsc:     this.sortAsc,
                }
            },
            processedLinks() {
                let navLinks = [];

                this.links.forEach((link, index) => {
                    if(index === 0)                       { link.label = '‹' }
                    if(index === (this.links.length - 1)) { link.label = '›' }

                    navLinks.push(link)
                })

                return navLinks;
            }, 
            arrow() { return (this.sortAsc) ? '&#xFFEA' : '&#xFFEC' },
        },
        methods: {
            showModal(recordID) {
                this.isActiveModal = true; 
                this.selectedRecord = (recordID) ? recordID : null;
            },
            hideModal() { this.isActiveModal = false },
            addRecord(record) { 
                this.showRecordError = false;

                axios.post('/api/upload-form', record)
                     .then((response) => {
                        this.updatePage('/api/query-records');
                        this.hideModal();
                     })
                     .catch((error) => { this.showRecordError = true });
            },
            deleteRecord(recordID) {
                axios.post('/api/delete-record', recordID)
                    .then((response) => {
                        this.updatePage('/api/query-records');
                        this.hideModal();
                    })
                    .catch((error) => { this.showRecordError = true });
            },
            updatePage(url) {
                if(url) {
                    let tmpContext = this;
                    
                    axios.post(url, this.payloadObj)
                         .then(function (response) {
                            if(response.status === 200) {
                                tmpContext.records      = response.data.data;
                                tmpContext.links        = response.data.links;
                                tmpContext.totalRecords = response.data.total;
                                tmpContext.fromRecords  = response.data.from;
                                tmpContext.toRecords    = response.data.to;
                            }
                         })
                         .catch(function (error) { console.log(error) });
                }
            },
            logout() {
                if(confirm('Are you sure you wish to logout?')) {
                    axios.post('/api/logout').then(() => { this.$router.push('/admin') })   
                }
            },
            exportData() {
                axios.post('/api/export-records', { queryString: this.inputText }, { responseType: 'blob' })
                     .then((response) => {
                        let link = document.createElement('a');
                        
                        link.href = window.URL.createObjectURL(new Blob([response.data]));
                        link.setAttribute('download', `record-export-{data}.xlsx`);
                        link.style.visibility = 'hidden';

                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                     })
                     .catch((error) => alert("export error: " + error));
            },
            setSortCol(index) {
                if(this.sortCol === index)
                    this.sortAsc = !this.sortAsc;

                this.sortCol = index;
                this.updatePage('/api/query-records');
            }
        },
        watch: {
            inputText()      { this.updatePage('/api/query-records') },
            inputStartDate() { this.updatePage('/api/query-records') },
            inputEndDate()   { this.updatePage('/api/query-records') },
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

                &:last-of-type { 
                    cursor: pointer;
                    color: #999;
                    font-size: .7rem;
                }
            }
            th {   
                font-size: .7rem;
                padding: .5rem 0;
                user-select: none;
                font-weight: normal;
                color: #ccc;
                cursor: pointer;

                &:last-of-type { cursor: default }
                &.active { 
                    font-weight: bold;
                    color: #fff;
                }
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

    #mainLogo {
        height: 100%;
        width: 20rem;
    }

    #queryContain {
        display: flex;
        justify-content: right;
        align-items: center;
        height: 5rem;
        padding: 1rem;
        border-bottom: 1px solid #ccc;    
        background: #333;
    
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

            &:nth-of-type(1) {
                width: 12rem;
                margin-right: auto;
                background: url('../../../public/assets/18-footers-logo-wide.png') no-repeat center left 1rem / auto 4rem;
            }
            &:nth-of-type(2) { background: #d9d9d9 url('../../../public/assets/person-icon.svg') no-repeat top .5rem center / 2rem }
            &:nth-of-type(3) { background: #d9d9d9 url('../../../public/assets/calendar.svg') no-repeat top .5rem center / 2rem  }
            &.active { background-color: #fff }
        }
    }
    #addRecordBtn {
        background: url('../../../public/assets/add-record.svg') no-repeat center / auto 1.5rem;
        border: solid 1px #3366cc;
        border-radius: 3px;
    }
    #exportBtn {
        background: url('../../../public/images/excel.svg') no-repeat center / auto 1.5rem;
        border: solid 1px #207245;
        border-radius: 3px;
    }
    #searchContain {
        display: flex;
        width: 100%;
        justify-content: right;
        padding: .5rem;
        height: 3.2rem;
        gap: 10px;

        input[type=text] { 
            flex: 1;
            height: 2.2rem;
            padding: 0 1rem;
            border-radius: 4px;
            border: 1px solid #767676;
        }
        button {
            min-width: 3.5rem;
            width: 3.5rem;
            background-color: #f2f2f2;
            border-radius: 4px;
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
            min-width: 30px;
            line-height: 20px;
            background: #333;
            color: #fff;
            border-radius: 5px;
            border: solid 1px #767676;
            cursor: pointer;

            &.active {
                background: #fff;
                color: #333;
            }
            &:hover {
                background: #fff;
                color: #333;
            }
        }
    }
</style>