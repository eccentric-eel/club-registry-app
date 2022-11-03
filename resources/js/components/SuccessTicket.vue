<template>
    <section>
        <h1>Check-in Confirmed</h1>
        <p>Your ticket number is <span id="ticketID">{{ ticketID }}</span></p>
        <p>
            Please keep this page for your records and be ready to show it upon request by 18 Footers staff.
            <i>This ticket is valid for today only.</i>
        </p>
    </section>
</template>

<script>
export default {
    props: {
        ticketID: String
    }
}
</script>

<style lang="scss" scoped>
    section {
        width: 30rem;
        max-width: 100%;
        margin: 1rem;
        padding: 12rem 2rem 2rem 2rem;
        background: #fff url('../../../public/assets/checkmark-green.svg') no-repeat top 2rem center / 10rem;
        box-shadow: 5px 5px 15px rgba(0,0,0, .6);
        border: 1px solid #999;
    }
    h1 {
        text-transform: uppercase;
        text-align: center;
        font-size: 1.4rem;
        margin: 1.5rem 0;
    }
    p {
        font-size: 1.2rem;
        line-height: 1.4;
        margin: 1rem 0;
    }

    #ticketID {
        font-size: 1.5rem;
        letter-spacing: 2px;
        padding-left: 10px;
    }
</style>