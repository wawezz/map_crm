<template>
  <table class="table tablesorter">
    <thead>
      <tr>
        <th v-for="(column, i) in columns" :key="i">
          <span v-if="column.name" class="tableTitle">{{column.name.toUpperCase()}}</span>
          <i
            v-if="column.name && !column.disableSort"
            :class="`sort-by fas fa-sort${sort[column.name]?sort[column.name] === 'DESC'?'-down':'-up':''}`"
            @click="sortBy(column.name)"
          ></i>
          <span v-if="sort[column.name]">#{{Object.keys(sort).indexOf(column.name)}}</span>
        </th>
      </tr>
    </thead>
    <tbody v-for="item in data" :key="item.id">
      <tr class="advance-table-row">
        <td v-for="(column, i) in columns" :key="i">
          <base-checkbox
            v-if="column.type === 'checkbox'"
            :id="'checkbox_' + item.id"
            :value="item.id"
            v-model="itemsObject.checked"
          ></base-checkbox>
          <router-link
            v-if="column.type === 'link'"
            :to="{ path: column.prefix + item.id}"
          >{{item[column.field||column.name]}}</router-link>
          <a v-if="column.type === 'phone'" href="#">{{item[column.field||column.name]}}</a>
          <a
            v-if="column.type === 'email'"
            :href="'mail-to:' + item[column.field||column.name]"
          >{{item[column.field||column.name]}}</a>
          <span v-if="column.type === 'date'">{{item[column.field||column.name].date | date}}</span>
          <span v-if="!column.type">{{item[column.field||column.name]||'undefined'}}</span>
        </td>
      </tr>
    </tbody>
  </table>
</template>
<script>
  export default {
    name: "active-table",
    props: {
      columns: {
        type: Array,
        default: () => [],
        description: "Table columns"
      },
      data: {
        type: [Array, Object],
        default: () => [],
        description: "Table data"
      },
      sort: {
        type: [String, Object]
      },
      itemsObject: {
        type: Object
      },
      sortBy: {
        type: Function
      }
    }
  };
</script>
<style>
</style>
