import axios from "axios";

export default class AuthService {
  static storage = window.localStorage;
  static tokens = null;
  static uid = null;

  static init() {
    try {
      AuthService.tokens = JSON.parse(AuthService.storage.getItem("authTokens"));
    } catch (e) {
      AuthService.tokens = null;
    }
  }

  static isGuest() {
    return AuthService.tokens === null;
  }

  static setTokens(tokens) {
    AuthService.tokens = tokens;

    AuthService.storage.setItem("authTokens", JSON.stringify(tokens));
  }

  static setUser(data) {

    AuthService.setTokens(data.tokens);
    AuthService.setUserId(data.user.id);

  }

  static setUserId(uid) {
    AuthService.uid = uid;

    AuthService.storage.setItem("uid", JSON.stringify(uid));
  }

  static removeUser() {
    AuthService.setTokens(null);
    AuthService.setUserId(null);
  }

  static refreshAccessToken() {
    if (AuthService.tokens === null || !AuthService.tokens.refresh) {
      throw new Error("Refresh token not found");
    }

    return axios.post("/v1/user/login-refresh", {
      form: { token: AuthService.tokens.refresh }
    })
      .then(response => {
        if (response.data.result && response.data.result.tokens) {
          AuthService.setTokens(response.data.result.tokens);
        }

        return response;
      }, error => {
        throw error;
      });
  }
}
