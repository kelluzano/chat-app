<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import MainLayout from "@/Layouts/MainLayout.vue";
import ChatConversation from "@/Components/ChatConversation.vue";
import ChatSideBar from "@/Components/ChatSideBar.vue";
import { ref, onMounted, watch } from 'vue';
import { debounce } from "lodash";

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
            $.ajax({
                url: nextPage,
                type: "GET",
                dataType: "json",
                success: function (response) {
                    sessionData.value = {
                        ...response,
                        data: [...sessionData.value.data, ...response.data],
                    }

                }
            });
        }
    }
}, 500);


//Search method
watch(() => form.search, debounce(() => {
    console.log(form.search);
}, 500));

onMounted(() => {
    $("#scrollableBody").on("scroll", function () {
        handleScroll();
    });
});


function handleSessionSelected(session) {
    if (!selectedSessions.value.some(s => s.id === session.id)) {
        selectedSessions.value.push(session);
    }
}

function handleCloseConversation(session) {
    const index = selectedSessions.value.findIndex(s => s.id === session.id);
    if (index !== -1) {
        selectedSessions.value.splice(index, 1);
    }

    console.log(selectedSessions.value);
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
            <div class="col-sm-6 col-md-3 d-flex flex-column">
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

            <div class="col-sm-6 col-md-9">
                <div class="row">
                    <div v-for="selectedSession in selectedSessions" :key="selectedSession.id" class="col-md-4">
                        <ChatConversation :selectedSession="selectedSession"
                            @close-conversation="handleCloseConversation" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
