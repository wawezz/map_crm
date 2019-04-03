<template>
  <div class="contact-us full-screen">
    <div class="wrapper wrapper-full-page section content">
      <div class="container">
        <div class="row">
          <div class="col-12 text-center marginTop">
            <h2 class="title text-danger">404 Not Found</h2>
            <h2 class="title">Oops! It seems that this page does not exist.</h2>
            <router-link :to="{path:'/'}" class="navbar-brand">Back to CRM</router-link>
          </div>
        </div>
      </div>
    </div>
    <footer class="footer-demo">
      <div class="container"></div>
    </footer>
  </div>
</template>

<script>
  export default {};
</script>

<style>
  .marginTop {
  margin-top: 150px;
}
</style>
