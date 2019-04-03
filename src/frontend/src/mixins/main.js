import axios from "axios";
import {
  mapState
} from "vuex";
import AuthService from "./../services/AuthService";

export const main = {
  data() {
    return {
      ws: null,
      access: null,
      statuses: {},
      statusesById: {},
      currenciesFiltered: {},
      countries: {},
      countriesById: {},
      currencies: {},
      currenciesById: {},
      products: {},
      productsById: {},
      managersById: {},
      managerGroups: {},
      elementsResult: [],
      managersFilter: '[]',
      managersSort: '[]',
      productsFilter: '[]',
      productsSort: '[]',
      params: {
        roles: {},
        groups: {}
      },
      inProgress: false,
      error: {
        timeoutID: null,
        message: null
      },
      response: {
        timeoutID: null,
        active: false
      },
      listError: null,
      config: {
        date: {
          format: "yyyy-MM-dd"
        }
      }
    }
  },
  destroyed() {
    if (this.ws) {
      this.ws.close();
    }
  },
  computed: {
    ...mapState(["user"]),
    noteTypes() {
      const data = [];
      const {
        state
      } = this.$store;

      for (let i in state.noteTypes) {
        data[state.noteTypes[i].id] = state.noteTypes[i].title;
      }

      return data;
    }
  },
  created() {
    AuthService.init();
  },
  methods: {
    getProducts() {
      const filter = this.productsFilter !== '[]' ? JSON.stringify(this.productsFilter) : this.productsFilter;
      const sort = this.productsSort !== '[]' ? JSON.stringify(this.productsSort) : this.productsSort;

      axios({
          method: "post",
          url: "/v1/product/list?filter=" + filter + "&sort=" + sort,
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
          this.products = response.data;
          for (let i in this.products) {
            this.productsById[this.products[i].id] = this.products[i];
          }
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
    getManagers() {
      const filter = this.managersFilter !== '[]' ? JSON.stringify(this.managersFilter) : this.managersFilter;
      const sort = this.managersSort !== '[]' ? JSON.stringify(this.managersSort) : this.managersSort;
      axios({
          method: "post",
          url: "/v1/user/list?filter=" + filter + "&sort=" + sort,
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
          this.managerGroups = response.data;

          for (let i in this.managerGroups) {
            for (let ii in this.managerGroups[i].users) {
              this.managersById[this.managerGroups[i].users[ii].id] = this.managerGroups[i].users[ii];
            }
          }
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
    getElementsByName(name = '') {
      name = name.trim();

      if (name.length < 3) return;
      axios
        .get("/v1/params/elements?name=" + name)
        .then(response => {
          this.elementsResult = response.data;
        })
        .catch(e => {
          console.log(e);
        });
    },
    getParams(filter) {
      const jsonFilter = filter ? "?filter=" + JSON.stringify(filter) : '';
      axios
        .get("/v1/params/all" + jsonFilter)
        .then(response => {
          if (filter) {
            for (let i in filter) {
              this[filter[i]] = response.data[filter[i]];
              for (let ii in this[filter[i]]) {
                this[filter[i] + "ById"][this[filter[i]][ii].id] = this[filter[i]][ii];
              }
            }
            this.currenciesFiltered = this.currencies;
          } else {
            this.params = response.data;
          }
        })
        .catch(e => {
          console.log(e);
        });
    },
    isValidEmail(email) {
      /* eslint-disable */
      const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      /* eslint-enable */
      return email && re.test(email.trim());
    },
    isValidPassword(password) {
      return password !== "";
    },
  }
}
