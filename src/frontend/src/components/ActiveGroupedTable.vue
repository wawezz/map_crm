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
    <tbody v-for="group in data" :key="group.id">
      <tr>
        <th :colspan="columns.length">
          GROUP: {{group.name}}
          <span
            v-if="group[items].length"
            class="users-list-count"
          >({{group[items].length +' '+ items}})</span>
        </th>
      </tr>
      <tr
        v-for="groupItem in group[items]"
        :key="groupItem.id"
        :class="'advance-table-row' + groupItem.isOnline>1?' online':groupItem.isOnline==1?' hold':''"
      >
        <td v-for="(column, i) in columns" :key="i">
          <base-checkbox
            v-if="column.type === 'checkbox' && groupItem.id != user.id"
            :id="'checkbox_' + groupItem.id"
            :value="groupItem.id"
            v-model="itemsObject.checked"
          ></base-checkbox>
          <router-link
            v-if="column.type === 'link'"
            :to="{ path: column.prefix + groupItem.id}"
          >{{groupItem[column.field||column.name]}}</router-link>
          <a v-if="column.type === 'phone'" href="#">{{groupItem[column.field||column.name]}}</a>
          <a
            v-if="column.type === 'email'"
            :href="'mail-to:' + groupItem[column.field||column.name]"
          >{{groupItem[column.field||column.name]}}</a>
          <span v-if="column.type === 'date'">{{groupItem[column.field||column.name].date | date}}</span>
          <span v-if="!column.type">{{groupItem[column.field||column.name]||'undefined'}}</span>
          <img
            v-if="column.type === 'image'"
            :src="groupItem[column.id]>0?groupItem[column.path] + groupItem[column.field||column.name]:require('./../assets/images/avatar.png')"
            alt="user img"
            class="img-circle"
            width="30"
          >
        </td>
      </tr>
    </tbody>
  </table>
</template>
<script>
  export default {
    name: "active-grouped-table",
    props: {
      user: {
        type: [Object]
      },
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
      },
      items: {
        type: String,
        default: ""
      }
    }
  };
</script>
<style>
</style>
