import { ref, onMounted } from "vue";

export default function showFlashMessage(message) {
    const isShowFlashMesasge = ref(false);

    onMounted(() => {
        if (message) {
            isShowFlashMesasge.value = true;

            setTimeout(() => {
                isShowFlashMesasge.value = false;
            }, 3000);
        }
    });

    return isShowFlashMesasge
}
