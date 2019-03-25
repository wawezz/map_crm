<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-4 mt-md-5 ml-md-auto mr-md-auto">
        <h2 class="m-b-0 text-center">Map crm</h2>
        <form @submit.prevent="submitForm">
          <div class="form-group m-t-20">
            <div class="col-xs-12">
              <base-input
                label="Email"
                ref="email"
                type="text"
                required
                v-model="data.email"
                placeholder="Email"
              ></base-input>
              <base-alert v-if="errors.email" type="warning">{{errors.email}}</base-alert>
            </div>
          </div>
          <div class="form-group">
            <div class="col-xs-12">
              <base-input
                label="Password"
                required
                v-model="data.password"
                placeholder="Password"
                type="password"
              ></base-input>
              <base-alert v-if="errors.password" type="warning">{{errors.password}}</base-alert>
            </div>
          </div>
          <div class="form-group text-center m-t-20">
            <div class="col-xs-12">
              <button class="btn btn-primary text-uppercase" type="submit">Sign in</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
<script>
import env from "./../../source/env";
import { main } from "./../mixins/main";
import AuthService from "./../services/AuthService";
import axios from "axios";
import { BaseAlert } from "@/components";

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
    this.$refs.email.$el.focus();
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
      axios
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
  mixins: [main],
  components: {
    BaseAlert
  }
};
</script>
<style>
</style>
