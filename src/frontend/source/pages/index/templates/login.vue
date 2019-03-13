<template>
  <div>
    <section id="wrapper" class="new-login-register">
      <div class="new-login-box">
        <div class="white-box">
          <h3 class="box-title m-b-0 text-center">Map crm</h3>
          <form class="form-horizontal new-lg-form" id="loginform" @submit.prevent="submitForm">
            <div class="form-group m-t-20">
              <div class="col-xs-12">
                <label>Email</label>
                <input
                  class="form-control"
                  ref="email"
                  type="text"
                  required
                  v-model="data.email"
                  placeholder="Email"
                >
                <div v-if="errors.email" class="alert alert-danger">{{errors.email}}</div>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12">
                <label>Password</label>
                <input
                  class="form-control"
                  type="password"
                  required
                  v-model="data.password"
                  placeholder="Password"
                >
                <div v-if="errors.password" class="alert alert-danger">{{errors.password}}</div>
              </div>
            </div>
            <div class="form-group text-center m-t-20">
              <div class="col-xs-12">
                <button
                  class="btn btn-info btn-lg btn-block btn-rounded text-uppercase waves-effect waves-light"
                  type="submit"
                >Sign in</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import Vue from "vue";
import env from "~/env";
import { main } from "./mixins/main";
import AuthService from "~/pages/index/services/AuthService";

export default {
  data() {
    return {
      data: {
        email: null,
        password: null
      },
      errors: {
        email: null,
        password: null
      }
    };
  },
  created() {},
  mounted() {
    this.$refs.email.focus();
  },
  computed: {
    hasError() {
      return this.errors.email !== null || this.errors.password !== null;
    }
  },
  methods: {
    submitForm() {
      if (this.inProgress === true) return;

      this.errors.email = null;
      this.errors.password = null;

      if (!this.isValidEmail(this.data.email)) {
        this.errors.email = "Incorrect email.";
        return false;
      }

      if (!this.isValidPassword(this.data.password)) {
        this.errors.password = "Password is required.";
        return false;
      }

      if (this.hasError) {
        return false;
      }

      this.inProgress = true;
      this.$http
        .post(
          "/v1/user/login",
          { form: this.data },
          { params: { s: env.env === "dev" ? 0 : null } }
        )
        .then(
          ({ data }) => {
            this.inProgress = false;

            AuthService.setUser(data.result);
            this.$store.commit("SET_USER", data.result.user);
            this.$store.commit("CLEAR_AUTH_FORM_DATA");

            this.$router.push({ path: "/" });
          },
          ({ response }) => {
            this.inProgress = false;

            const { data } = response;

            if (data.error) {
              if (data.error.code === 418 && data.error.errors)
                this.errors = Object.assign(this.errors, data.error.errors);
            } else {
              console.error("Unexpected error", data.error);
            }
          }
        );
    }
  },
  mixins: [main]
};
</script>

<style>
.new-login-register .new-login-box {
  margin-left: calc(50% - 200px) !important;
}
</style>