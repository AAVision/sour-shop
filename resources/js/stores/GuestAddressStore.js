import axios from "axios";
import { defineStore } from "pinia";

export const useGuestAddress = defineStore("guestAddress", {
    state: () => ({
        name: null,
        email: null,
        country: null,
        phone_code: null,
        phone: null,
        area: null,
        flat_no: null,
        post_code: null,
        address_line: null,
        address_line2: null,
        address_type: null,
        langitude: null,
        longitude: null,
        errors: {}
    }),
    actions: {
        clearGuestAddress() {
            this.name = null;
            this.email = null;
            this.country = null;
            this.phone_code = null;
            this.phone = null;
            this.area = null;
            this.flat_no = null;
            this.post_code = null;
            this.address_line = null;
            this.address_line2 = null;
            this.langitude = null;
            this.longitude = null;
            this.errors = {};
        },
    },

});
