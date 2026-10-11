/**
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 * @version 1.0.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 */

import { defineStore } from "pinia";
import referencesService from "@/services/references/referencesService";

export const useReferencesStore = defineStore("references", {
  state: () => ({
    loading: false,
    loadingMessage: "Loading...",
    error: null,
    message: null,
    countries: null,
    states: null,
    cities: null,
    marital_statuses: null,
    genders: null,
    religions: null,
    educational_levels: null,
    disabilities: null,
    sexual_orientations: null,
    ethnicities: null,
    nationalities: null,
    educational_types: null,
    completion_statuses: null,
    contact_types: null,
    contact_categories: null,
    subscription_plans: [],
    subscription_statuses: []
  }),
  actions: {
    async fetchCountries(params) {
      this.loading = true;
      this.error = null;
      try {
        const response = await referencesService.fetchCountries(params);
        this.countries = response.data.list;
        return true;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },
    async fetchStates(params, countryId) {
      this.loading = true;
      this.error = null;
      try {
        const response = await referencesService.fetchStates(params, countryId);
        this.states = response.data.list;
        return true;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },
    async fetchCities(params, stateId, countryId) {
      this.loading = true;
      this.error = null;
      try {
        const response = await referencesService.fetchCities(params, stateId, countryId);
        this.cities = response.data.list;
        return true;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },
    async fetchMaritalStatuses(params) {
      this.loading = true;
      this.error = null;
      try {
        const response = await referencesService.fetchMaritalStatuses(params);
        this.marital_statuses = response.data.list;
        return true;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },
    async fetchGenders(params) {
      this.loading = true;
      this.error = null;
      try {
        const response = await referencesService.fetchGenders(params);
        this.genders = response.data.list;
        return true;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },

    async fetchReligions(params) {
      this.loading = true;
      this.error = null;
      try {
        const response = await referencesService.fetchReligions(params);
        this.religions = response.data.list;
        return true;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },

    async fetchEducationalLevels(params) {
      this.loading = true;
      this.error = null;
      try {
        const response = await referencesService.fetchEducationalLevels(params);
        this.educational_levels = response.data.list;
        return true;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },
    async fetchEducationalTypes(params = {}) {
      this.loading = true;
      this.error = null;
      try {
        const response = await referencesService.fetchEducationalTypes(params);
        this.educational_types = response.data.list;
        return true;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },

    async fetchCompletionStatuses(type) {
      this.loading = true;
      this.error = null;
      try {
        const response = await referencesService.fetchCompletionStatuses(type);
        this.completion_statuses = response.data.list;
        return true;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },

    async fetchDisabilities(params) {
      this.loading = true;
      this.error = null;
      try {
        const response = await referencesService.fetchDisabilities(params);
        this.disabilities = response.data.list;
        return true;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },

    async fetchSexualOrientations(params) {
      this.loading = true;
      this.error = null;
      try {
        const response = await referencesService.fetchSexualOrientations(params);
        this.sexual_orientations = response.data.list;
        return true;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },

    async fetchEthnicities(params) {
      this.loading = true;
      this.error = null;
      try {
        const response = await referencesService.fetchEthnicities(params);
        this.ethnicities = response.data.list;
        return true;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }      
    },

    async fetchNationalities(params) {
      this.loading = true;
      this.error = null;
      try {
        const response = await referencesService.fetchNationalities(params);
        this.nationalities = response.data.list;
        return true;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },
    async fetchContactCategories(params) {
      this.loading = true;
      this.error = null;
      try {
        const response = await referencesService.fetchContactCategories(params);
        this.contact_categories = response.data.list;
        return true;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },
    async fetchContactTypes(params) {
      this.loading = true;
      this.error = null;
      try {
        const response = await referencesService.fetchContactTypes(params);
        this.contact_types = response.data.list;
        return true;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },
    async fetchSubscriptionPlans(params){
      this.loading = true;
      this.error = null;
      try {
        const response = await referencesService.fetchSubscriptionPlans(params);
        this.subscription_plans = response.data.list;
        return true;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },
    async fetchSubscriptionStatuses(params){
      this.loading = true;
      this.error = null;
      try {
        const response = await referencesService.fetchSubscriptionStatuses(params);
        this.subscription_statuses = response.data.list;
        return true;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    }
  },
})


