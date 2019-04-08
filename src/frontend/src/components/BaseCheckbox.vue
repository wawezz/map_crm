<template>
  <div class="form-check" :class="[{disabled: disabled}, inlineClass]">
    <label :for="cbId" class="form-check-label">
      <div v-if="label">{{label}}</div>
      <input
        :id="cbId"
        class="form-check-input"
        type="checkbox"
        :disabled="disabled"
        :value="value"
        v-model="model"
      >
      <span class="form-check-sign"></span>
      <slot>
        <span v-if="inline">&nbsp;</span>
      </slot>
    </label>
  </div>
</template>
<script>
  export default {
    name: "base-checkbox",
    model: {
      prop: "checked"
    },
    props: {
      checked: {
        type: [Array, Boolean, Number, String]
      },
      disabled: {
        type: Boolean
      },
      inline: {
        type: Boolean
      },
      value: {
        type: [String, Number, Boolean]
      },
      label: {
        type: String
      }
    },
    data() {
      return {
        cbId: "",
        touched: false
      };
    },
    computed: {
      model: {
        get() {
          return this.checked;
        },
        set(check) {
          if (!this.touched) {
            this.touched = true;
          }
          this.$emit("input", check);
        }
      },
      inlineClass() {
        if (this.inline) {
          return `form-check-inline`;
        }
      }
    },
    created() {
      this.cbId = Math.random()
        .toString(16)
        .slice(2);
    }
  };
</script>
