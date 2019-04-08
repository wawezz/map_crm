import axios from "axios";
import moment from "moment";
import {
  updatedDiff
} from "deep-object-diff";

import AuthService from "./../services/AuthService";

export const placeLeads = {
  data() {
    return {
      placeLeadsFilter: '[]',
      placeLeadsSort: '[]',
      placeLeadsLoading: false,
      placeLeadsData: {
        checked: []
      },
      placeLeadsSearchString: '',
      placeLeadInfo: {},
      placeLeadsOriginal: {},
      placeLeads: {},
      placeLeadsTotalCount: 0,
      multiUpdatePlaceLeads: false,
      placeLeadsform: {
        id: null,
        placeId: '',
        name: '',
        address: '',
        status: 1,
        phone: '',
        type: '',
        price: 0,
        rating: 0,
        review: '',
        website: '',
        geo: '',
        data: '{}',
        toSync: 0,
        campaignCode: 0,
        isImportant: 0,
        zipCode: '',
        city: '',
        alexaRank: 0,
        onlineSince: '',
        ypReviews: 0,
        multiLocation: 0,
        lastRemark: '',
        bbbRating: 0,
        ypRating: 0,
        dataScore: 0,
        carrier: '',
        callerIdName: '',
        rn: 0,
        contractAt: null,
        nextFollowupDate: null
      },
      placeLeadsColumns: [{
          type: 'checkbox'
        },
        {
          name: 'name',
          type: 'link',
          prefix: '/place-lead/'
        },
        {
          name: 'address'
        },
        {
          name: 'city'
        },
        {
          name: 'zip code',
          field: 'zipCode',
          sortValue: 'zipCode'
        },
        {
          name: 'last update',
          field: 'updatedAt',
          type: 'date',
          sortValue: 'updatedAt'
        },
        {
          name: 'type'
        },
        {
          name: 'status',
          field: 'statusName',
          updateble: true,
          type: 'select'
        },
        {
          name: 'carrier'
        },
        {
          name: 'data score',
          field: 'dataScore',
          sortValue: 'dataScore'
        },
        {
          name: 'notes',
          field: 'notesCount',
          disableSort: true
        },
        {
          name: 'tasks',
          field: 'tasksCount',
          disableSort: true
        },
        {
          name: 'phone'
        }
      ],
      placeLeadsFilterObject: {
        carrier: {
          value: '',
          condition: 'LIKE'
        },
        rn: {
          value: '',
          condition: '='
        },
        callerIdName: {
          value: '',
          condition: 'NULL'
        },
        dataScore: {
          value: '',
          condition: '='
        },
        type: {
          value: '',
          condition: 'LIKE'
        },
        city: {
          value: '',
          condition: 'LIKE'
        },
        status: {
          value: '',
          condition: '='
        },
        multiLocation: {
          value: 0,
          condition: '!=',
          default: 0
        },
        bbbRating: {
          value: 0,
          condition: '!=',
          default: 0
        },
        ypRating: {
          value: 0,
          condition: '!=',
          default: 0
        },
        rating: {
          value: 0,
          condition: '!=',
          default: 0
        },
        alexaRank: {
          value: 0,
          condition: '!=',
          default: 0
        },
        updatedAt: {
          from: '',
          to: '',
          condition: 'BETWEEN',
          isDate: true
        }
      },
      placeLeadsLimit: 25
    }
  },
  computed: {
    placeLeadsPage() {
      let page = 1;
      if (this.$route.params.page) page = this.$route.params.page;

      return parseInt(page);
    }
  },
  methods: {
    getPlaceLeads() {
      this.placeLeadsLoading = true;
      const filter = this.placeLeadsFilter !== '[]' ? JSON.stringify(this.placeLeadsFilter) : this.placeLeadsFilter;
      const sort = this.placeLeadsSort !== '[]' ? JSON.stringify(this.placeLeadsSort) : this.placeLeadsSort;
      const query = this.placeLeadsSearchString || '';
      axios({
          method: "post",
          url: "/v1/place-lead/list?filter=" + filter + "&sort=" + sort + "&limit=" + this.placeLeadsLimit + "&offset=" + (this.placeLeadsLimit * (this.placeLeadsPage - 1) + "&query=" + query),
          data: {
            params: {
              id: AuthService.uid
            }
          },
          headers: {
            Authorization: `Bearer ${AuthService.tokens.access}`
          }
        })
        .then(response => {
          this.placeLeadsOriginal = JSON.parse(JSON.stringify(response.data));
          this.placeLeads = response.data;
          this.placeLeadsTotalCount = parseInt(response.headers['x-pagination-total']);
          this.placeLeadsLoading = false;
        })
        .catch(e => {
          const data = e.response.data;

          if (data.error) {
            if (data.error.code === 418 && data.error) {
              this.listError = data.error.message;
            } else if (data.error.code === 403 && data.error) {
              this.listError = "You are not allowed to access this content.";
            }
          } else {
            console.error("Unexpected error", data.error);
          }
          this.placeLeadsLoading = false;
        });
    },
    async getPlaceLeadByPlaceId() {
      this.placeLeadsLoading = true;
      await axios({
          method: "post",
          url: "/v1/place-lead/get-by-place-id?placeId=" + this.$route.params.id,
          data: {
            params: {
              id: AuthService.uid
            }
          },
          headers: {
            Authorization: `Bearer ${AuthService.tokens.access}`
          }
        })
        .then(response => {
          this.placeLeadInfo = response.data;
          this.placeLeadsform.placeId = this.placeLeadInfo.placeId;
          this.placeLeadsform.id = this.placeLeadInfo.id;
          this.placeLeadsform.name = this.placeLeadInfo.name;
          this.placeLeadsform.address = this.placeLeadInfo.address;
          this.placeLeadsform.status = this.placeLeadInfo.status;
          this.placeLeadsform.phone = this.placeLeadInfo.phone;
          this.placeLeadsform.type = this.placeLeadInfo.type;
          this.placeLeadsform.price = this.placeLeadInfo.price;
          this.placeLeadsform.rating = this.placeLeadInfo.rating;
          this.placeLeadsform.review = this.placeLeadInfo.review;
          this.placeLeadsform.website = this.placeLeadInfo.website;
          this.placeLeadsform.geo = this.placeLeadInfo.geo;
          this.placeLeadsform.toSync = parseInt(this.placeLeadInfo.toSync);
          this.placeLeadsform.campaignCode = this.placeLeadInfo.campaignCode;
          this.placeLeadsform.isImportant = parseInt(this.placeLeadInfo.isImportant);
          this.placeLeadsform.zipCode = this.placeLeadInfo.zipCode;
          this.placeLeadsform.city = this.placeLeadInfo.city;
          this.placeLeadsform.alexaRank = this.placeLeadInfo.alexaRank;
          this.placeLeadsform.onlineSince = this.placeLeadInfo.onlineSince;
          this.placeLeadsform.ypReviews = this.placeLeadInfo.ypReviews;
          this.placeLeadsform.multiLocation = this.placeLeadInfo.multiLocation;
          this.placeLeadsform.lastRemark = this.placeLeadInfo.lastRemark;
          this.placeLeadsform.bbbRating = this.placeLeadInfo.bbbRating;
          this.placeLeadsform.ypRating = this.placeLeadInfo.ypRating;
          this.placeLeadsform.dataScore = this.placeLeadInfo.dataScore;
          this.placeLeadsform.carrier = this.placeLeadInfo.carrier;
          this.placeLeadsform.callerIdName = this.placeLeadInfo.callerIdName;
          this.placeLeadsform.rn = this.placeLeadInfo.rn;
          this.placeLeadsform.data = this.placeLeadInfo.data;
          this.placeLeadsform.contractAt = this.placeLeadInfo.contractAt.date;
          this.placeLeadsform.nextFollowupDate = this.placeLeadInfo.nextFollowupDate ? this.placeLeadInfo.nextFollowupDate.date : null;
          this.notes = this.placeLeadInfo.notes;
          this.placeLeadsLoading = false;
        })
        .catch(async e => {
          const data = e.response.data;

          if (data.error) {
            if (data.error.code === 418 && data.error) {
              if (data.error.message === 'Place lead not found.') {
                await this.getPlaceLead();
              } else {
                this.access = data.error.message;
              }
            } else if (data.error.code === 403 && data.error) {
              this.access = "You are not allowed to access this content.";
            }
          } else {
            console.error("Unexpected error", data.error);
          }
          this.placeLeadsLoading = false;
        });
    },
    async getPlaceLead() {
      this.placeLeadsLoading = true;
      await axios({
          method: "post",
          url: "/v1/place-lead/get?id=" + this.$route.params.id,
          data: {
            params: {
              id: AuthService.uid
            }
          },
          headers: {
            Authorization: `Bearer ${AuthService.tokens.access}`
          }
        })
        .then(response => {
          this.placeLeadInfo = response.data;
          this.placeLeadsform.id = this.placeLeadInfo.id;
          this.placeLeadsform.placeId = this.placeLeadInfo.placeId;
          this.placeLeadsform.name = this.placeLeadInfo.name;
          this.placeLeadsform.address = this.placeLeadInfo.address;
          this.placeLeadsform.status = this.placeLeadInfo.status;
          this.placeLeadsform.phone = this.placeLeadInfo.phone;
          this.placeLeadsform.type = this.placeLeadInfo.type;
          this.placeLeadsform.price = this.placeLeadInfo.price;
          this.placeLeadsform.rating = this.placeLeadInfo.rating;
          this.placeLeadsform.review = this.placeLeadInfo.review;
          this.placeLeadsform.website = this.placeLeadInfo.website;
          this.placeLeadsform.geo = this.placeLeadInfo.geo;
          this.placeLeadsform.toSync = parseInt(this.placeLeadInfo.toSync);
          this.placeLeadsform.campaignCode = this.placeLeadInfo.campaignCode;
          this.placeLeadsform.isImportant = parseInt(this.placeLeadInfo.isImportant);
          this.placeLeadsform.zipCode = this.placeLeadInfo.zipCode;
          this.placeLeadsform.city = this.placeLeadInfo.city;
          this.placeLeadsform.alexaRank = this.placeLeadInfo.alexaRank;
          this.placeLeadsform.onlineSince = this.placeLeadInfo.onlineSince;
          this.placeLeadsform.ypReviews = this.placeLeadInfo.ypReviews;
          this.placeLeadsform.multiLocation = this.placeLeadInfo.multiLocation;
          this.placeLeadsform.lastRemark = this.placeLeadInfo.lastRemark;
          this.placeLeadsform.bbbRating = this.placeLeadInfo.bbbRating;
          this.placeLeadsform.ypRating = this.placeLeadInfo.ypRating;
          this.placeLeadsform.dataScore = this.placeLeadInfo.dataScore;
          this.placeLeadsform.carrier = this.placeLeadInfo.carrier;
          this.placeLeadsform.callerIdName = this.placeLeadInfo.callerIdName;
          this.placeLeadsform.rn = this.placeLeadInfo.rn;
          this.placeLeadsform.data = this.placeLeadInfo.data;
          this.placeLeadsform.contractAt = this.placeLeadInfo.contractAt.date;
          this.placeLeadsform.nextFollowupDate = this.placeLeadInfo.nextFollowupDate ? this.placeLeadInfo.nextFollowupDate.date : null;
          this.notes = this.placeLeadInfo.notes;
          this.placeLeadsLoading = false;
        })
        .catch(e => {
          const data = e.response.data;

          if (data.error) {
            if (data.error.code === 418 && data.error) {
              this.access = data.error.message;
            } else if (data.error.code === 403 && data.error) {
              this.access = "You are not allowed to access this content.";
            }
          } else {
            console.error("Unexpected error", data.error);
          }
          this.placeLeadsLoading = false;
        });
    },
    searchPlaceLeads() {
      this.placeLeadsQueryControll();
      this.getPlaceLeads();
    },
    addPlaceLead() {
      if (this.inProgress === true) return;
      let formData;
      this.errors = {};

      const vm = this;
      this.inProgress = true;
      /* eslint-disable */
      formData = new FormData();
      /* eslint-enable */
      formData.append("form[placeId]", this.placeLeadsform.placeId);
      formData.append("form[name]", this.placeLeadsform.name);
      formData.append("form[address]", this.placeLeadsform.address);
      formData.append("form[status]", this.placeLeadsform.status);
      formData.append("form[phone]", this.placeLeadsform.phone);
      formData.append("form[type]", this.placeLeadsform.type);
      formData.append("form[price]", this.placeLeadsform.price);
      formData.append("form[rating]", this.placeLeadsform.rating);
      formData.append("form[review]", this.placeLeadsform.review);
      formData.append("form[website]", this.placeLeadsform.website);
      formData.append("form[geo]", this.placeLeadsform.geo);
      formData.append(
        "form[toSync]",
        this.placeLeadsform.toSync ? 1 : 0
      );
      formData.append("form[campaignCode]", this.placeLeadsform.campaignCode);
      formData.append(
        "form[isImportant]",
        this.placeLeadsform.isImportant ? 1 : 0
      );
      formData.append("form[zipCode]", this.placeLeadsform.zipCode);
      formData.append("form[city]", this.placeLeadsform.city);
      formData.append("form[alexaRank]", this.placeLeadsform.alexaRank);
      formData.append("form[onlineSince]", this.placeLeadsform.onlineSince);
      formData.append("form[ypReviews]", this.placeLeadsform.ypReviews);
      formData.append("form[multiLocation]", this.placeLeadsform.multiLocation);
      formData.append("form[lastRemark]", this.placeLeadsform.lastRemark);
      formData.append("form[bbbRating]", this.placeLeadsform.bbbRating);
      formData.append("form[ypRating]", this.placeLeadsform.ypRating);
      formData.append("form[dataScore]", this.placeLeadsform.dataScore);
      formData.append("form[carrier]", this.placeLeadsform.carrier);
      formData.append("form[callerIdName]", this.placeLeadsform.callerIdName);
      formData.append("form[rn]", this.placeLeadsform.rn);
      formData.append("form[data]", this.placeLeadsform.data);
      formData.append("form[contractAt]", this.placeLeadsform.contractAt ? moment(this.placeLeadsform.contractAt).format("YYYY-MM-DD HH:mm:ss") : '');
      formData.append("form[nextFollowupDate]", this.placeLeadsform.nextFollowupDate ? moment(this.placeLeadsform.nextFollowupDate).format("YYYY-MM-DD HH:mm:ss") : '');

      axios({
        method: "post",
        url: "/v1/place-lead/add",
        data: formData,
        headers: {
          Authorization: `Bearer ${AuthService.tokens.access}`
        }
      }).then(
        ({
          data
        }) => {
          this.getPlaceLeads();
          vm.inProgress = false;
          clearTimeout(vm.response.timeoutID);
          vm.response.active = true;
          vm.response.timeoutID = setTimeout(function () {
            vm.response.active = false;
          }, 3000);
        },
        ({
          response
        }) => {
          vm.inProgress = false;

          const {
            data
          } = response;

          if (data.error) {
            if (data.error.code === 418 && data.error) {
              clearTimeout(vm.error.timeoutID);
              if (data.error.errors) {
                vm.error.message = JSON.stringify(data.error.errors);
              } else {
                vm.error.message = data.error.message;
              }
              vm.error.timeoutID = setTimeout(function () {
                vm.error.message = null;
              }, 3000);
            }
          } else {
            console.error("Unexpected error", data.error);
          }
        }
      );
    },
    updatePlaceLead() {
      if (this.inProgress === true) return;
      let formData;
      this.errors = {};
      const vm = this;
      this.inProgress = true;
      /* eslint-disable */
      formData = new FormData();
      /* eslint-enable */
      formData.append("params[id]", AuthService.uid);
      formData.append("form[id]", this.placeLeadsform.id);
      formData.append("form[placeId]", this.placeLeadsform.placeId);
      formData.append("form[name]", this.placeLeadsform.name);
      formData.append("form[address]", this.placeLeadsform.address);
      formData.append("form[status]", this.placeLeadsform.status);
      formData.append("form[phone]", this.placeLeadsform.phone);
      formData.append("form[review]", this.placeLeadsform.review);
      formData.append(
        "form[toSync]",
        this.placeLeadsform.toSync ? 1 : 0
      );
      formData.append("form[campaignCode]", this.placeLeadsform.campaignCode);
      formData.append(
        "form[isImportant]",
        this.placeLeadsform.isImportant ? 1 : 0
      );
      formData.append("form[zipCode]", this.placeLeadsform.zipCode || 0);
      formData.append("form[city]", this.placeLeadsform.city || '');
      formData.append("form[onlineSince]", this.placeLeadsform.onlineSince || '');
      formData.append("form[ypRating]", this.placeLeadsform.ypRating || 0);
      formData.append("form[dataScore]", this.placeLeadsform.dataScore || 0);
      formData.append("form[contractAt]", this.placeLeadsform.contractAt ? moment(this.placeLeadsform.contractAt).format("YYYY-MM-DD HH:mm:ss") : '');
      formData.append("form[nextFollowupDate]", this.placeLeadsform.nextFollowupDate ? moment(this.placeLeadsform.nextFollowupDate).format("YYYY-MM-DD HH:mm:ss") : '');
      formData.append("form[alexaRank]", this.placeLeadsform.alexaRank || 0);
      formData.append("form[ypReviews]", this.placeLeadsform.ypReviews || 0);
      formData.append("form[multiLocation]", this.placeLeadsform.multiLocation || 0);
      formData.append("form[lastRemark]", this.placeLeadsform.lastRemark || '');
      formData.append("form[bbbRating]", this.placeLeadsform.bbbRating || 0);
      formData.append("form[type]", this.placeLeadsform.type || '');
      formData.append("form[price]", this.placeLeadsform.price || 0);
      formData.append("form[rating]", this.placeLeadsform.rating || 0);
      formData.append("form[website]", this.placeLeadsform.website || '');
      formData.append("form[geo]", this.placeLeadsform.geo);
      formData.append("form[carrier]", this.placeLeadsform.carrier || '');
      formData.append("form[callerIdName]", this.placeLeadsform.callerIdName || '');
      formData.append("form[rn]", this.placeLeadsform.rn || 0);
      formData.append("form[data]", this.placeLeadsform.data);

      axios({
        method: "post",
        url: "/v1/place-lead/update",
        data: formData,
        headers: {
          Authorization: `Bearer ${AuthService.tokens.access}`
        }
      }).then(
        ({
          data
        }) => {
          vm.inProgress = false;
          vm.placeLeadInfo = data.result;
          vm.notes = vm.notes.concat(data.result.notes);
          clearTimeout(vm.response.timeoutID);
          vm.response.active = true;
          vm.response.timeoutID = setTimeout(function () {
            vm.response.active = false;
          }, 3000);
        },
        ({
          response
        }) => {
          vm.inProgress = false;

          const {
            data
          } = response;

          if (data.error) {
            if (data.error.code === 418 && data.error) {
              clearTimeout(vm.error.timeoutID);
              if (data.error.errors) {
                vm.error.message = JSON.stringify(data.error.errors);
              } else {
                vm.error.message = data.error.message;
              }
              vm.error.timeoutID = setTimeout(function () {
                vm.error.message = null;
              }, 3000);
            }
          } else {
            console.error("Unexpected error", data.error);
          }
        }
      );
    },
    removePlaceLeads(redirect = false) {
      if (this.inProgress === true) return;
      let formData;
      const vm = this;

      /* eslint-disable */
      formData = new FormData();
      /* eslint-enable */
      formData.append("params[id]", AuthService.uid);
      formData.append("placeLeads", JSON.stringify(this.placeLeadsData.checked.length ? this.placeLeadsData.checked : [this.placeLeadInfo.id]));
      axios({
        method: "post",
        url: "/v1/place-lead/remove",
        data: formData,
        headers: {
          Authorization: `Bearer ${AuthService.tokens.access}`
        }
      }).then(
        ({
          data
        }) => {
          vm.inProgress = false;
          this.placeLeadsRemoveModalVisible = false;

          if (redirect) {
            this.$router.push({
              path: "/place-leads/1"
            });
          } else {
            this.placeLeadsData.checked = [];
            this.getPlaceLeads();
          }
        },
        ({
          response
        }) => {
          vm.inProgress = false;

          const {
            data
          } = response;

          if (data.error) {
            console.error(data.error);
          } else {
            console.error("Unexpected error", data.error);
          }
        }
      );
    },
    massUpdate() {
      const changes = updatedDiff(this.placeLeadsOriginal, this.placeLeads);
      if (Object.keys(changes).length === 0) return;

      if (this.inProgress === true) return;
      this.placeLeadsLoading = true;
      let formData;
      const vm = this;

      /* eslint-disable */
      formData = new FormData();
      /* eslint-enable */

      formData.append("params[id]", AuthService.uid);
      for (let key in changes) {

        let placeLead = this.placeLeads[key];

        formData.append("forms[" + placeLead.id + "][form][id]", placeLead.id);
        formData.append("forms[" + placeLead.id + "][form][placeId]", placeLead.placeId);
        formData.append("forms[" + placeLead.id + "][form][name]", placeLead.name);
        formData.append("forms[" + placeLead.id + "][form][address]", placeLead.address);
        formData.append("forms[" + placeLead.id + "][form][status]", placeLead.status);
        formData.append("forms[" + placeLead.id + "][form][phone]", placeLead.phone);
        formData.append("forms[" + placeLead.id + "][form][review]", placeLead.review);
        formData.append(
          "forms[" + placeLead.id + "][form][toSync]",
          placeLead.toSync ? 1 : 0
        );
        formData.append("forms[" + placeLead.id + "][form][campaignCode]", placeLead.campaignCode);
        formData.append(
          "forms[" + placeLead.id + "][form][isImportant]",
          placeLead.isImportant ? 1 : 0
        );
        formData.append("forms[" + placeLead.id + "][form][zipCode]", placeLead.zipCode || 0);
        formData.append("forms[" + placeLead.id + "][form][city]", placeLead.city || '');
        formData.append("forms[" + placeLead.id + "][form][onlineSince]", placeLead.onlineSince || '');
        formData.append("forms[" + placeLead.id + "][form][ypRating]", placeLead.ypRating || 0);
        formData.append("forms[" + placeLead.id + "][form][dataScore]", placeLead.dataScore || 0);
        formData.append("forms[" + placeLead.id + "][form][contractAt]", placeLead.contractAt ? moment(placeLead.contractAt.date).format("YYYY-MM-DD HH:mm:ss") : '');
        formData.append("forms[" + placeLead.id + "][form][nextFollowupDate]", placeLead.nextFollowupDate ? moment(placeLead.nextFollowupDate.date).format("YYYY-MM-DD HH:mm:ss") : '');
        formData.append("forms[" + placeLead.id + "][form][alexaRank]", placeLead.alexaRank || 0);
        formData.append("forms[" + placeLead.id + "][form][ypReviews]", placeLead.ypReviews || 0);
        formData.append("forms[" + placeLead.id + "][form][multiLocation]", placeLead.multiLocation || 0);
        formData.append("forms[" + placeLead.id + "][form][lastRemark]", placeLead.lastRemark || '');
        formData.append("forms[" + placeLead.id + "][form][bbbRating]", placeLead.bbbRating || 0);
        formData.append("forms[" + placeLead.id + "][form][type]", placeLead.type || '');
        formData.append("forms[" + placeLead.id + "][form][price]", placeLead.price || 0);
        formData.append("forms[" + placeLead.id + "][form][rating]", placeLead.rating || 0);
        formData.append("forms[" + placeLead.id + "][form][website]", placeLead.website || '');
        formData.append("forms[" + placeLead.id + "][form][geo]", placeLead.geo);
        formData.append("forms[" + placeLead.id + "][form][carrier]", placeLead.carrier || '');
        formData.append("forms[" + placeLead.id + "][form][callerIdName]", placeLead.callerIdName || '');
        formData.append("forms[" + placeLead.id + "][form][rn]", placeLead.rn || 0);
        formData.append("forms[" + placeLead.id + "][form][data]", placeLead.data);
      }

      axios({
        method: "post",
        url: "/v1/place-lead/massupdate",
        data: formData,
        headers: {
          Authorization: `Bearer ${AuthService.tokens.access}`
        }
      }).then(
        ({
          data
        }) => {
          vm.inProgress = false;
          this.placeLeadsData.checked = [];
          this.multiUpdatePlaceLeads = false;
          this.getPlaceLeads();
          this.placeLeadsLoading = false;
        },
        ({
          response
        }) => {
          vm.inProgress = false;

          const {
            data
          } = response;

          if (data.error) {
            console.error(data.error);
          } else {
            console.error("Unexpected error", data.error);
          }
        }
      );
    },
    sortPlaceLeadsBy(field = null) {
      if (field === null) return;
      if (this.placeLeadsSort === '[]') this.placeLeadsSort = {};
      if (!this.placeLeadsSort[field]) {
        this.placeLeadsSort[field] = 'DESC';
      } else if (this.placeLeadsSort[field] === 'DESC') {
        this.placeLeadsSort[field] = 'ASC';
      } else if (this.placeLeadsSort[field] === 'ASC') {
        delete this.placeLeadsSort[field];
      }

      const sortFieldsCount = Object.keys(this.placeLeadsSort).length;

      if (sortFieldsCount === 0) this.placeLeadsSort = '[]';
      this.placeLeadsQueryControll();
      this.getPlaceLeads();
    },
    filterPlaceLeads() {

      let filterData = {};

      for (let key in this.placeLeadsFilterObject) {
        let field = this.placeLeadsFilterObject[key];
        if (field.condition !== "BETWEEN" && field.value === "") continue;
        if (field.condition === "BETWEEN" && (field.from === "" || field.to === "")) continue;
        if (field.condition === '!=' && !field.value) continue;

        let val;
        if (field.condition !== '!=' && field.condition !== 'BETWEEN') val = field.condition + '|' + field.value;
        if (field.condition === '!=') val = field.condition + '|' + field.default;
        if (field.condition === 'BETWEEN') val = field.condition + '|' + (field.isDate ? moment(field.from).format("YYYY-MM-DD") + ' 00:00:00' : field.from) + '|' + (field.isDate ? moment(field.to).format("YYYY-MM-DD") + ' 00:00:00' : field.to);

        filterData[key] = val;
      }

      this.placeLeadsFilter = '[]';

      if (Object.keys(filterData).length > 0) {
        this.placeLeadsFilter = filterData;
      }

      this.placeLeadsQueryControll();
      this.getPlaceLeads();
    },
    placeLeadsQueryControll() {

      if (this.$route.query.string === this.placeLeadsSearchString &&
        (this.$route.query.sort != '' && this.$route.query.sort === JSON.stringify(this.placeLeadsSort)) &&
        (this.$route.query.filter != '' && this.$route.query.filter === JSON.stringify(this.placeLeadsFilter))) return;

      if (this.placeLeadsPage != 1) {
        this.$router.push({
          name: 'place-leads',
          params: {
            page: 1
          },
          query: this.$route.query
        });
      }
      this.$router.replace({
        query: {
          string: this.placeLeadsSearchString,
          sort: (this.placeLeadsSort !== '[]') ? JSON.stringify(this.placeLeadsSort) : '',
          filter: (this.placeLeadsFilter !== '[]') ? JSON.stringify(this.placeLeadsFilter) : ''
        }
      });
    },
    getDateRange(val) {
      if (val === 'clear') {
        this.placeLeadsFilterObject.updatedAt.from = '';
        this.placeLeadsFilterObject.updatedAt.to = '';
        return;
      }

      let date = {};

      if (val === 'today') {
        date.from = moment().format("YYYY-MM-DD");
        date.to = moment().add(1, 'day').format("YYYY-MM-DD");
      }
      if (val === 'this week') {
        date.from = moment().startOf('week').add(1, 'day').format("YYYY-MM-DD");
        date.to = moment().endOf('week').add(1, 'day').format("YYYY-MM-DD");
      }
      if (val === 'this month') {
        date.from = moment().startOf('month').format("YYYY-MM-DD");
        date.to = moment().endOf('month').add(1, 'day').format("YYYY-MM-DD");
      }
      if (val === 'this year') {
        date.from = moment().startOf('year').format("YYYY-MM-DD");
        date.to = moment().endOf('year').add(1, 'day').format("YYYY-MM-DD");
      }
      if (val === 'week to date') {
        date.from = moment().startOf('week').add(1, 'day').format("YYYY-MM-DD");
        date.to = moment().add(1, 'day').format("YYYY-MM-DD");
      }
      if (val === 'month to date') {
        date.from = moment().startOf('month').format("YYYY-MM-DD");
        date.to = moment().add(1, 'day').format("YYYY-MM-DD");
      }
      if (val === 'year to date') {
        date.from = moment().startOf('year').format("YYYY-MM-DD");
        date.to = moment().add(1, 'day').format("YYYY-MM-DD");
      }
      if (val === 'yesterday') {
        date.from = moment().subtract(1, 'day').format("YYYY-MM-DD");
        date.to = moment().format("YYYY-MM-DD");
      }
      if (val === 'day before yesterday') {
        date.from = moment().subtract(2, 'day').format("YYYY-MM-DD");
        date.to = moment().subtract(1, 'day').format("YYYY-MM-DD");
      }
      if (val === 'this day last week') {
        date.from = moment().subtract(7, 'day').format("YYYY-MM-DD");
        date.to = moment().subtract(6, 'day').format("YYYY-MM-DD");
      }
      if (val === 'prev week') {
        date.from = moment().startOf('week').subtract(6, 'day').format("YYYY-MM-DD");
        date.to = moment().startOf('week').format("YYYY-MM-DD");
      }
      if (val === 'prev month') {
        date.from = moment().subtract(1, 'months').startOf('months').format("YYYY-MM-DD");
        date.to = moment().subtract(1, 'months').endOf('months').add(1, 'day').format("YYYY-MM-DD");
      }
      if (val === 'last 7 days') {
        date.from = moment().subtract(6, 'day').format("YYYY-MM-DD");
        date.to = moment().add(1, 'day').format("YYYY-MM-DD");
      }
      if (val === 'last 30 days') {
        date.from = moment().subtract(29, 'day').format("YYYY-MM-DD");
        date.to = moment().add(1, 'day').format("YYYY-MM-DD");
      }
      if (val === 'last 60 days') {
        date.from = moment().subtract(59, 'day').format("YYYY-MM-DD");
        date.to = moment().add(1, 'day').format("YYYY-MM-DD");
      }
      if (val === 'last 90 days') {
        date.from = moment().subtract(89, 'day').format("YYYY-MM-DD");
        date.to = moment().add(1, 'day').format("YYYY-MM-DD");
      }

      this.placeLeadsFilterObject.updatedAt.from = date.from + ' 00:00:00';
      this.placeLeadsFilterObject.updatedAt.to = date.to + ' 00:00:00';
    }
  },
  created() {
    this.placeLeadsSearchString = this.$route.query.string;
    this.placeLeadsSort = this.$route.query.sort && this.$route.query.sort.length ? JSON.parse(this.$route.query.sort) : this.placeLeadsSort;
    this.placeLeadsFilter = this.$route.query.filter && this.$route.query.filter.length ? JSON.parse(this.$route.query.filter) : this.placeLeadsFilter;
    if (this.placeLeadsFilter !== '[]') {
      for (let key in this.placeLeadsFilter) {
        const val = this.placeLeadsFilter[key].split("|");
        if (val.length == 3) {
          this.placeLeadsFilterObject[key].from = val[1];
          this.placeLeadsFilterObject[key].to = val[2];
        } else {
          this.placeLeadsFilterObject[key].value = val[1]
        }
      }
    }
  }
}
