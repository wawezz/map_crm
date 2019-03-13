import AuthService from "./services/AuthService";

module.exports = function (axios) {
  axios.interceptors.response.use(function (response) {
    return response;
  }, function (error) {
    const {status} = error.response;

    if (status === 401) {
      AuthService.refreshAccessToken()
        .then(() => window.location.reload());
    }

    return Promise.reject(error);
  });
};
