import api from '@/services/apiService'

const referencesService = {
    async fetchCountries(params) {
        const response = await api.query('/references/countries/', { params })
        return response.data
    },
    async fetchStates(params, countryId) {
        const response = await api.query(`/references/countries/${countryId}/states/`, { params })
        return response.data
    },

    async fetchCities(params, stateId, countryId) {
        const response = await api.query(`/references/countries/${countryId}/states/${stateId}/cities/`, { params })
        return response.data
    },
    async fetchMaritalStatuses(params) {
        const response = await api.query('/references/marital-statuses/', { params })
        return response.data
    },
    async fetchGenders(params) {
        const response = await api.query('/references/genders/', { params })
        return response.data
    },
    async fetchDisabilities(params) {
        const response = await api.query('/references/disabilities/', { params })
        return response.data
    },
    async fetchEducationalLevels(params) {
        const response = await api.query('/references/educational-levels/', { params })
        return response.data
    },
    async fetchEducationalTypes(params) {
        const response = await api.query('/references/educational-types/', { params })
        return response.data
    },
    async fetchCompletionStatuses(type){
        const response = await api.query(`/references/completion-statuses/${type}/`)
        return response.data
    },
    
    async fetchReligions(params) {
        const response = await api.query('/references/religions/', { params })
        return response.data
    },

    async fetchSexualOrientations(params) {
        const response = await api.query('/references/sexual-orientations/', { params })
        return response.data
    },

    async fetchEthnicities(params) {
        const response = await api.query('/references/ethnicities/', { params })
        return response.data
    },

    async fetchNationalities(params) {
        const response = await api.query('/references/nationalities/', { params })
        return response.data
    },

    async fetchContactCategories(params) {
        const response = await api.query('/references/contact-categories/', { params })
        return response.data
    },

    async fetchContactTypes(params) {
        const response = await api.query('/references/contact-types/', { params })
        return response.data
    },
    async fetchSubscriptionStatuses(params){
        const response = await api.query('/references/subscription-statuses/', { params })
        return response.data
    },
    async fetchSubscriptionPlans(params){
        const response = await api.query('/references/subscription-plans/', { params })
        return response.data
    }
}

export default referencesService