<template>
  <div v-if="user">
      <nav class="navbar navbar-default navbar-static-top m-b-0">
            <div class="navbar-header" :style="onHold?'background: red;':''">
                <div class="top-left-part">
                    <ul class="nav navbar-top-links navbar-left">
                        <li><a href="#"  @click.prevent="toggleSidebar()" class="waves-effect waves-light"><i class="ti-menu"></i></a></li>
                    </ul>
                </div> 
                <div class="nav navbar-top-links left-right pull-left m-l-15 m-t-10">
                  <h4 class="page-title-navbar">{{ $route.meta.title }}</h4>
                </div>
                 <ul class="nav navbar-top-links navbar-right pull-right">
                    <li class="dropdown">
                        <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#"> <img :src='user.avatarId>0?user.avatar:require("./images/avatar.png")' alt="user-img" width="36" class="img-circle"><b class="hidden-xs">{{user.name}}</b><span class="caret"></span> </a>
                        <ul class="dropdown-menu dropdown-user animated flipInY">
                            <li>
                                <div class="dw-user-box">
                                    <div class="u-img"><img :src='user.avatarId>0?user.avatar:require("./images/avatar.png")' alt="user" /></div>
                                    <div class="u-text">
                                        <h4>{{user.name}}</h4>
                                        <p class="text-muted">{{user.email}}</p></div>
                                </div>
                            </li>
                            <li role="separator" class="divider"></li>
                            <li><router-link :to="'/user/' + user.id"><i class="ti-user"></i> Профиль</router-link></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="#" @click.prevent="hold()"><i class="fa fa-pause-circle"></i> Приостановить сессию</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="#" @click.prevent="logout()"><i class="fa fa-power-off"></i> Выйти</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="navbar-default sidebar" role="navigation">
            <div class="sidebar-nav slimscrollsidebar">
                <ul class="nav" id="side-menu">
                    <li> <router-link :to="'/'" class="waves-effect"><i class="mdi mdi-av-timer fa-fw" data-icon="v"></i> <span class="hide-menu">Dashboard</span></router-link></li>
                    <li> <router-link :to="'/leads'" class="waves-effect"><i class=" mdi mdi-format-list-bulleted fa-fw"></i> <span class="hide-menu">Лиды</span></router-link></li>
                    <li> <router-link :to="'/clients'"  :class="'waves-effect'"><i class=" mdi mdi-contacts fa-fw"></i> <span class="hide-menu">Клиенты</span></router-link></li>
                    <li v-if="user.roleId == 1"> <router-link :to="'/users'" class="waves-effect "><i class="icon-people fa-users fa-fw"></i> <span class="hide-menu">Пользователи</span></router-link></li>
                    <li v-if="user.roleId == 1"> <router-link :to="'/events/1'" class="waves-effect "><i class="icon-note fa-users fa-fw"></i> <span class="hide-menu">События</span></router-link></li>
                    <li> <router-link :to="'/tasks/' + user.id" class="waves-effect "><i class="icon-clock fa-users fa-fw"></i> <span class="hide-menu">Задачи</span></router-link></li>
                    <li> <router-link :to="'/calls/1'" class="waves-effect "><i class="icon-phone fa-users fa-fw"></i> <span class="hide-menu">Звонки</span></router-link></li>
                </ul>
            </div>
        </div> 
    </div>
</template>
<script>
import Vue from "vue";
import { mapState } from "vuex";
import axios from "axios";
import AuthService from "~/pages/index/services/AuthService";
/* eslint-disable */
import "./../../../../plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js";
import "./../../../../common/js/jquery.slimscroll.js";
import "./../../../../common/js/waves.js";
/* eslint-enable */
export default {

  data() {
    return {
      onHold: false,
      data: {},
      errors: {}
    };
  },
  created() {},
  mounted() {
    (function($) {
      const body = $("body");
      body.trigger("resize");
      [].slice.call(document.querySelectorAll(".sttabs")).forEach(function(el) {
        new CBPFWTabs(el);
      });
    })(window.jQuery);
  },
  computed: {
    ...mapState(["user"])
  },
  methods: {
    toggleSidebar() {
      (function() {
        $("body")
          .toggleClass("show-sidebar")
          .toggleClass("hide-sidebar");
        $(".sidebar-head .open-close i").toggleClass("ti-menu");
      })(window.jQuery);
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
.navbar-header {
  border-bottom: 1px solid rgba(0, 0, 0, 0.08);
}
.top-left-part {
  text-align: center;
}

.navbar-left {
  float: none !important;
}
.navbar-top-links {
  margin: 0 auto;
  display: inline-block;
}

#side-menu > li > a.active {
  background: none;
  color: #97999f;
  font-weight: inherit;
}
#side-menu > li > a.router-link-exact-active {
  background: #2cabe3;
  color: #ffffff;
  font-weight: 500;
}
.page-title-navbar {
  font-weight: 400;
  font-size: 16px;
  text-transform: uppercase;
}
.navbar-default.navbar-static-top {
  height: 60px;
}
</style>

