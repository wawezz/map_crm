import axios from "axios";
import moment from "moment";
import AuthService from "./../services/AuthService";

export const leads = {
  data() {
    return {
      leadsFilter: '[]',
      leadsSort: '[]',
      leadsLoading: true,
      leadsData: {
        checked: []
      },
      leadInfo: {},
      leads: {},
      leadsTotalCount: 0,
      leadsform: {
        name: null,
        responsible: null,
        client: null,
        status: 1,
        budget: null,
        firstCallAt: null,
        completedAt: null,
        countryId: 1,
        currency: 1,
        product: null,
        productCount: null,
        productPrice: null,
        shippingPrice: null,
        postOrder: 0,
        rejectionReason: null,
        id: null
      },
      leadsColumns: [{
          type: 'checkbox'
        }, {
          name: 'id'
        },
        {
          name: 'date of create',
          field: 'createdAt',
          type: 'date',
          disableSort: true
        },
        {
          name: 'date of completed',
          field: 'completedAt',
          type: 'date',
          disableSort: true
        },
        {
          name: 'name',
          type: 'link',
          prefix: '/lead/'
        },
        {
          name: 'client',
          type: 'link',
          prefix: '/client/',
          field: 'clientName',
          disableSort: true
        },
        {
          name: 'responsible',
          type: 'link',
          prefix: '/profile/',
          field: 'responsibleName',
          disableSort: true
        },
        {
          name: 'status',
          field: 'statusName'
        }
      ],
      leadsLimit: 25
    }
  },
  computed: {
    leadsPage() {
      let page = 1;
      if (this.$route.params.page) page = this.$route.params.page;

      return parseInt(page);
    }
  },
  methods: {
    getLeads() {
      const filter = this.leadsFilter !== '[]' ? JSON.stringify(this.leadsFilter) : this.leadsFilter;
      const sort = this.leadsSort !== '[]' ? JSON.stringify(this.leadsSort) : this.leadsSort;
      axios({
          method: "post",
          url: "/v1/lead/list?filter=" + filter + "&sort=" + sort + "&limit=" + this.leadsLimit + "&offset=" + (this.leadsLimit * (this.leadsPage - 1)),
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
          this.leads = response.data;
          this.leadsTotalCount = parseInt(response.headers['x-pagination-total']);
          this.leadsLoading = false;
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
    getLead() {
      axios({
          method: "post",
          url: "/v1/lead/get?id=" + this.$route.params.id,
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
          this.leadInfo = response.data;
          this.leadsform.name = this.leadInfo.name;
          this.leadsform.responsible = this.leadInfo.responsible;
          this.leadsform.client = this.leadInfo.client;
          this.leadsform.status = this.leadInfo.status;
          this.leadsform.id = this.leadInfo.id;
          this.leadsform.budget = this.leadInfo.budget;
          this.leadsform.firstCallAt = this.leadInfo.firstCallAt.date;
          this.leadsform.completedAt = this.leadInfo.completedAt.date;
          this.leadsform.countryId = this.leadInfo.countryId;
          this.leadsform.currency = this.leadInfo.currency;
          this.leadsform.product = this.leadInfo.product;
          this.leadsform.productCount = this.leadInfo.productCount;
          this.leadsform.productPrice = this.leadInfo.productPrice;
          this.leadsform.shippingPrice = this.leadInfo.shippingPrice;
          this.leadsform.postOrder = parseInt(this.leadInfo.postOrder);
          this.leadsform.rejectionReason = this.leadInfo.rejectionReason;
          this.notes = this.leadInfo.notes;
          this.updateCurrency(true);
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
    addLead() {
      if (this.inProgress === true) return;
      let formData;
      this.errors = {};

      const vm = this;
      this.inProgress = true;
      /* eslint-disable */
      formData = new FormData();
      /* eslint-enable */
      formData.append("form[name]", this.leadsform.name);
      formData.append("form[responsible]", this.leadsform.responsible);
      formData.append("form[client]", this.leadsform.client);
      formData.append("form[status]", this.leadsform.status);
      formData.append("form[budget]", this.leadsform.budget);
      formData.append("form[firstCallAt]", this.leadsform.firstCallAt ? moment(this.leadsform.firstCallAt).format("YYYY-MM-DD HH:mm:ss") : '');
      formData.append("form[completedAt]", this.leadsform.completedAt ? moment(this.leadsform.completedAt).format("YYYY-MM-DD HH:mm:ss") : '');
      formData.append("form[countryId]", this.leadsform.countryId);
      formData.append("form[currency]", this.leadsform.currency);
      formData.append("form[product]", this.leadsform.product);
      formData.append("form[productCount]", this.leadsform.productCount);
      formData.append("form[productPrice]", this.leadsform.productPrice);
      formData.append("form[shippingPrice]", this.leadsform.shippingPrice);
      formData.append("form[rejectionReason]", this.leadsform.rejectionReason);
      formData.append(
        "form[postOrder]",
        this.leadsform.postOrder ? 1 : 0
      );

      axios({
        method: "post",
        url: "/v1/lead/add",
        data: formData,
        headers: {
          Authorization: `Bearer ${AuthService.tokens.access}`
        }
      }).then(
        ({
          data
        }) => {
          this.getLeads();
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
    updateLead() {
      if (this.inProgress === true) return;
      let formData;
      this.errors = {};
      const vm = this;
      this.inProgress = true;
      /* eslint-disable */
      formData = new FormData();
      /* eslint-enable */
      formData.append("params[id]", AuthService.uid);
      formData.append("form[id]", this.leadsform.id);
      formData.append("form[name]", this.leadsform.name);
      formData.append("form[responsible]", this.leadsform.responsible);
      formData.append("form[client]", this.leadsform.client);
      formData.append("form[status]", this.leadsform.status);
      formData.append("form[budget]", this.leadsform.budget);
      formData.append("form[firstCallAt]", this.leadsform.firstCallAt ? moment(this.leadsform.firstCallAt).format("YYYY-MM-DD HH:mm:ss") : '');
      formData.append("form[completedAt]", this.leadsform.completedAt ? moment(this.leadsform.completedAt).format("YYYY-MM-DD HH:mm:ss") : '');
      formData.append("form[countryId]", this.leadsform.countryId);
      formData.append("form[currency]", this.leadsform.currency);
      formData.append("form[product]", this.leadsform.product);
      formData.append("form[productCount]", this.leadsform.productCount);
      formData.append("form[productPrice]", this.leadsform.productPrice);
      formData.append("form[shippingPrice]", this.leadsform.shippingPrice);
      formData.append("form[rejectionReason]", this.leadsform.rejectionReason);
      formData.append("form[postOrder]", this.leadsform.postOrder ? 1 : 0);

      axios({
        method: "post",
        url: "/v1/lead/update",
        data: formData,
        headers: {
          Authorization: `Bearer ${AuthService.tokens.access}`
        }
      }).then(
        ({
          data
        }) => {
          vm.inProgress = false;
          vm.leadInfo = data.result;
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
    removeLeads(redirect = false) {
      if (this.inProgress === true) return;
      let formData;
      const vm = this;

      /* eslint-disable */
      formData = new FormData();
      /* eslint-enable */
      formData.append("params[id]", AuthService.uid);
      formData.append("leads", JSON.stringify(this.checkedLeads.length ? this.checkedLeads : [this.leadInfo.id]));
      axios({
        method: "post",
        url: "/v1/lead/remove",
        data: formData,
        headers: {
          Authorization: `Bearer ${AuthService.tokens.access}`
        }
      }).then(
        ({
          data
        }) => {
          vm.inProgress = false;
          this.leadsRemoveModalVisible = false;

          if (redirect) {
            this.$router.push({
              path: "/leads"
            });
          } else {
            this.checkedLeads = [];
            this.getLeads();
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
    updateCurrency(update = false) {
      let curIndex = 0;
      const vm = this;
      this.countries.forEach(function (item, index) {
        if (item.id === vm.leadsform.countryId) {
          curIndex = index;
        }
      });

      let avilCurrencies = this.countries[curIndex].currencies;
      this.currenciesFiltered = [];
      this.currencies.forEach(element => {
        let val = element.id;

        if (avilCurrencies.indexOf(val) !== -1) {
          this.currenciesFiltered.push(element);
        }
      });

      if (!update) {
        this.leadsform.currency = JSON.parse(avilCurrencies)[0];
      }
    },
    sortBy(field = null) {
      if (field === null) return;
      this.leadsLoading = true;
      if (this.leadsSort === '[]') this.leadsSort = {};
      if (!this.leadsSort[field]) {
        this.leadsSort[field] = 'DESC';
      } else if (this.leadsSort[field] === 'DESC') {
        this.leadsSort[field] = 'ASC';
      } else if (this.leadsSort[field] === 'ASC') {
        delete this.leadsSort[field];
      }

      if (Object.keys(this.leadsSort).length === 0) this.leadsSort = '[]';
      this.getLeads();
    }
  }
}
