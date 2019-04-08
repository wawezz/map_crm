import axios from "axios";
import AuthService from "./../services/AuthService";

export const clients = {
  data() {
    return {
      clientsFilter: '[]',
      clientsSort: '[]',
      clientsLoading: false,
      clientsData: {
        checked: []
      },
      clientsSearchString: '',
      clientInfo: {},
      clientLeads: {},
      clients: {},
      clientsTotalCount: 0,
      clientsById: {},
      clientform: {
        name: null,
        surname: null,
        emailVerified: 0,
        phoneVerified: 0,
        email: null,
        phone: null,
        otherPhone: null,
        zip: null,
        flat: null,
        building: null,
        street: null,
        city: null,
        state: null,
        skype: null,
        responsible: null,
        countryId: 1,
        id: null
      },
      clientsColumns: [{
          type: 'checkbox'
        },
        {
          name: 'name',
          type: 'link',
          prefix: '/client/',
        },
        {
          name: 'phone',
          type: 'phone',
          field: 'workPhone'
        },
        {
          name: 'email',
          type: 'email'
        },
        {
          name: 'added by',
          field: 'creatorName',
          disableSort: true
        },
        {
          name: 'responsible',
          field: 'responsibleName',
          disableSort: true
        },
        {
          name: 'country',
          field: 'countryName',
          disableSort: true
        }
      ],
      clientsLimit: 25
    }
  },
  computed: {
    clientsPage() {
      let page = 1;
      if (this.$route.params.page) page = this.$route.params.page;

      return parseInt(page);
    }
  },
  methods: {
    getClient() {
      this.clientsLoading = true;
      axios({
          method: "post",
          url: "/v1/client/get?id=" + this.$route.params.id,
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
          this.clientInfo = response.data;
          this.clientform.name = this.clientInfo.name;
          this.clientform.email = this.clientInfo.email;
          this.clientform.phone = this.clientInfo.workPhone;
          this.clientform.countryId = this.clientInfo.countryId;
          this.clientform.responsible = this.clientInfo.responsible;
          this.clientform.surname = this.clientInfo.surname ?
            this.clientInfo.surname :
            "";
          this.clientform.emailVerified =
            this.clientInfo.emailVerified > 0 ? 1 : 0;
          this.clientform.phoneVerified =
            this.clientInfo.phoneVerified > 0 ? 1 : 0;
          this.clientform.otherPhone = this.clientInfo.otherPhone ?
            this.clientInfo.otherPhone :
            "";
          this.clientform.zip = this.clientInfo.zip ? this.clientInfo.zip : "";
          this.clientform.flat = this.clientInfo.flat ? this.clientInfo.flat : "";
          this.clientform.building = this.clientInfo.building ?
            this.clientInfo.building :
            "";
          this.clientform.street = this.clientInfo.street ?
            this.clientInfo.street :
            "";
          this.clientform.city = this.clientInfo.city ? this.clientInfo.city : "";
          this.clientform.state = this.clientInfo.state ?
            this.clientInfo.state :
            "";
          this.clientform.skype = this.clientInfo.skype ?
            this.clientInfo.skype :
            "";
          this.clientform.id = this.clientInfo.id;
          this.notes = this.clientInfo.notes;
          this.clientsLoading = false;
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
          this.clientsLoading = false;
        });
    },
    getClients() {
      this.clientsLoading = true;
      const filter = this.clientsFilter !== '[]' ? JSON.stringify(this.clientsFilter) : this.clientsFilter;
      const sort = this.clientsSort !== '[]' ? JSON.stringify(this.clientsSort) : this.clientsSort;
      axios({
          method: "post",
          url: "/v1/client/list?filter=" + filter + "&sort=" + sort + "&limit=" + this.clientsLimit + "&offset=" + (this.clientsLimit * (this.clientsPage - 1)),
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
          this.clients = response.data;
          this.clientsTotalCount = parseInt(response.headers['x-pagination-total']);

          for (let i in this.clients) {
            this.clientsById[this.clients[i].id] = this.clients[i];
          }
          this.clientsLoading = false;
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
          this.clientsLoading = false;
        });
    },
    addClient() {
      if (this.inProgress === true) return;
      let formData;
      this.errors = {};

      if (!this.isValidEmail) {
        this.errors.email = "Incorrect email.";
        return false;
      }

      const vm = this;
      this.inProgress = true;
      /* eslint-disable */
      formData = new FormData();
      /* eslint-enable */
      formData.append("form[name]", this.clientform.name);
      formData.append("form[email]", this.clientform.email);
      formData.append("form[phone]", this.clientform.phone);
      formData.append("form[responsible]", this.clientform.responsible);
      formData.append("form[countryId]", this.clientform.countryId);
      formData.append("form[surname]", this.clientform.surname);
      formData.append(
        "form[emailVerified]",
        this.clientform.emailVerified ? 1 : 0
      );
      formData.append(
        "form[phoneVerified]",
        this.clientform.phoneVerified ? 1 : 0
      );
      formData.append("form[otherPhone]", this.clientform.otherPhone);
      formData.append("form[zip]", this.clientform.zip);
      formData.append("form[flat]", this.clientform.flat);
      formData.append("form[building]", this.clientform.building);
      formData.append("form[street]", this.clientform.street);
      formData.append("form[city]", this.clientform.city);
      formData.append("form[state]", this.clientform.state);
      formData.append("form[skype]", this.clientform.skype);

      axios({
        method: "post",
        url: "/v1/client/add",
        data: formData,
        headers: {
          Authorization: `Bearer ${AuthService.tokens.access}`
        }
      }).then(
        ({
          data
        }) => {
          this.getClients();
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
    updateClient() {
      if (this.inProgress === true) return;
      let formData;
      this.errors = {};

      if (!this.isValidEmail) {
        this.errors.email = "Incorrect email.";
        return false;
      }

      const vm = this;
      this.inProgress = true;
      /* eslint-disable */
      formData = new FormData();
      /* eslint-enable */
      formData.append("params[id]", AuthService.uid);
      formData.append("form[id]", this.clientform.id);
      formData.append("form[name]", this.clientform.name);
      formData.append("form[email]", this.clientform.email);
      formData.append("form[phone]", this.clientform.phone);
      formData.append("form[countryId]", this.clientform.countryId);
      formData.append("form[responsible]", this.clientform.responsible);
      formData.append("form[surname]", this.clientform.surname);

      formData.append(
        "form[emailVerified]",
        this.clientform.emailVerified ? 1 : 0
      );
      formData.append(
        "form[phoneVerified]",
        this.clientform.phoneVerified ? 1 : 0
      );
      formData.append("form[otherPhone]", this.clientform.otherPhone);
      formData.append("form[zip]", this.clientform.zip);
      formData.append("form[flat]", this.clientform.flat);
      formData.append("form[building]", this.clientform.building);
      formData.append("form[street]", this.clientform.street);
      formData.append("form[city]", this.clientform.city);
      formData.append("form[state]", this.clientform.state);
      formData.append("form[skype]", this.clientform.skype);
      axios({
        method: "post",
        url: "/v1/client/update",
        data: formData,
        headers: {
          Authorization: `Bearer ${AuthService.tokens.access}`
        }
      }).then(
        ({
          data
        }) => {
          vm.inProgress = false;
          vm.clientInfo = data.result;

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
    removeClients(redirect = false) {
      if (this.inProgress === true) return;
      let formData;
      const vm = this;

      /* eslint-disable */
      formData = new FormData();
      /* eslint-enable */
      formData.append("params[id]", AuthService.uid);
      formData.append("clients", JSON.stringify(this.clientsData.checked.length ? this.clientsData.checked : [this.clientInfo.id]));

      axios({
        method: "post",
        url: "/v1/client/remove",
        data: formData,
        headers: {
          Authorization: `Bearer ${AuthService.tokens.access}`
        }
      }).then(
        ({
          data
        }) => {
          vm.inProgress = false;
          this.clientsRemoveModalVisible = false;

          if (redirect) {
            this.$router.push({
              path: "/clients"
            });
          } else {
            this.clientsData.checked = [];
            this.getClients();
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
    sortClientsBy(field = null) {
      if (field === null) return;
      if (this.clientsSort === '[]') this.clientsSort = {};
      if (!this.clientsSort[field]) {
        this.clientsSort[field] = 'DESC';
      } else if (this.clientsSort[field] === 'DESC') {
        this.clientsSort[field] = 'ASC';
      } else if (this.clientsSort[field] === 'ASC') {
        delete this.clientsSort[field];
      }

      if (Object.keys(this.clientsSort).length === 0) this.clientsSort = '[]';
      this.clientsQueryControll();
      this.getClients();
    },
    clientsQueryControll() {
      if (this.$route.query.string === this.placeLeadsSearchString &&
        (this.$route.query.sort != '' && this.$route.query.sort === JSON.stringify(this.placeLeadsSort)) &&
        (this.$route.query.filter != '' && this.$route.query.filter === JSON.stringify(this.placeLeadsFilter))) return;

      if (this.placeLeadsPage != 1) {
        this.$router.push({
          name: 'clients',
          params: {
            page: 1
          },
          query: this.$route.query
        });
      }
      this.$router.replace({
        query: {
          string: this.clientsSearchString,
          sort: (this.clientsSort !== '[]') ? JSON.stringify(this.clientsSort) : '',
          filter: (this.clientsFilter !== '[]') ? JSON.stringify(this.clientsFilter) : ''
        }
      });
    }
  },
  created() {
    this.clientsSearchString = this.$route.query.string;
    this.clientsSort = this.$route.query.sort && this.$route.query.sort.length ? JSON.parse(this.$route.query.sort) : this.clientsSort;
    this.clientsFilter = this.$route.query.filter && this.$route.query.filter.length ? JSON.parse(this.$route.query.filter) : this.clientsFilter;

  }
}
