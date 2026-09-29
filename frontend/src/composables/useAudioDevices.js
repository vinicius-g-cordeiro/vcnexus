import { ref } from 'vue'

export function useAudioDevices() {
    const microphones = ref([])
    const selectedMicrophone = ref('')

    async function loadMicrophones() {
        if (!navigator.mediaDevices?.enumerateDevices) {
            microphones.value = []

            return []
        }

        try {
            const devices =
                await navigator.mediaDevices.enumerateDevices()

            microphones.value = devices.filter(
                device => device.kind === 'audioinput'
            )

            /*
             * Keep the currently selected device if it still exists.
             */
            const selectedExists =
                microphones.value.some(
                    device =>
                        device.deviceId ===
                        selectedMicrophone.value
                )

            if (
                !selectedExists &&
                microphones.value.length > 0
            ) {
                selectedMicrophone.value =
                    microphones.value[0].deviceId
            }

            return microphones.value
        } catch (error) {
            console.error(
                'Unable to enumerate microphones:',
                error
            )

            microphones.value = []

            return []
        }
    }

    async function requestMicrophonePermission() {
        if (!navigator.mediaDevices?.getUserMedia) {
            return false
        }

        let stream = null

        try {
            stream =
                await navigator.mediaDevices.getUserMedia({
                    audio: true,
                })

            await loadMicrophones()

            return true
        } catch (error) {
            console.error(
                'Unable to access microphone:',
                error
            )

            return false
        } finally {
            if (stream) {
                stream.getTracks().forEach(track => {
                    track.stop()
                })
            }
        }
    }

    async function initialize() {
        /*
         * First try to enumerate existing devices.
         */
        await loadMicrophones()

        /*
         * If labels are unavailable, request permission
         * and enumerate again.
         */
        const hasLabels =
            microphones.value.some(
                microphone =>
                    microphone.label.length > 0
            )

        if (!hasLabels) {
            await requestMicrophonePermission()
        }

        return microphones.value
    }

    function getSelectedMicrophoneConstraints() {
        if (!selectedMicrophone.value) {
            return {}
        }

        return {
            deviceId: {
                exact: selectedMicrophone.value,
            },
        }
    }

    return {
        microphones,
        selectedMicrophone,

        loadMicrophones,
        requestMicrophonePermission,
        initialize,
        getSelectedMicrophoneConstraints,
    }
}