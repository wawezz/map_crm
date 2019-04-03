import axios from "axios";
import moment from "moment";
import AuthService from "./../services/AuthService";

export const placeLeads = {
  data() {
    return {
      placeLeadsFilter: '[]',
      placeLeadsSort: '[]',
      placeLeadsLoading: true,
      placeLeadsData: {
        checked: []
      },
      placeLeadInfo: {},
      placeLeads: {},
      placeLeadsTotalCount: 0,
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
        contractAt: null,
        nextFollowupDate: null
      },
      placeLeadsColumns: [{
          type: 'checkbox'
        }, {
          name: 'name',
          type: 'link',
          prefix: '/place-lead/'
        },
        {
          name: 'date of create',
          field: 'createdAt',
          type: 'date',
          disableSort: true
        },
        {
          name: 'type'
        },
        {
          name: 'status',
          field: 'statusName'
        },
        {
          name: 'phone'
        }
      ],
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
      const filter = this.placeLeadsFilter !== '[]' ? JSON.stringify(this.placeLeadsFilter) : this.placeLeadsFilter;
      const sort = this.placeLeadsSort !== '[]' ? JSON.stringify(this.placeLeadsSort) : this.placeLeadsSort;
      axios({
          method: "post",
          url: "/v1/place-lead/list?filter=" + filter + "&sort=" + sort + "&limit=" + this.placeLeadsLimit + "&offset=" + (this.placeLeadsLimit * (this.placeLeadsPage - 1)),
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
        });
    },
    async getPlaceLeadByPlaceId() {
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
          this.placeLeadsform.data = this.placeLeadInfo.data;
          this.placeLeadsform.contractAt = this.placeLeadInfo.contractAt.date;
          this.placeLeadsform.nextFollowupDate = this.placeLeadInfo.nextFollowupDate ? this.placeLeadInfo.nextFollowupDate.date : null;
          this.notes = this.placeLeadInfo.notes;
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
        });
    },
    async getPlaceLead() {
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
          this.placeLeadsform.data = this.placeLeadInfo.data;
          this.placeLeadsform.contractAt = this.placeLeadInfo.contractAt.date;
          this.placeLeadsform.nextFollowupDate = this.placeLeadInfo.nextFollowupDate ? this.placeLeadInfo.nextFollowupDate.date : null;
          this.notes = this.placeLeadInfo.notes;
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
        });
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
      formData.append("form[data]", this.placeLeadsform.data);
      formData.append("form[contractAt]", this.placeLeadsform.contractAt ? moment(this.placeLeadsform.contractAt).format("YYYY-MM-DD HH:mm:ss") : '');
      formData.append("form[nextFollowupDate]", this.placeLeadsform.nextFollowupDate ? moment(this.placeLeadsform.nextFollowupDate).format("YYYY-MM-DD HH:mm:ss") : '');

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
      formData.append("placeLeads", JSON.stringify(this.checkedPlaceLeads.length ? this.checkedPlaceLeads : [this.placeLeadInfo.id]));
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
            this.checkedPlaceLeads = [];
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
    sortPlaceLeadsBy(field = null) {
      if (field === null) return;
      this.placeLeadsLoading = true;
      if (this.placeLeadsSort === '[]') this.placeLeadsSort = {};
      if (!this.placeLeadsSort[field]) {
        this.placeLeadsSort[field] = 'DESC';
      } else if (this.placeLeadsSort[field] === 'DESC') {
        this.placeLeadsSort[field] = 'ASC';
      } else if (this.placeLeadsSort[field] === 'ASC') {
        delete this.placeLeadsSort[field];
      }

      if (Object.keys(this.placeLeadsSort).length === 0) this.placeLeadsSort = '[]';
      this.getPlaceLeads();
    }
  }
}
