<template>
  <div class="wrapper">
    <side-bar>
      <template slot="links">
        <sidebar-link
          to="/dashboard"
          :name="$t('sidebar.dashboard')"
          icon="tim-icons icon-chart-pie-36"
        />
        <sidebar-link to="/leads/1" :name="$t('leads')" icon="tim-icons icon-pencil"/>
        <sidebar-link
          to="/place-leads/1"
          :name="$t('place leads')"
          icon="tim-icons icon-square-pin"
        />
        <sidebar-link to="/clients/1" :name="$t('clients')" icon="tim-icons icon-badge"/>
        <sidebar-link to="/users/1" :name="$t('users')" icon="tim-icons icon-single-02"/>
        <sidebar-link to="/events/1" :name="$t('events')" icon="tim-icons icon-calendar-60"/>
        <sidebar-link
          :to="'/tasks/' + user.id"
          :name="$t('tasks')"
          icon="tim-icons icon-bullet-list-67"
        />
        <sidebar-link to="/calls/1" :name="$t('calls')" icon="tim-icons icon-headphones"/>
      </template>
    </side-bar>
    <div class="main-panel">
      <top-navbar></top-navbar>

      <dashboard-content @click.native="toggleSidebar"></dashboard-content>

      <content-footer></content-footer>
    </div>
  </div>
</template>
<style lang="scss">
</style>
<script>
import TopNavbar from "./TopNavbar.vue";
import ContentFooter from "./ContentFooter.vue";
import DashboardContent from "./Content.vue";
import MobileMenu from "./MobileMenu";
import { mapState } from "vuex";
export default {
  components: {
    TopNavbar,
    ContentFooter,
    DashboardContent,
    MobileMenu
  },
  computed: {
    ...mapState(["user"])
  },
  methods: {
    toggleSidebar() {
      if (this.$sidebar.showSidebar) {
        this.$sidebar.displaySidebar(false);
      }
    }
  }
};
</script>
