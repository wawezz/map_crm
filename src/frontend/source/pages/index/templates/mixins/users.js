import axios from "axios";
import AuthService from "~/pages/index/services/AuthService";

export const users = {
    data() {
        return {
            usersFilter: '[]',
            usersSort: '[]',
            checkedUsers: [],
            usersByGroups: {},
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
                sipId: '',
                sipPass: '',
                id: null
            },
        }
    },
    computed: {

    },
    methods: {
        initUser() {
            if (this.$route.params.id && this.user.id !== this.$route.params.id) {
                this.userInfo = {
                    avatarId: 0
                };
                this.getUser(this.$route.params.id);
            } else {
                console.log('done');
                this.userInfo = this.user;
                this.usersForm.name = this.userInfo.name;
                this.usersForm.email = this.userInfo.email;
                this.usersForm.roleId = this.userInfo.roleId;
                this.usersForm.groupId = this.userInfo.groupId;
                this.usersForm.id = this.userInfo.id;
            }
        },
        getUser(id) {
            axios({
                method: "post",
                url: id?"/v1/user/get?id=" + id:"/v1/user/get",
                data: {
                    params: { id: AuthService.uid }
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
        getUsers() {
            const filter = this.usersFilter !== '[]' ? JSON.stringify(this.usersFilter) : this.usersFilter;
            const sort = this.usersSort !== '[]' ? JSON.stringify(this.usersSort) : this.usersSort;
            
            axios({
                method: "post",
                url: "/v1/user/list?filter="+ filter +"&sort="+ sort,
                data: {
                    params: { id: AuthService.uid }
                },
                headers: {
                    Authorization: `Bearer ${AuthService.tokens.access}`
                }
            })
                .then(response => {
                    this.usersByGroups = response.data;
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
                ({ data }) => {
                    this.getUsers();
                    vm.inProgress = false;
                    clearTimeout(vm.response.timeoutID);
                    vm.response.active = true;
                    vm.response.timeoutID = setTimeout(function () {
                        vm.response.active = false;
                    }, 3000);
                },
                ({ response }) => {
                    vm.inProgress = false;

                    const { data } = response;

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
            formData.append("file", this.usersForm.image);
            formData.append("form[id]", this.usersForm.id);
            formData.append("form[name]", this.usersForm.name);
            formData.append("form[email]", this.usersForm.email);
            formData.append("form[roleId]", this.usersForm.roleId);
            formData.append("form[groupId]", this.usersForm.groupId);
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
                ({ data }) => {
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
                ({ response }) => {
                    vm.inProgress = false;

                    const { data } = response;

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
            formData.append("users", JSON.stringify(this.checkedUsers.length ? this.checkedUsers : [this.userInfo.id]));
            axios({
                method: "post",
                url: "/v1/user/remove",
                data: formData,
                headers: {
                    Authorization: `Bearer ${AuthService.tokens.access}`
                }
            }).then(
                ({ data }) => {
                    vm.inProgress = false;
                    let elem = this.$refs.usersRemoveClose;
                    elem.click();
                    if (redirect) {
                        // if (vm.formData.owner) {
                        //     AuthService.removeUser();
                        //     this.$store.commit("CLEAR_USER");
                        //     this.$router.push({ path: "/login" });
                        // }
                        this.$router.push({ path: "/users" });
                    } else {
                        this.checkedUsers = [];
                        this.getUsers();
                    }
                },
                ({ response }) => {
                    vm.inProgress = false;

                    const { data } = response;

                    if (data.error) {
                        console.error(data.error);
                    } else {
                        console.error("Unexpected error", data.error);
                    }
                }
            );
        }
    }
}
