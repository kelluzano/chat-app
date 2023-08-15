<script setup>
import { computed } from 'vue';
const props = defineProps(['selectedSession']);

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
                <div class="direct-chat-messages">
                    <!-- Message. Default to the left -->
                    <div v-if="selectedSession.messages" v-for="message in selectedSession.messages" :key="message.id"
                        class="direct-chat-msg" :class="message.direction == 'out' ? 'right' : ''">
                        <div class="direct-chat-infos clearfix">
                            <span class="direct-chat-name"
                                :class="message.direction == 'out' ? 'float-right' : 'float-left'">{{ message.user ? message.user.name : selectedSession.uniqueId }}</span>
                            <span class="direct-chat-timestamp"
                                :class="message.direction == 'out' ? 'float-left' : 'float-right'">23 Jan 2:00 pm</span>
                        </div>
                        <!-- /.direct-chat-infos -->
                        <img class="direct-chat-img" :src="message.direction == 'out' ? '/dist/img/avatar3.png' : '/dist/img/avatar5.png'" alt="message user image">
                        <!-- /.direct-chat-img -->
                        <div class="direct-chat-text">
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
                <form action="#" method="post">
                    <div class="input-group">
                        <input type="text" name="message" placeholder="Type Message ..." class="form-control">
                        <span class="input-group-append">
                            <button type="button" class="btn btn-primary">Send</button>
                        </span>
                    </div>
                </form>
            </div>
            <!-- /.card-footer-->
        </div>
</div></template>