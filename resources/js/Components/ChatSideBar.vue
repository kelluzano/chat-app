<script setup>
import { computed, onMounted } from 'vue';
const props = defineProps(['session']);

function getRandomAvatar() {
    const minAvatarNumber = 1;
    const maxAvatarNumber = 5;
    var randomAvatarNumber = Math.floor(Math.random() * (maxAvatarNumber - minAvatarNumber + 1)) + minAvatarNumber;

    if (randomAvatarNumber == 1) {
        randomAvatarNumber = "";
    }

    const avatarURL = `/dist/img/avatar${randomAvatarNumber}.png`;
    return avatarURL;
}

const formattedCreatedAt = computed(() => {
    let date = new Date(props.session.created_at)
    return date.toLocaleString();
});

const clientName = computed(() => {
    return props.session.client ? props.session.client.name : props.session.uniqueId ;
});

const lastMessageDateReceived = computed(() => {
    return {
        date: props.session.last_message_received ? props.session.last_message_received.created_at_formatted : "N/A",
        title: props.session.last_message_received ? props.session.last_message_received?.created_at : "N/A",
    }

});


</script>
<template>
    <div class="d-flex p-3 shadow border border-light rounded-lg mh-50" @click="$emit('session-selected', props.session);">
        <div>
            <img :src="getRandomAvatar()" class="rounded-circle shadow shadow-sm" alt="User"
                style="height: 50px; width: 50px;">
        </div>
        <div class="d-flex flex-grow-1 flex-column mx-3 w-50">
            <span class="font-weight-bold text-md">{{ clientName }}</span>
            <span class="text-sm">
                <small :title="new Date(lastMessageDateReceived.title).toLocaleString()">
                    {{ lastMessageDateReceived.date }}
                </small>
            </span>
            <div v-if="props.session.message_not_seen.length > 0" class="badge badge-info">New Message</div>
        </div>
        <div class="d-flex flex-column align-items-center">
            <span v-if="!props.session.close_date" class="rounded-circle text-sm bg-success d-inline-block mb-1" style="width: 15px; height: 15px;"></span>
            <img src="/dist/img/viber.png" style="height: 20px; width: 20px;">

        </div>
    </div>
</template>
