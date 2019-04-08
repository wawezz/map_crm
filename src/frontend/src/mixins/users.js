import axios from "axios";
import AuthService from "./../services/AuthService";

export const users = {
  data() {
    return {
      usersFilter: '[]',
      usersSort: '[]',
      usersLoading: false,
      usersData: {
        checked: []
      },
      usersSearchString: '',
      usersByGroups: {},
      usersTotalCount: 0,
      userInfo: {
        avatarId: 0
      },
      usersForm: {
        name: null,
        email: null,
        password: null,
        image: null,
        roleId: 2,
        groupId: 1,
        sipId: 0,
        sipPass: '',
        id: null
      },
      usersColumns: [{
          type: 'checkbox'
        },
        {
          type: 'image',
          id: 'avatarId',
          path: 'avatarPath',
          field: 'avatarName'
        },
        {
          name: 'name',
          type: 'link',
          prefix: '/profile/',
          disableSort: true
        },
        {
          name: 'email',
          type: 'email',
          disableSort: true
        },
        {
          name: 'role',
          field: 'roleName',
          sortField: 'roleId',
          disableSort: true
        }
      ],
      usersLimit: 25
    }
  },
  computed: {
    usersPage() {
      let page = 1;
      if (this.$route.params.page) page = this.$route.params.page;

      return parseInt(page);
    }
  },
  methods: {
    initUser() {
      if (this.$route.params.id && this.user.id !== this.$route.params.id) {
        this.userInfo = {
          avatarId: 0
        };
        this.getUser(this.$route.params.id);
      } else {
        this.userInfo = this.user;
        this.usersForm.name = this.userInfo.name;
        this.usersForm.email = this.userInfo.email;
        this.usersForm.roleId = this.userInfo.roleId;
        this.usersForm.groupId = this.userInfo.groupId;
        this.usersForm.id = this.userInfo.id;
      }
    },
    getUser(id) {
      this.usersLoading = true;
      axios({
          method: "post",
          url: id ? "/v1/user/get?id=" + id : "/v1/user/get",
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
          this.userInfo = response.data;
          this.usersForm.name = this.userInfo.name;
          this.usersForm.email = this.userInfo.email;
          this.usersForm.roleId = this.userInfo.roleId;
          this.usersForm.groupId = this.userInfo.groupId;
          this.usersForm.sipId = this.userInfo.sipId;
          this.usersForm.sipPass = this.userInfo.sipPass;
          this.usersForm.id = this.userInfo.id;
          this.usersLoading = false;
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
          this.usersLoading = false;
        });
    },
    getUsers() {
      this.usersLoading = true;
      const filter = this.usersFilter !== '[]' ? JSON.stringify(this.usersFilter) : this.usersFilter;
      const sort = this.usersSort !== '[]' ? JSON.stringify(this.usersSort) : this.usersSort;

      axios({
          method: "post",
          url: "/v1/user/list?filter=" + filter + "&sort=" + sort + "&limit=" + this.usersLimit + "&offset=" + (this.usersLimit * (this.usersPage - 1)),
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
          this.usersByGroups = response.data;
          this.usersTotalCount = parseInt(response.headers['x-pagination-total']);
          this.usersLoading = false;
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
          this.usersLoading = false;
        });
    },
    addUser() {
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
      formData.append("file", this.usersForm.image);
      formData.append("form[id]", this.usersForm.id);
      formData.append("form[name]", this.usersForm.name);
      formData.append("form[email]", this.usersForm.email);
      formData.append("form[roleId]", this.usersForm.roleId);
      formData.append("form[groupId]", this.usersForm.groupId);
      formData.append("form[sipId]", this.usersForm.sipId);
      formData.append("form[sipPass]", this.usersForm.sipPass);
      formData.append("form[password]", this.usersForm.password);

      axios({
        method: "post",
        url: "/v1/user/registration",
        data: formData,
        headers: {
          Authorization: `Bearer ${AuthService.tokens.access}`
        }
      }).then(
        ({
          data
        }) => {
          this.getUsers();
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
    updateProfile() {
      if (this.inProgress === true) return;
      let formData;
      this.errors = {};

      if (!this.isValidEmail(this.usersForm.email)) {
        this.errors.email = "Incorrect email.";
        return false;
      }

      const vm = this;
      this.inProgress = true;
      /* eslint-disable */
      formData = new FormData();
      /* eslint-enable */
      formData.append("params[id]", AuthService.uid);
      formData.append("file", this.usersForm.image || '');
      formData.append("form[id]", this.usersForm.id);
      formData.append("form[name]", this.usersForm.name);
      formData.append("form[email]", this.usersForm.email);
      formData.append("form[roleId]", this.usersForm.roleId);
      formData.append("form[groupId]", this.usersForm.roleId == 1 ? 0 : this.usersForm.groupId);
      formData.append("form[sipId]", this.usersForm.sipId);
      formData.append("form[sipPass]", this.usersForm.sipPass);
      formData.append("form[updatedBy]", this.user.id);
      if (this.usersForm.password) {
        formData.append("form[password]", this.usersForm.password);
      }

      axios({
        method: "post",
        url: "/v1/user/update",
        data: formData,
        headers: {
          Authorization: `Bearer ${AuthService.tokens.access}`
        }
      }).then(
        ({
          data
        }) => {
          vm.inProgress = false;
          if (vm.usersForm.id === vm.user.id) {
            AuthService.setUser(data.result);
            vm.$store.commit("SET_USER", data.result.user);
          }
          vm.userInfo = data.result.user;
          vm.usersForm.image = null;
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
    removeUsers(redirect = false) {

      if (this.inProgress === true) return;
      let formData;
      const vm = this;

      /* eslint-disable */
      formData = new FormData();
      /* eslint-enable */
      formData.append("params[id]", AuthService.uid);
      formData.append("users", JSON.stringify(this.usersData.checked.length ? this.usersData.checked : [this.userInfo.id]));
      axios({
        method: "post",
        url: "/v1/user/remove",
        data: formData,
        headers: {
          Authorization: `Bearer ${AuthService.tokens.access}`
        }
      }).then(
        ({
          data
        }) => {
          vm.inProgress = false;

          this.usersRemoveModalVisible = false;
          if (redirect) {
            // if (vm.formData.owner) {
            //     AuthService.removeUser();
            //     this.$store.commit("CLEAR_USER");
            //     this.$router.push({ path: "/login" });
            // }
            this.$router.push({
              path: "/users/1"
            });
          } else {
            this.usersData.checked = [];
            this.getUsers();
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
    sortUsersBy(field = null) {
      if (field === null) return;
      if (this.usersSort === '[]') this.usersSort = {};
      if (!this.usersSort[field]) {
        this.usersSort[field] = 'DESC';
      } else if (this.usersSort[field] === 'DESC') {
        this.usersSort[field] = 'ASC';
      } else if (this.usersSort[field] === 'ASC') {
        delete this.usersSort[field];
      }

      if (Object.keys(this.usersSort).length === 0) this.usersSort = '[]';
      this.usersQueryControll();
      this.getUsers();
    },
    usersQueryControll() {
      if (this.$route.query.string === this.placeLeadsSearchString &&
        (this.$route.query.sort != '' && this.$route.query.sort === JSON.stringify(this.placeLeadsSort)) &&
        (this.$route.query.filter != '' && this.$route.query.filter === JSON.stringify(this.placeLeadsFilter))) return;

      if (this.placeLeadsPage != 1) {
        this.$router.push({
          name: 'users',
          params: {
            page: 1
          },
          query: this.$route.query
        });
      }
      this.$router.replace({
        query: {
          string: this.usersSearchString,
          sort: (this.usersSort !== '[]') ? JSON.stringify(this.usersSort) : '',
          filter: (this.usersFilter !== '[]') ? JSON.stringify(this.usersFilter) : ''
        }
      });
    }
  },
  created() {
    this.usersSearchString = this.$route.query.string;
    this.usersSort = this.$route.query.sort && this.$route.query.sort.length ? JSON.parse(this.$route.query.sort) : this.usersSort;
    this.usersFilter = this.$route.query.filter && this.$route.query.filter.length ? JSON.parse(this.$route.query.filter) : this.usersFilter;

  }
}
