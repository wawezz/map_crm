<template>
  <div v-if="user">
    <div id="page-wrapper">
      <div class="container-fluid p-t-20">
        <div class="white-box">
          <div class="p-t-10 row">
            <div class="col-sm-12">
              <ul class="side-icon-text pull-left">
                <li>
                  <button
                    data-toggle="modal"
                    data-target=".bs-userAdd-modal-lg"
                    type="button"
                    class="btn btn-info"
                  >
                    <i class="fa fa-plus m-r-5"></i>
                    <span>Добавить пользователя</span>
                  </button>
                </li>
                <li>
                  <button
                    v-if="checkedUsers.length>0"
                    data-toggle="modal"
                    data-target=".bs-usersRemove-modal-md"
                    type="button"
                    class="btn btn-danger"
                  >
                    <i class="fa fa-trash-o m-r-5"></i>
                    <span>Удалить пользователей</span>
                  </button>
                </li>
              </ul>
            </div>
          </div>
          {{listError}}
          <table
            id="demo-foo-addrow"
            class="table m-t-15 table-hover contact-list footable-loaded users-list footable"
            data-page-size="10"
            v-if="!listError"
          >
            <thead>
              <tr>
                <th></th>
                <th></th>
                <th>ИМЯ</th>
                <th>ПОЧТА</th>
                <th>РОЛЬ</th>
              </tr>
            </thead>
            <tbody v-for="group in usersByGroups" :key="group.id">
              <tr class="footable-even users-list-group" v-if="group.name!='ADMINS'">
                <th colspan="7">
                  ГРУППА: {{group.name}}
                  <span
                    v-if="group.users.length"
                    class="users-list-count"
                  >({{group.users.length}} пользователей)</span>
                </th>
              </tr>
              <tr
                class="footable-even"
                style="display: table-row;"
                :class="listUser.isOnline>1?' online':listUser.isOnline==1?' hold':''"
                v-for="listUser in group.users"
                :key="listUser.id"
              >
                <td style="width: 40px;">
                  <div v-if="listUser.id != user.id" class="checkbox checkbox-circle checkbox-info">
                    <input
                      :id="'checkbox23_' + listUser.id"
                      type="checkbox"
                      :value="listUser.id"
                      v-model="checkedUsers"
                    >
                    <label :for="'checkbox23_' + listUser.id"></label>
                  </div>
                </td>
                <td style="width: 60px;">
                  <img
                    :src="listUser.avatarId>0?listUser.avatarPath + listUser.avatarName:require('./images/avatar.png')"
                    alt="user img"
                    class="img-circle"
                    width="30"
                  >
                </td>
                <td>
                  <router-link :to="{ path: '/user/' + listUser.id}">{{listUser.name}}</router-link>
                </td>
                <td>
                  <a :href="'mail-to:' + listUser.email">{{listUser.email}}</a>
                </td>
                <td>
                  <span class="label label-success">{{listUser.roleName}}</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div
        class="modal fade bs-userAdd-modal-lg"
        tabindex="-1"
        role="dialog"
        aria-labelledby="myLargeModalLabel"
        aria-hidden="true"
        style="display: none;"
      >
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h4 class="modal-title" id="myLargeModalLabel">Добавить пользователя</h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal form-material">
                <div class="alert alert-success" v-if="response.active">пользователь добавлен</div>
                <div class="alert alert-danger" v-if="error.message">{{error.message}}</div>
                <div class="form-group">
                  <label class="col-md-12">Полное имя</label>
                  <div class="col-md-12">
                    <input
                      type="text"
                      v-model="usersForm.name"
                      placeholder="Joe Division"
                      class="form-control form-control-line"
                    >
                  </div>
                </div>
                <div class="form-group">
                  <label for="example-email" class="col-md-12">Email</label>
                  <div class="col-md-12">
                    <input
                      type="email"
                      v-model="usersForm.email"
                      placeholder="Aphex_Twin@gmail.com"
                      class="form-control form-control-line"
                      name="example-email"
                      id="example-email"
                    >
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-md-12">Пароль</label>
                  <div class="col-md-12">
                    <input
                      type="password"
                      v-model="usersForm.password"
                      class="form-control form-control-line"
                    >
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-12">Фотография</label>
                  <div class="col-sm-12">
                    <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                      <div class="form-control" data-trigger="fileinput">
                        <i v-if="usersForm.image" class="glyphicon glyphicon-file"></i>
                        <span
                          class="fileinput-filename"
                        >{{(usersForm.image)?usersForm.image.name:''}}</span>
                      </div>
                      <span class="input-group-addon btn btn-default btn-file">
                        <span class="fileinput-new">Выберете файл</span>
                        <input type="hidden">
                        <input
                          type="file"
                          id="avatar"
                          ref="avatar"
                          v-on:change="handleFileUpload()"
                        >
                      </span>
                      <a
                        href="#"
                        @click.prevent="removeFile()"
                        v-if="usersForm.image"
                        class="input-group-addon btn btn-default"
                        data-dismiss="fileinput"
                      >Удалить</a>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-12">Выберете роль</label>
                  <div class="col-sm-12">
                    <select class="form-control form-control-line" v-model="usersForm.roleId">
                      <option
                        v-for="role in params.roles"
                        :key="role.id"
                        :value="role.id"
                      >{{role.name}}</option>
                    </select>
                  </div>
                </div>
                <span v-if="usersForm.roleId == 2">
                  <div class="form-group">
                    <label class="col-sm-12">Выберете Группу</label>
                    <div class="col-sm-12">
                      <select class="form-control form-control-line" v-model="usersForm.groupId">
                        <option
                          v-for="group in params.groups"
                          :key="group.id"
                          :value="group.id"
                        >{{group.name}}</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="example-email" class="col-md-12">SIP id</label>
                    <div class="col-md-12">
                      <input
                        type="text"
                        v-model="usersForm.sipId"
                        class="form-control form-control-line"
                      >
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-12">SIP password</label>
                    <div class="col-md-12">
                      <input
                        type="password"
                        v-model="usersForm.sipPass"
                        class="form-control form-control-line"
                      >
                    </div>
                  </div>
                </span>
                <div class="form-group">
                  <div class="col-sm-12">
                    <button
                      class="btn btn-success fRigh"
                      @click.prevent="addUser()"
                    >Добавить пользователя</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <div
        class="modal fade bs-usersRemove-modal-md"
        tabindex="-1"
        role="dialog"
        aria-labelledby="mySmallModalLabel"
        aria-hidden="true"
        style="display: none;"
      >
        <div class="modal-dialog modal-md">
          <div class="modal-content">
            <div class="modal-header">
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-hidden="true"
                ref="usersRemoveClose"
              >×</button>
              <h4 class="modal-title" id="mySmallModalLabel">Удалить пользователей?</h4>
            </div>
            <div class="modal-body">
              <button class="btn btn-success button" data-dismiss="modal" aria-hidden="true">Нет</button>
              <button class="btn btn-danger" @click.prevent="removeUsers()">Да</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Vue from "vue";
import { main } from "./mixins/main";
import { users } from "./mixins/users";
import authGuard from "./../components/guards/auth.guard";

export default {
  beforeRouteEnter: authGuard,
  data() {
    return {
      data: {}
    };
  },
  created() {
    this.usersFilter = { roleId: "!=|3" };
    this.getUsers();
    this.getParams();
  },
  mounted() {},
  methods: {
    handleFileUpload() {
      this.usersForm.image = this.$refs.avatar.files[0];
    },
    removeFile() {
      this.usersForm.image = null;
    }
  },
  mixins: [main, users],
  components: {}
};
</script>

<style scoped>
.users-list td,
.users-list th {
  vertical-align: middle !important;
}
.users-list td .checkbox label:before,
.users-list td .checkbox label:after {
  margin-top: 4px;
}
.users-list > tbody + tbody {
  border-top: 0;
}
.users-list > tbody > tr > td {
  padding: 10px 8px;
}
.users-list-group th {
  color: #666;
}
.users-list-count {
  font-size: 12px;
  color: #999;
}
.footable-even {
  border-left: 6px #b90400 solid;
}
.footable-even.online {
  border-color: #00de70;
}
.footable-even.hold {
  border-color: #ffd400;
}
</style>