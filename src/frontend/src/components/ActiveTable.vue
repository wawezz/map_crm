<template>
  <table class="table tablesorter">
    <thead>
      <tr>
        <th v-for="(column, i) in columns" :key="i">
          <span v-if="column.name" class="tableTitle">{{column.name.toUpperCase()}}</span>
          <i
            v-if="(column.sortValue || column.name) && !column.disableSort"
            :class="`sort-by fas fa-sort${sort[column.sortValue || column.name]?sort[column.sortValue || column.name] === 'DESC'?'-down':'-up':''}`"
            @click="sortBy(column.sortValue || column.name)"
          ></i>
          <span
            v-if="sort[column.sortValue || column.name]"
          >#{{Object.keys(sort).indexOf(column.sortValue || column.name)}}</span>
        </th>
      </tr>
    </thead>
    <tbody v-for="(item, n) in data" :key="item.id">
      <tr class="advance-table-row">
        <td v-for="(column, i) in columns" :key="i">
          <div
            v-if="column.updateble && update && itemsObject.checked.indexOf(item.id.toString()) != -1 && updatebleStatuses.indexOf(item.status) != -1"
          >
            <span v-if="column.type === 'select'">
              <select
                class="form-control form-control-line"
                v-model="updatebleData[n][column.name]"
              >
                <option
                  v-for="data in updatebleList[column.name]"
                  :key="data.id"
                  :value="data.id"
                >{{data.name}}</option>
              </select>
            </span>
            <span v-if="column.type === 'input'">
              <base-input v-model="updatebleData[n][column.field||column.name]"></base-input>
            </span>
          </div>
          <div
            v-if="(column.updateble && !update) || !column.updateble || (column.updateble && update && itemsObject.checked.indexOf(item.id.toString()) == -1) || (column.updateble && update && itemsObject.checked.indexOf(item.id.toString()) != -1 && updatebleStatuses.indexOf(item.status) == -1)"
          >
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
            <span
              v-if="column.type === 'select' || column.type === 'input' || !column.type"
            >{{item[column.field||column.name]||'null'}}</span>
          </div>
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
        type: Object,
        default: () => {}
      },
      updatebleData: {
        type: [Array, Object],
        default: () => []
      },
      sortBy: {
        type: Function
      },
      update: {
        type: Boolean,
        default: false
      },
      updatebleList: {
        type: Object,
        default: () => {}
      },
      updatebleStatuses: {
        type: Array,
        default: () => []
      }
    }
  };
</script>
<style>
</style>
