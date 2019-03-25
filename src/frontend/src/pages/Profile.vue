<template>
  <div class="row" v-if="user">
    <div class="col-md-8">
      <div>
        <base-alert v-if="access" type="danger">{{access}}</base-alert>
        <base-alert v-if="error.message" type="warning">{{error.message}}</base-alert>
        <base-alert v-if="response.active" type="success">Data updated</base-alert>
      </div>
      <edit-profile-form
        :removeUsers="removeUsers"
        :updateProfile="updateProfile"
        :userInfo="userInfo"
        :usersForm="usersForm"
        :params="params"
      ></edit-profile-form>
    </div>
    <div class="col-md-4">
      <user-card :user="userInfo" :curUser="user"></user-card>
    </div>
  </div>
</template>
<script>
import EditProfileForm from "./Profile/EditProfileForm";
import UserCard from "./Profile/UserCard";
import { BaseAlert } from "@/components";
import { main } from "./../mixins/main";
import { users } from "./../mixins/users";
import authGuard from "./../guards/auth.guard";

export default {
  beforeRouteEnter: authGuard,
  components: {
    EditProfileForm,
    UserCard,
    BaseAlert
  },
  watch: {
    $route(to, from) {
      this.initUser();
    }
  },
  data() {
    return {};
  },
  created() {
    this.getParams();
    this.initUser();
  },
  methods: {
    handleFileUpload() {
      this.usersForm.image = this.$refs.avatar.files[0];
    },
    removeFile() {
      this.usersForm.image = null;
    },
    updateGroup() {
      if (this.usersForm.roleId != 1 && this.usersForm.groupId == 0)
        this.usersForm.groupId = 1;
    }
  },
  mixins: [main, users]
};
</script>
<style>
</style>
