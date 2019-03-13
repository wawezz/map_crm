<template>
  <div v-if="user">
        <div id="page-wrapper">
            <div class="container-fluid p-t-20">

                <div class="white-box">
                    <table id="demo-foo-addrow" class="table m-t-15 table-hover contact-list footable-loaded users-list footable" data-page-size="10" v-if="!listError">
                      <thead>
                          <tr>
                            <th>ДАТА</th>
                            <th>АВТОР</th>
                            <th>ОБЪЕКТ</th>
                            <th>НАЗВАНИЕ</th>
                            <th>СОБЫТИЕ</th>
                            <th>ЗНАЧЕНИЕ ДО</th>
                            <th>ЗНАЧЕНИЕ ПОСЛЕ</th>
                          </tr>
                          <tr class="footable-even users-list-group"  v-if="notes.count">
                              <th colspan="7">ОБЩЕЕ ЧИСЛО: <span class="users-list-count">({{notes.count}} записей)</span></th>
                          </tr>
                      </thead>
                      <tbody v-for="note in notes.result" :key="note._id.$oid">
                          <tr class="advance-table-row">
                            <td>{{new Date(note.createdAt*1000).toISOString() | dateTime}}</td>
                            <td><router-link :to="'/user/' + note.createdBy">{{note.dataValue.by}}</router-link></td>
                            <td>
                              <router-link v-if="(note.elementType == $store.state.elementTypes.ELEMENT_TYPE_CLIENT.id)" :to="'/client/' + note.elementId">{{$store.state.elementTypes.ELEMENT_TYPE_CLIENT.title}}</router-link>
                              <router-link v-if="(note.elementType == $store.state.elementTypes.ELEMENT_TYPE_LEAD.id)" :to="'/lead/' + note.elementId">{{$store.state.elementTypes.ELEMENT_TYPE_LEAD.title}}</router-link>
                            </td>
                            <td>{{note.dataValue.name?note.dataValue.name:'no name'}}</td>
                            <td style="max-width: 270px; overflow: hidden;">
                              {{noteTypes[note.noteType]}} 
                              <span v-if="note.noteType == $store.state.noteTypes.NOTE_TYPE_LEAD_FIELD_UPDATE.id || note.noteType == $store.state.noteTypes.NOTE_TYPE_CLIENT_FIELD_UPDATE.id">
                                : {{note.dataValue.data.field}}
                              </span>
                            </td>
                            <td colspan="2" style="text-align: center;" v-if="note.noteType != $store.state.noteTypes.NOTE_TYPE_LEAD_FIELD_UPDATE.id && 
                              note.noteType != $store.state.noteTypes.NOTE_TYPE_LEAD_STATUS_CHANGED.id &&
                              note.noteType != $store.state.noteTypes.NOTE_TYPE_CLIENT_FIELD_UPDATE.id">
                              <span v-if="note.noteType == $store.state.noteTypes.NOTE_TYPE_COMMON.id">{{note.dataValue.text}}</span>
                            </td>
                            <td 
                              style="max-width: 100px; overflow: hidden;" 
                              v-if="note.noteType == $store.state.noteTypes.NOTE_TYPE_LEAD_FIELD_UPDATE.id || 
                              note.noteType == $store.state.noteTypes.NOTE_TYPE_LEAD_STATUS_CHANGED.id || 
                              note.noteType == $store.state.noteTypes.NOTE_TYPE_CLIENT_FIELD_UPDATE.id">
                              <router-link v-if="(note.dataValue.data.field == 'responsible')" :to="'/user/' + note.dataValue.data.values.from">from</router-link>
                              <router-link v-if="(note.dataValue.data.field == 'client')" :to="'/client/' + note.dataValue.data.values.from">from</router-link>
                              <span v-if="(note.dataValue.data.field != 'responsible' && 
                                note.dataValue.data.field != 'client')">
                                <span v-if="note.dataValue.data.field == 'status'">{{statusesById[note.dataValue.data.values.from].name}}</span>
                                <span v-if="note.dataValue.data.field == 'countryId'">{{countriesById[note.dataValue.data.values.from].name}}</span>
                                <span v-if="note.dataValue.data.field == 'currency'">{{currenciesById[note.dataValue.data.values.from].name}}</span>
                                <span v-if="note.dataValue.data.field != 'status' && note.dataValue.data.field != 'countryId' && note.dataValue.data.field != 'currency'">{{note.dataValue.data.values.from?note.dataValue.data.values.from:'null'}}</span>
                              </span>
                            </td>
                            <td style="max-width: 100px; overflow: hidden;" 
                              v-if="note.noteType == $store.state.noteTypes.NOTE_TYPE_LEAD_FIELD_UPDATE.id || 
                              note.noteType == $store.state.noteTypes.NOTE_TYPE_LEAD_STATUS_CHANGED.id || 
                              note.noteType == $store.state.noteTypes.NOTE_TYPE_CLIENT_FIELD_UPDATE.id">
                              <router-link v-if="(note.dataValue.data.field == 'responsible')" :to="'/user/' + note.dataValue.data.values.to">to</router-link>
                              <router-link v-if="(note.dataValue.data.field == 'client')" :to="'/client/' + note.dataValue.data.values.to">to</router-link>
                              <span v-if="(note.dataValue.data.field != 'responsible' && 
                                note.dataValue.data.field != 'client')">
                                <span v-if="note.dataValue.data.field == 'status'">{{statusesById[note.dataValue.data.values.to].name}}</span>
                                <span v-if="note.dataValue.data.field == 'countryId'">{{countriesById[note.dataValue.data.values.to].name}}</span>
                                <span v-if="note.dataValue.data.field == 'currency'">{{currenciesById[note.dataValue.data.values.to].name}}</span>
                                <span v-if="note.dataValue.data.field != 'status' && note.dataValue.data.field != 'countryId' && note.dataValue.data.field != 'currency'">{{note.dataValue.data.values.to?note.dataValue.data.values.to:'null'}}</span>
                              </span>
                            </td>
                          </tr>
                      </tbody>
                    </table>
                    <ul class="notesNav">
                        <li v-for="n in notesPages" v-if="notesPages > 1" :key="n">
                          <router-link v-if="n != notesPage" :to="'/events/' + n">{{n}}</router-link>
                          <span v-if="n == notesPage">{{n}}</span>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
  </div>
</template>

<script> 
import Vue from "vue";
import { main } from "./mixins/main";
import { notes } from "./mixins/notes";
import authGuard from "./../components/guards/auth.guard";

export default {
  beforeRouteEnter: authGuard,
  data() {
    return {
      data: {}
    };
  },
  watch: {
    $route(to, from) {
      this.getNotes();
    }
  },
  created() {
    const { state } = this.$store;
    this.getParams(["statuses", "countries", "currencies"]);
    this.notesFilter = { noteType: { $nin: [state.noteTypes.NOTE_TYPE_CALL_IN.id, state.noteTypes.NOTE_TYPE_CALL_OUT.id]}};
    this.getNotes();
  },
  mounted() {},
  computed: {
  },
  methods: {
  },
  mixins: [main, notes],  
  components: {
  }
};
</script>

<style>
.notesNav li{
  list-style: none;
  display: inline-flex;
  width: 20px;
  text-align: center;
}
</style>