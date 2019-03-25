<template>
  <nav
    class="navbar navbar-expand-lg navbar-absolute"
    :class="{'bg-white': showMenu, 'navbar-transparent': !showMenu}"
  >
    <div class="container-fluid">
      <div class="navbar-wrapper">
        <div class="navbar-toggle d-inline" :class="{toggled: $sidebar.showSidebar}">
          <button
            type="button"
            class="navbar-toggler"
            aria-label="Navbar toggle button"
            @click="toggleSidebar"
          >
            <span class="navbar-toggler-bar bar1"></span>
            <span class="navbar-toggler-bar bar2"></span>
            <span class="navbar-toggler-bar bar3"></span>
          </button>
        </div>
        <div class="navbar-brand">{{routeName}}</div>
      </div>
      <button
        class="navbar-toggler"
        type="button"
        @click="toggleMenu"
        data-toggle="collapse"
        data-target="#navigation"
        aria-controls="navigation-index"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-bar navbar-kebab"></span>
        <span class="navbar-toggler-bar navbar-kebab"></span>
        <span class="navbar-toggler-bar navbar-kebab"></span>
      </button>

      <collapse-transition>
        <div class="collapse navbar-collapse show" v-show="showMenu">
          <ul class="navbar-nav" :class="$rtl.isRTL ? 'mr-auto' : 'ml-auto'">
            <base-dropdown
              tag="li"
              :menu-on-right="!$rtl.isRTL"
              title-tag="a"
              class="nav-item"
              menu-classes="dropdown-navbar"
            >
              <a
                slot="title"
                href="#"
                class="dropdown-toggle nav-link"
                data-toggle="dropdown"
                aria-expanded="true"
                @click.prevent
              >
                <div class="photo">
                  <img
                    :src="user.avatarId>0?user.avatar:require('./../../assets/images/avatar.png')"
                  >
                </div>
                <b class="caret d-none d-lg-block d-xl-block"></b>
                <p class="d-lg-none" @click.prevent>{{user.name}}</p>
              </a>
              <li class="nav-link">
                <router-link :to="'/profile/' + user.id" class="nav-item dropdown-item">Profile</router-link>
              </li>
              <div class="dropdown-divider"></div>
              <li class="nav-link">
                <a href="#" class="nav-item dropdown-item" @click.prevent="hold()">Stop session</a>
              </li>
              <li class="nav-link">
                <a href="#" class="nav-item dropdown-item" @click.prevent="logout()">Log out</a>
              </li>
            </base-dropdown>
          </ul>
        </div>
      </collapse-transition>
    </div>
  </nav>
</template>
<script>
import { CollapseTransition } from "vue2-transitions";
import { mapState } from "vuex";
import axios from "axios";
import AuthService from "./../../services/AuthService";

export default {
  components: {
    CollapseTransition
  },
  computed: {
    ...mapState(["user"]),
    routeName() {
      const { name } = this.$route;
      return this.capitalizeFirstLetter(name);
    },
    isRTL() {
      return this.$rtl.isRTL;
    }
  },
  data() {
    return {
      onHold: false,
      activeNotifications: false,
      showMenu: false
    };
  },
  methods: {
    capitalizeFirstLetter(string) {
      return string.charAt(0).toUpperCase() + string.slice(1);
    },
    toggleNotificationDropDown() {
      this.activeNotifications = !this.activeNotifications;
    },
    closeDropDown() {
      this.activeNotifications = false;
    },
    toggleSidebar() {
      this.$sidebar.displaySidebar(!this.$sidebar.showSidebar);
    },
    hideSidebar() {
      this.$sidebar.displaySidebar(false);
    },
    toggleMenu() {
      this.showMenu = !this.showMenu;
    },
    hold() {
      axios({
        method: "post",
        url: "/v1/user/hold?id=" + this.user.id,
        data: {
          params: { id: AuthService.uid }
        },
        headers: {
          Authorization: `Bearer ${AuthService.tokens.access}`
        }
      })
        .then(response => {
          if (response.data) this.onHold = true;
        })
        .catch(e => {
          const data = e.response.data;

          console.error("Unexpected error", data);
        });
    },
    logout() {
      axios({
        method: "post",
        url: "/v1/user/logout",
        data: {
          params: { id: AuthService.uid }
        },
        headers: {
          Authorization: `Bearer ${AuthService.tokens.access}`
        }
      })
        .then(response => {
          if (response.data) {
            AuthService.removeUser();
            this.$store.commit("CLEAR_USER");
            this.$router.push({ path: "/login" });
          }
        })
        .catch(e => {
          const data = e.response.data;

          console.error("Unexpected error", data);
        });
    }
  }
};
</script>
<style>
</style>
