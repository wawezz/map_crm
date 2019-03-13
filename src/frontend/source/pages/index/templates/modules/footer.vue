<template>
  <div v-if="user">
    <dialer></dialer>
    <footer class="footer text-center">2018 &copy; Map CRM</footer>
  </div>
</template>
<script>
import Vue from "vue";
import dialer from "./dialer.vue";
import { mapState } from "vuex";

export default {
  components: {
    dialer: dialer
  },
  computed: {
    ...mapState(["user"])
  },
  data() {
    return {};
  }
};
</script>
