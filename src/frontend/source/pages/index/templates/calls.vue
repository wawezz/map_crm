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
                            <th>КЛИЕНТ</th>
                            <th>ТИП</th>
                            <th>СТАТУС</th>
                            <th>ЗАПИСЬ</th>
                          </tr>
                          <tr class="footable-even users-list-group"  v-if="notes.count">
                              <th colspan="5">ОБЩЕЕ ЧИСЛО: <span class="users-list-count">({{notes.count}} записей)</span></th>
                          </tr>
                      </thead>
                      <tbody v-for="note in notes.result" :key="note._id.$oid">
                          <tr class="advance-table-row">
                            <td>{{new Date(note.createdAt*1000).toISOString() | dateTime}}</td>
                            <td><router-link :to="'/user/' + note.createdBy">{{note.dataValue.by}}</router-link></td>
                            <td>
                              <router-link v-if="note.elementId != 0" :to="'/client/' + note.elementId">Клиент</router-link>
                              <span v-if="note.elementId == 0">Клиент не определен</span>
                            </td>
                            <td>{{noteTypes[note.noteType]}}</td>
                            <td>{{note.dataValue.data.hangup_cause}}</td>
                            <td>
                              <audio controls>
                                <source :src="'https://sancom.lv:533/recordings/' + note.dataValue.data.uniqueid + '.mp3'" type="audio/mpeg">
                                Your browser does not support the audio element.
                              </audio>
                            </td>
                          </tr>
                      </tbody>
                    </table>
                    <ul class="notesNav">
                        <li v-for="n in notesPages" v-if="notesPages > 1" :key="n">
                          <router-link v-if="n != notesPage" :to="'/calls/' + n">{{n}}</router-link>
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
    this.notesFilter = { noteType: { $in: [state.noteTypes.NOTE_TYPE_CALL_IN.id, state.noteTypes.NOTE_TYPE_CALL_OUT.id]}};
    if(this.user.roleId != 1) this.notesFilter.createdBy = this.user.id;
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