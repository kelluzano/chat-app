<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import MainLayout from "@/Layouts/MainLayout.vue";
import ChatConversation from "@/Components/ChatConversation.vue";
import ChatSideBar from "@/Components/ChatSideBar.vue";
import { ref, onMounted, watch } from 'vue';
import { debounce } from "lodash";
import axios from "axios";

const props = defineProps(['sessions'])
const scrollContainer = ref(null);
const sessionData = ref(props.sessions)
const selectedSessions = ref([]);

const form = useForm({
    search: ""
})

const handleScroll = debounce(() => {
    if (scrollContainer.value.scrollHeight - scrollContainer.value.scrollTop <= scrollContainer.value.clientHeight + 10) {
        let nextPage = sessionData.value.next_page_url;
        if (nextPage) {

            axios.get(nextPage)
                .then(function (response) {
                    sessionData.value = {
                        ...response.data,
                        data: [...sessionData.value.data, ...response.data.data],
                    }
                });
        }
    }
}, 500);


//Search method
watch(() => form.search, debounce(() => {
    axios.get(route('messages.index'), { params: {search: form.search} })
        .then(function (response) {
            sessionData.value = response.data
        });

}, 500));

onMounted(() => {
    $("#scrollableBody").on("scroll", function () {
        handleScroll();
    });
});


function handleSessionSelected(session) {

    if (!selectedSessions.value.some(s => s.id === session.id)) {

        axios.get(route('messages.get', session.uniqueId))
            .then((response) => {
                session.messages = response.data;
                selectedSessions.value.push(session);
            })
            .catch(function (error) {
                console.log(error);
            });
    }
}

function handleCloseConversation(session) {
    const index = selectedSessions.value.findIndex(s => s.id === session.id);
    if (index !== -1) {
        selectedSessions.value.splice(index, 1);
    }
}

</script>

<script>
export default {
    layout: MainLayout
}
</script>

<template>
    <Head title="Messages" />
    <div class="container-fluid">
        <div class="row mt-2">
            <div class="col-sm-6 col-md-4 d-flex flex-column">
                <div class="card card-outline card-primary">
                    <div class="card-body">
                        <div class="d-flex flex-column mt-1">
                            <input v-model="form.search" type="text" class="form-control" placeholder="Search...">
                            <hr>

                            <div id="scrollableBody" ref="scrollContainer" class="overflow-auto" style="height: 400px;">

                                <div v-if="sessionData.data" v-for="session in sessionData.data" :key="session.id">
                                    <ChatSideBar :session="session" @session-selected="handleSessionSelected" />
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-8">
                <div class="row">
                    <div v-for="selectedSession in selectedSessions" :key="selectedSession.id" class="col-md-6 col-lg-6">
                        <ChatConversation :selectedSession="selectedSession"
                            @close-conversation="handleCloseConversation" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
