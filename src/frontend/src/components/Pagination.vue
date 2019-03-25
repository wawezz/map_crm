<template>
  <div class="row">
    <div class="col-md-12 text-center" v-if="pages.length > 1">
      <nav class="mt-3">
        <router-link v-if="pCurrent > 1" :to="lPrefix + (pCurrent - 1)">
          <base-button type="primary" class="mr-3">prev</base-button>
        </router-link>
        <base-button v-if="pCurrent === 1" class="mr-3">prev</base-button>

        <span v-for="(page, index) in pages" :key="index">
          <router-link
            v-if="page != pCurrent && page !== '...'"
            class="page"
            :to="lPrefix + page"
          >{{page}}</router-link>

          <span v-if="page == pCurrent" class="page current">{{page}}</span>

          <span v-if="page === '...'" class="page dots">…</span>
        </span>

        <router-link v-if="pCurrent < maxPage" :to="lPrefix + (pCurrent + 1)">
          <base-button type="primary" class="ml-3">next</base-button>
        </router-link>
        <base-button v-if="pCurrent === maxPage" class="ml-3">next</base-button>
      </nav>
    </div>
  </div>
</template>
<script>
export default {
  name: "pagination",
  props: {
    total: {
      type: Number,
      default: 0
    },
    size: {
      type: Number,
      default: 1
    },
    current: {
      type: Number,
      default: 1
    },
    prefix: {
      type: String,
      default: "/"
    }
  },
  data() {
    return {
      pages: [],
      maxPage: 0,
      pCurrent: parseInt(this.current),
      pSize: parseInt(this.size),
      pTotal: parseInt(this.total),
      lPrefix: this.prefix
    };
  },
  created() {
    this.maxPage = Math.ceil(this.pTotal / this.pSize);

    this.getPages();
  },
  methods: {
    getPages() {
      const { pCurrent, maxPage } = this;
      const pages = [];

      if (pCurrent > 3) {
        pages.push(1);
        if (pCurrent > 4) pages.push("...");
      }

      for (let page = pCurrent - 2; page <= pCurrent + 2; page++) {
        if (page > 0 && page <= maxPage) {
          pages.push(page);
        }
      }

      if (pCurrent < maxPage - 2) {
        if (pCurrent < maxPage - 3) pages.push("...");
        pages.push(maxPage);
      }
      this.pages = pages;
    }
  }
};
</script>
<style>
.page {
  margin: 0 10px;
  font-size: 16px;
}
.current,
.dots {
  color: #fff;
}
</style>