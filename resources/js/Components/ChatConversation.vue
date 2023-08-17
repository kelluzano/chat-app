<script setup>
import { usePage } from '@inertiajs/vue3';
import { reactive, ref, watch, onMounted, nextTick } from "vue";
import { debounce } from "lodash";
import axios from 'axios';

const props = defineProps(['selectedSession']);

const chatMessagesRef = ref(null);
const sendBtn = ref(true);
const messages = reactive(props.selectedSession.messages.data);

const form = reactive({
    session_id: parseInt(props.selectedSession.id),
    content: '',
    direction: "out",
    user_id: parseInt(usePage().props.auth.user.id)
});

const send = debounce(function () {

    axios.post(route('messages.send'), form)
        .then(function (response){
            messages.push(response.data);
            form.content = "";
        })
        .catch(function (error){
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
async function loadMoreMessages(){
    
    if (!props.selectedSession.messages.next_page_url) {
        console.log('Reached the last page of messages.');
        return;
    }

    if(currentPage.value <= props.selectedSession.messages.last_page){
        await axios.get(props.selectedSession.messages.next_page_url)
        .then(function (response){
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

onMounted(() => {
    scrollToBottom();
    loadMoreMessages();
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


</script>

<template>
    <div class="">
        <div class="card card-info direct-chat direct-chat-primary">
            <div class="card-header">
                <h3 class="card-title">{{ selectedSession.uniqueId }}</h3>
                <div class="card-tools">
                    <span data-toggle="tooltip" title="3 New Messages" class="badge badge-light">3</span>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-toggle="tooltip" title="Contacts"
                        data-widget="chat-pane-toggle">
                        <i class="fas fa-comments"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove"
                        @click="$emit('close-conversation', props.selectedSession);"><i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <!-- Conversations are loaded here -->
                <div class="direct-chat-messages" ref="chatMessagesRef">
                    <!-- Message. Default to the left -->
                    <div v-if="messages" v-for="message in messages" :key="message.id" class="direct-chat-msg"
                        :class="message.direction == 'out' ? 'right' : ''">
                        <div class="direct-chat-infos clearfix">
                            <span class="direct-chat-name"
                                :class="message.direction == 'out' ? 'float-right' : 'float-left'">{{ message.user ?
                                    message.user.name : selectedSession.uniqueId }}</span>
                            <span class="direct-chat-timestamp"
                                :class="message.direction == 'out' ? 'float-left' : 'float-right'">{{
                                    message.created_at_formatted }}</span>
                        </div>
                        <!-- /.direct-chat-infos -->
                        <img class="direct-chat-img"
                            :src="message.direction == 'out' ? '/dist/img/avatar3.png' : '/dist/img/avatar5.png'"
                            alt="message user image">
                        <!-- /.direct-chat-img -->
                        <div class="direct-chat-text"
                            :style="failedMessage(message)">
                            {{ message.content }}
                        </div>
                        <!-- /.direct-chat-text -->
                    </div>
                    <!-- /.direct-chat-msg -->

                </div>
                <!--/.direct-chat-messages-->

                <!-- /.direct-chat-pane -->
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
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
    </div>
</template>
