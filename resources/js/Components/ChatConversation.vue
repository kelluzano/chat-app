<script setup>
import { usePage } from '@inertiajs/vue3';
import { reactive, ref, watch, onMounted, nextTick, computed, defineEmits } from "vue";
import { debounce } from "lodash";
import axios from 'axios';

const props = defineProps(['selectedSession']);

const chatCard = ref(null);
const chatMessagesRef = ref(null);
const sendBtn = ref(true);
const messages = reactive(props.selectedSession.messages.data);
const messageNotifications = ref(false);
const countNotSeenMessages = ref(props.selectedSession.message_not_seen.length);
const emit = defineEmits(['updateSessionData']);
const closeChatConversation = ref(null);

const form = reactive({
    session_id: parseInt(props.selectedSession.id),
    content: '',
    direction: "out",
    user_id: parseInt(usePage().props.auth.user.id)
});

const clientName = computed(() => {
    return props.selectedSession.client ? props.selectedSession.client.name : props.selectedSession.uniqueId;
});

const send = debounce(function () {

    axios.post(route('messages.send'), form)
        .then(function (response) {
            messages.push(response.data);
            form.content = "";
        })
        .catch(function (error) {
            console.log(error);
        })

}, 500)

watch(() => form.content, () => {
    if (form.content.trim() != '') {
        sendBtn.value = false;
    } else {
        sendBtn.value = true;
    }

});


const currentPage = ref(props.selectedSession.messages.current_page);
async function loadMoreMessages() {

    if (!props.selectedSession.messages.next_page_url) {
        console.log('Reached the last page of messages.');
        return;
    }

    if (currentPage.value <= props.selectedSession.messages.last_page) {
        await axios.get(props.selectedSession.messages.next_page_url)
            .then(function (response) {
                messages.push(...response.data.data)
                props.selectedSession.messages = response.data;
                currentPage.value = response.data.current_page;
            });
    }

}

const scrollToBottom = () => {
    if (chatMessagesRef.value) {
        nextTick(() => {
            chatMessagesRef.value.scrollTop = chatMessagesRef.value.scrollHeight;
        });
    }
};

const updateMessageSeenStatus = () => {
    let message_ids = props.selectedSession.message_not_seen.map(item => item.id);

    if(message_ids.length > 0){
        const response = axios.post(route('messages.update.seen'), { ids: message_ids });
        const updatedSessionData = { ...props.selectedSession };
        updatedSessionData.message_not_seen = [];
        emit('updateSessionData', updatedSessionData);
    }
    
}

onMounted(() => {
    scrollToBottom();
    loadMoreMessages();
    updateMessageSeenStatus();

    window.Echo.channel('new-message')
        .listen('ReceiveMessageEvent', function (e) {
            if (e.data.session.id === props.selectedSession.id) {
                messages.push(e.data.session.last_message_received);
                countNotSeenMessages.value = e.data.session.message_not_seen.length;
            }
        });

    $(chatCard.value).on("collapsed.lte.cardwidget", function () {
        messageNotifications.value = true;
    });

    $(chatCard.value).on("expanded.lte.cardwidget", function () {
        
        messageNotifications.value = false;
        scrollToBottom();

        if (countNotSeenMessages.value > 0) {
            countNotSeenMessages.value = 0;
            updateMessageSeenStatus();
        }
    });

});

watch(messages, () => {
    scrollToBottom();
    loadMoreMessages();
});

const failedMessage = (message) => {
    let failedColor = '#d76161';
    if (message.status === 'failed') {
        return {
            'background-color': failedColor,
            'border-color': failedColor,
        };
    }
};

const endSession = debounce((session_id) => {

    axios.post(route('session.end'), { session_id: session_id })
        .then(function (response) {

            $(document).Toasts('create', {
                //title: response.data.toast.title,
                body: response.data.toast.body,
                class: response.data.toast.class,
                autohide: true,
                delay: 3000,
                close: false,
            });

            const updatedSessionData = { ...props.selectedSession };
            updatedSessionData.close_date = response.data.date;
            emit('updateSessionData', updatedSessionData);

            closeChatConversation.value.click();
        });
}, 500);

const messagesWithSessions = computed(() => {
    const messagesWithSessions = [];

    let currentSessionId = null;
    let currentSessionCloseDate = null;
    let sessionStarted = false;
    $.each(messages, function (key, message) {
        const sessionId = message.session_id;
        const sessionCloseDate = message.session ? message.session.close_date : null;

        if (sessionId !== currentSessionId) {
            if (sessionStarted) {
                messagesWithSessions.push({
                    type: "end",
                    message: `Session ${currentSessionId} Ended.`,
                    date: message.created_at_formatted,
                    session_id: message.session_id
                });
            }

            messagesWithSessions.push({
                type: "start",
                message: `Session ${message.session_id} Started.`,
                date: message.created_at_formatted,
                session_id: message.session_id
            });

            sessionStarted = true;
            currentSessionId = sessionId;
            currentSessionCloseDate = sessionCloseDate;
        }

        messagesWithSessions.push(message);

    });

    if (sessionStarted && currentSessionCloseDate) {
        messagesWithSessions.push({
            type: "end",
            message: `Session ${currentSessionId} Ended.`,
        });
    }

    return messagesWithSessions;
})

</script>

<template>
    <div class="card card-outline card-info direct-chat direct-chat-primary" ref="chatCard">
        <div class="card-header">
            <h3 class="card-title text-truncate w-50" :title="clientName">{{ clientName }}</h3>
            <div class="card-tools">
                <span v-if="messageNotifications" data-toggle="tooltip" :title="countNotSeenMessages"
                    class="badge badge-light">{{ countNotSeenMessages }}</span>
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Minimize">
                    <i class="fas fa-minus"></i>
                </button>
                <button v-if="!props.selectedSession.close_date" type="button" class="btn btn-tool" data-toggle="tooltip" title="Update"
                    data-widget="chat-pane-toggle">
                    <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove" title="Close"
                    ref="closeChatConversation" @click="$emit('close-conversation', props.selectedSession);"><i
                        class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <!-- Conversations are loaded here -->
            <div class="direct-chat-messages" ref="chatMessagesRef">
                <!-- Message. Default to the left -->
                <div v-if="messages" v-for="message in messagesWithSessions" :key="message.id" class="direct-chat-msg"
                    :class="message.direction == 'out' ? 'right' : ''">

                    <div v-if="message.type === 'start'" class="hr-sect">
                        <span>
                            {{ message.message }}
                        </span>
                    </div>

                    <div v-else-if="!message.type">
                        <div class="direct-chat-infos clearfix">
                            <span class="direct-chat-name"
                                :class="message.direction == 'out' ? 'float-right' : 'float-left'">{{
                                    message.user ?
                                    message.user.name : clientName }}</span>
                            <span class="direct-chat-timestamp"
                                :class="message.direction == 'out' ? 'float-left' : 'float-right'">{{
                                    message.created_at_formatted }}</span>
                        </div>
                        <!-- /.direct-chat-infos -->
                        <img class="direct-chat-img"
                            :src="message.direction == 'out' ? '/dist/img/avatar3.png' : '/dist/img/avatar5.png'"
                            alt="message user image">
                        <!-- /.direct-chat-img -->
                        <div class="direct-chat-text" :style="failedMessage(message)"
                            @click="message.status == 'failed' ? $emit('send-retry', message.id) : ''">
                            {{ message.content }}
                        </div>
                    </div>

                    <div v-else-if="message.type === 'end'" class="hr-sect">
                        <span>
                            {{ message.message }}
                        </span>
                    </div>

                    <!-- /.direct-chat-text -->
                </div>
                <!-- /.direct-chat-msg -->

            </div>
            <!--/.direct-chat-messages-->

            <div class="direct-chat-contacts" style="background-color: aliceblue">

                <div class="d-flex flex-column p-3 text-dark justify-content-center">
                    <p class="text-sm">Disposition</p>
                    <input type="text" class="form-control">
                    <button class="btn btn-sm btn-rounded btn-primary mt-2">Submit</button>

                    <button v-if="!props.selectedSession.close_date" @click="endSession(props.selectedSession.id)"
                        class="btn btn-sm btn-success btn-rounded mt-5">End session</button>
                </div>
            </div>
        </div>
        <!-- /.card-body -->
        <div v-if="!props.selectedSession.close_date" class="card-footer">
            <form @submit.prevent="send">
                <div class="input-group">
                    <input type="text" v-model="form.content" placeholder="Type Message ..." class="form-control">
                    <span class="input-group-append">
                        <button type="submit" class="btn btn-primary" :disabled="sendBtn">Send</button>
                    </span>
                </div>
            </form>
        </div>
        <!-- /.card-footer-->
    </div>
</template>

<style scoped>
.hr-sect {
    display: flex;
    flex-basis: 100%;
    align-items: center;
    color: rgba(0, 0, 0, 0.35);
    margin: 8px 0px;
    font-weight: bold;
}

.hr-sect:before,
.hr-sect:after {
    content: "";
    flex-grow: 1;
    background: rgba(0, 0, 0, 0.35);
    height: 1px;
    font-size: 0px;
    line-height: 0px;
    margin: 0px 8px;
}
</style>