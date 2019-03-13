<template>
  <div v-if="user">
        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row bg-title">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <h4 class="page-title">Dashboard</h4> 
                    </div>
                </div>
            
                <div class="white-box">
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
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
import authGuard from "./../components/guards/auth.guard";

export default {
  beforeRouteEnter: authGuard,
  data() {
    return {
      data: {},
      errors: {}
    };
  },
  created() {
    if(!this.user) this.$router.push({ path: "/login" });
  },
  mounted() {},
  computed: {
  },
  methods: {
    toggleSidebar(data){
      this.hideSidebar = data;
    }
  },
  mixins: [main],
  components: {
  }
};
</script>