<template>
  <card>
    <h5 slot="header" class="title">Edit Profile</h5>
    <div class="row">
      <div class="col-md-6 pr-md-1">
        <base-input label="Full name" :placeholder="userInfo.name" v-model="usersForm.name"></base-input>
      </div>
      <div class="col-md-6 pl-md-1">
        <base-input
          label="Email"
          type="email"
          :placeholder="userInfo.email"
          v-model="usersForm.email"
        ></base-input>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6 pr-md-1 text-left">
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
      <div class="col-md-6 pl-md-1 text-center">
        <div>{{(usersForm.image)?usersForm.image.name:''}}</div>
        <a
          href="#"
          @click.prevent="removeFile()"
          v-if="usersForm.image"
          class="btn btn-block"
        >Remove</a>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <label>Select role</label>
        <select
          class="form-control form-control-line"
          @change="updateGroup()"
          v-model="usersForm.roleId"
        >
          <option v-for="role in params.roles" :key="role.id" :value="role.id">{{role.name}}</option>
        </select>
      </div>
      <div class="col-md-6 pl-md-1">
        <base-input
          label="Password"
          type="password"
          :placeholder="userInfo.password"
          v-model="usersForm.password"
        ></base-input>
      </div>
    </div>
    <div class="row" v-if="usersForm.roleId == 2">
      <div class="col-md-4 pr-md-1">
        <label>Select group</label>
        <select
          class="form-control form-control-line"
          @change="updateGroup()"
          v-model="usersForm.groupId"
        >
          <option v-for="group in params.groups" :key="group.id" :value="group.id">{{group.name}}</option>
        </select>
      </div>
      <div class="col-md-4 px-md-1">
        <base-input label="SIP id" v-model="usersForm.sipId"></base-input>
      </div>
      <div class="col-md-4 pl-md-1">
        <base-input label="SIP password" v-model="usersForm.sipPass"></base-input>
      </div>
    </div>
    <base-button
      slot="footer"
      type="primary"
      class="ml-md-2 mr-md-2"
      fill
      @click.prevent="updateProfile"
    >Save</base-button>
    <base-button
      slot="footer"
      type="default"
      data-toggle="modal"
      data-target="#removeModal"
      class="ml-md-2 mr-md-2"
      fill
      @click="removeModalVisible = true"
    >Remove profile</base-button>
    <modal
      :show.sync="removeModalVisible"
      id="removeModal"
      :centered="false"
      :show-close="true"
      class="text-center"
    >
      <h4 class="black">
        Remove user
        <b>{{userInfo.name}}</b>?
      </h4>
      <button class="btn btn-default" @click="removeModalVisible = false">No</button>
      <button class="btn btn-danger" @click.prevent="removeUsers(true)">Yes</button>
    </modal>
  </card>
</template>
<script>
import Modal from "@/components/Modal";
export default {
  data() {
    return {
      removeModalVisible: false
    };
  },
  props: {
    userInfo: {
      type: Object,
      default: () => {
        return {};
      }
    },
    usersForm: {
      type: Object,
      default: () => {
        return {};
      }
    },
    params: {
      type: Object,
      default: () => {
        return {};
      }
    },
    updateProfile: { type: Function },
    removeUsers: { type: Function }
  },
  components: {
    Modal
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
  }
};
</script>
<style>
</style>
