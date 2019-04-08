import axios from "axios";
import moment from "moment";
import AuthService from "./../services/AuthService";

export const tasks = {
  data() {
    return {
      tasksFilter: '[]',
      tasksSort: '[]',
      tasksLoading: false,
      tasks: {},
      currentTask: {
        id: null,
        result: null
      },
      taskResponsibleId: null,
      tasksForm: {
        elementId: null,
        elementType: null,
        elementName: null,
        type: 'connect',
        responsible: null,
        createdBy: null,
        comment: null,
        eventDate: null,
        id: null
      },
    }
  },
  methods: {
    getTasks() {
      this.tasksLoading = true;
      const filter = this.tasksFilter !== '[]' ? JSON.stringify(this.tasksFilter) : this.tasksFilter;
      const sort = this.tasksSort !== '[]' ? JSON.stringify(this.tasksSort) : this.tasksSort;
      axios({
          method: "post",
          url: "/v1/task/list?filter=" + filter + "&sort=" + sort,
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
          this.tasks = response.data;
          this.tasksLoading = false;
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
          this.tasksLoading = false;
        });
    },
    getTaskData(id) {
      this.currentTask.id = id;
    },
    closeTask() {
      if (this.inProgress === true) return;
      let formData;
      this.errors = {};

      const vm = this;
      this.inProgress = true;
      /* eslint-disable */
      formData = new FormData();
      /* eslint-enable */
      formData.append("form[id]", this.currentTask.id);
      formData.append("form[result]", this.currentTask.result);
      // formData.append("form[createdBy]", this.user.id);

      axios({
        method: "post",
        url: "/v1/task/close",
        data: formData,
        headers: {
          Authorization: `Bearer ${AuthService.tokens.access}`
        }
      }).then(
        ({
          data
        }) => {
          this.taskCloseModalVisible = false;

          if (vm.notes) vm.notes = vm.notes.concat(data.result);
          this.getTasks();

          vm.inProgress = false;
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
    removeTask() {
      if (this.inProgress === true) return;

      this.errors = {};

      const vm = this;
      this.inProgress = true;

      axios({
        method: "post",
        url: "/v1/task/delete?id=" + this.currentTask.id + "&createdBy=" + this.user.id,
        headers: {
          Authorization: `Bearer ${AuthService.tokens.access}`
        }
      }).then(
        ({
          data
        }) => {
          this.taskRemoveModalVisible = false;

          this.getTasks();

          vm.inProgress = false;
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
    addTask(refresh = false) {
      if (this.inProgress === true) return;
      let formData;
      this.errors = {};

      const vm = this;
      this.inProgress = true;
      /* eslint-disable */
      formData = new FormData();
      /* eslint-enable */
      formData.append("form[elementId]", this.tasksForm.elementId);
      formData.append("form[elementType]", this.tasksForm.elementType);
      formData.append("form[type]", this.tasksForm.type);
      // formData.append("form[createdBy]", this.user.id);
      formData.append("form[responsible]", this.tasksForm.responsible);
      formData.append("form[comment]", this.tasksForm.comment);
      formData.append("form[eventDate]", this.tasksForm.eventDate ? moment(this.tasksForm.eventDate).format("YYYY-MM-DD HH:mm:ss") : '');
      this.taskResponsibleId = this.tasksForm.responsible;

      axios({
        method: "post",
        url: "/v1/task/add",
        data: formData,
        headers: {
          Authorization: `Bearer ${AuthService.tokens.access}`
        }
      }).then(
        ({
          data
        }) => {
          if (this.taskResponsibleId === this.user.id || refresh === true) this.getTasks();

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
    }
  }
}
