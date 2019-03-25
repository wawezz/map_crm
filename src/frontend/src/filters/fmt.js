export default function (Vue) {
  Vue.filter("fmt", function (value) {
    if (typeof value !== "undefined") {
      let data = value.toString().split(".")[0].replace(/\B(?=(\d{3})+(?!\d))/g, " ");
      if (value % 1 !== 0) {
        data = data + "." + value.toString().split(".")[1];
      }
      return data;
    }
  });
};
