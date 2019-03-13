import moment from "moment";

module.exports = function (Vue) {
  Vue.filter("date", function (value) {
    if (value) {
      return moment(String(value)).format("YYYY-MM-DD")
    }
  });
};
