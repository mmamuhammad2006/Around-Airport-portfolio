<template>
    <div
        v-if="isVisible"
        class="loader-overlay">
        <div class="loader"/>
        <span
            class="text"
            v-html="text"/>
    </div>
</template>

<script>
export default {
    props: {
        isVisible:
            {
                type: Boolean,
                required: true
            },
        text:
            {
                type: String,
                required: false,
                default: ""
            },
    }
}
</script>

<style lang="scss" scoped>
.loader-overlay {
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.7);
    z-index: 999;
    cursor: pointer;

    span.text {
        display: inline-block;
        position: relative;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #fff;
    }

    .loader {
        animation: loader-animate 1.5s linear infinite;
        clip: rect(0, 80px, 80px, 40px);
        height: 80px;
        width: 80px;
        position: absolute;
        left: calc(50% - 40px);
        top: calc(50% - 40px);

        &:after {
            animation: loader-animate-after 1.5s ease-in-out infinite;
            clip: rect(0, 80px, 80px, 40px);
            content: '';
            border-radius: 50%;
            height: 80px;
            width: 80px;
            position: absolute;
        }
    }

    @keyframes loader-animate {
        0% {
            transform: rotate(0deg)
        }
        100% {
            transform: rotate(220deg)
        }
    }
    @keyframes loader-animate-after {
        0% {
            box-shadow: inset #fff 0 0 0 17px;
            transform: rotate(-140deg);
        }
        50% {
            box-shadow: inset #fff 0 0 0 2px;
        }
        100% {
            box-shadow: inset #fff 0 0 0 17px;
            transform: rotate(140deg);
        }
    }
}
</style>
