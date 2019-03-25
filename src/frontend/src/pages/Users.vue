<template>
  <div class="row" v-if="user">
    <div class="col-sm-12 mb-3">
      <base-button
        data-toggle="modal"
        data-target="userAdd"
        type="button"
        class="btn btn-primary"
        @click="userAddModalVisible = true"
      >
        <i class="tim-icons icon-simple-add"></i> Add user
      </base-button>
      <base-button
        type="button"
        class="btn btn-default ml-3"
        @click="toogleHidden"
      >{{toogleHidenUsers?'Hide api users':'Show api users'}}</base-button>
      <base-button
        v-if="checkedUsers.length>0"
        data-toggle="modal"
        data-target="usersRemove"
        type="button"
        class="btn btn-danger ml-3"
        @click="usersRemoveModalVisible = true"
      >
        <i class="tim-icons icon-trash-simple"></i> Remove users
      </base-button>
    </div>
    <div class="col-sm-12">
      <base-alert v-if="listError" type="danger">{{listError}}</base-alert>
    </div>
    <div class="col-12">
      <card title="Users">
        <div class="table">
          <table class="table tablesorter">
            <thead>
              <tr>
                <th></th>
                <th></th>
                <th>NAME</th>
                <th>EMAIL</th>
                <th>ROLE</th>
              </tr>
            </thead>
            <tbody v-for="group in usersByGroups" :key="group.id">
              <tr>
                <th colspan="7">
                  GROUP: {{group.name}}
                  <span
                    v-if="group.users.length"
                    class="users-list-count"
                  >({{group.users.length}} users)</span>
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
                  <base-checkbox
                    v-if="listUser.id != user.id"
                    :id="'checkbox_' + listUser.id"
                    :value="listUser.id"
                    v-model="checkedUsers"
                  ></base-checkbox>
                </td>
                <td style="width: 60px;">
                  <img
                    :src="listUser.avatarId>0?listUser.avatarPath + listUser.avatarName:require('./../assets/images/avatar.png')"
                    alt="user img"
                    class="img-circle"
                    width="30"
                  >
                </td>
                <td>
                  <router-link :to="{ path: '/profile/' + listUser.id}">{{listUser.name}}</router-link>
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
        <div class="col-12">
          <pagination
            v-if="usersTotalCount"
            :total="usersTotalCount"
            :current="usersPage"
            :size="usersLimit"
            :prefix="'/users/'"
          />
        </div>
        <modal
          modalClasses="modal-lg"
          :show.sync="userAddModalVisible"
          id="userAdd"
          :centered="false"
          :show-close="true"
        >
          <h4 class="black">Add user</h4>
          <div class="row">
            <div class="col-12">
              <base-alert v-if="error.message" type="danger">{{error.message}}</base-alert>
              <base-alert v-if="response.active" type="success">user added</base-alert>
            </div>
          </div>
          <card>
            <div class="row">
              <div class="col-4">
                <base-input label="Full name" placeholder="Joe Division" v-model="usersForm.name"></base-input>
              </div>
              <div class="col-4">
                <base-input
                  label="Email"
                  type="email"
                  placeholder="Aphex_Twin@gmail.com"
                  v-model="usersForm.email"
                ></base-input>
              </div>

              <div class="col-4">
                <base-input label="Password" type="password" v-model="usersForm.password"></base-input>
              </div>
            </div>
            <div class="row">
              <div class="col-4">
                <label>Select role</label>
                <select
                  class="form-control form-control-line"
                  @change="updateGroup()"
                  v-model="usersForm.roleId"
                >
                  <option v-for="role in params.roles" :key="role.id" :value="role.id">{{role.name}}</option>
                </select>
              </div>
              <div class="col-4">
                <label>Avatar</label>
                <div>
                  <label for="avatarInput" class="btn btn-primary">Select file</label>
                  <input
                    type="file"
                    id="avatarInput"
                    ref="avatar"
                    class="hidden"
                    v-on:change="handleFileUpload()"
                  >
                </div>
              </div>
              <div class="col-4 text-center">
                <div>{{(usersForm.image)?usersForm.image.name:''}}</div>
                <a href="#" @click.prevent="removeFile()" v-if="usersForm.image" class="btn">Remove</a>
              </div>
            </div>
            <div class="row" v-if="usersForm.roleId == 2">
              <div class="col-4">
                <label>Select group</label>
                <select
                  class="form-control form-control-line"
                  @change="updateGroup()"
                  v-model="usersForm.groupId"
                >
                  <option
                    v-for="group in params.groups"
                    :key="group.id"
                    :value="group.id"
                  >{{group.name}}</option>
                </select>
              </div>
              <div class="col-4">
                <base-input label="SIP id" v-model="usersForm.sipId"></base-input>
              </div>
              <div class="col-4">
                <base-input label="SIP password" v-model="usersForm.sipPass"></base-input>
              </div>
            </div>
            <base-button
              slot="footer"
              type="primary"
              class="text-center"
              fill
              @click.prevent="addUser"
            >Save</base-button>
          </card>
        </modal>
        <modal
          :show.sync="usersRemoveModalVisible"
          id="usersRemove"
          :centered="false"
          :show-close="true"
          class="text-center"
        >
          <h4 class="black">Remove users?</h4>
          <button class="btn btn-default" @click="usersRemoveModalVisible = false">No</button>
          <button class="btn btn-danger" @click.prevent="removeUsers(false)">Yes</button>
        </modal>
      </card>
    </div>
  </div>
</template>
<script>
import { BaseAlert } from "@/components";
import { Pagination } from "@/components";
import { Modal } from "@/components";
import { main } from "./../mixins/main";
import { users } from "./../mixins/users";
import authGuard from "./../guards/auth.guard";

export default {
  beforeRouteEnter: authGuard,
  components: {
    BaseAlert,
    Modal,
    Pagination
  },
  watch: {
    $route(to, from) {
      this.getUsers();
    }
  },
  data() {
    return {
      userAddModalVisible: false,
      usersRemoveModalVisible: false,
      toogleHidenUsers: false
    };
  },
  created() {
    this.usersFilter = { roleId: "!=|3" };
    this.getUsers();
    this.getParams();
  },
  methods: {
    handleFileUpload() {
      this.usersForm.image = this.$refs.avatar.files[0];
    },
    removeFile() {
      this.usersForm.image = null;
    },
    toogleHidden() {
      if (this.toogleHidenUsers) {
        this.toogleHidenUsers = false;
        this.usersFilter = { roleId: "!=|3" };
      } else {
        this.toogleHidenUsers = true;
        this.usersFilter = [];
      }
      this.getUsers();
    }
  },
  mixins: [main, users]
};
</script>
<style>
</style>