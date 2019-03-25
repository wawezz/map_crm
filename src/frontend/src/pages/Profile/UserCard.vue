<template>
  <card type="user">
    <p class="card-text"></p>
    <div class="author">
      <div class="block block-one"></div>
      <div class="block block-two"></div>
      <div class="block block-three"></div>
      <div class="block block-four"></div>
      <span>
        <img
          class="avatar"
          :src="user.avatarId>0?user.avatar:require('./../../assets/images/avatar.png')"
          alt="..."
        >
        <h5 class="title">{{user.name}}</h5>
      </span>
      <p class="description">{{user.email}}</p>
    </div>
    <p></p>
    <router-link v-if="curUser.roleId == 1" :to="'/tasks/' + user.id" class="text-white">
      <button class="btn btn-block btn-primary" fill>User tasks</button>
    </router-link>
  </card>
</template>
<script>
export default {
  props: {
    curUser: {
      type: Object,
      default: () => {
        return {};
      }
    },
    user: {
      type: Object,
      default: () => {
        return {};
      }
    }
  }
};
</script>
<style>
</style>
