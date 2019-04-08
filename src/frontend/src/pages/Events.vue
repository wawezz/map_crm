<template>
  <div class="row" v-if="user">
    <div class="col-12">
      <card title="Events" :loading="notesLoading">
        <div class="table">
          <table class="table tablesorter" v-if="!listError">
            <thead>
              <tr>
                <th>DATE</th>
                <th>AUTHOR</th>
                <th>OBJECT</th>
                <th>NAME</th>
                <th>EVENT</th>
                <th>VALUE BEFORE</th>
                <th>VALUE AFTER</th>
              </tr>
              <tr class="footable-even users-list-group" v-if="notesTotalCount">
                <th colspan="7">
                  TOTAL COUNT:
                  <span class="users-list-count">({{notesTotalCount}} notes)</span>
                </th>
              </tr>
            </thead>
            <tbody v-for="note in notes" :key="note._id.$oid">
              <tr class="advance-table-row">
                <td>{{new Date(note.createdAt*1000).toISOString() | dateTime}}</td>
                <td>
                  <router-link :to="'/profile/' + note.createdBy">{{note.dataValue.by}}</router-link>
                </td>
                <td>
                  <router-link
                    v-if="(note.elementType == $store.state.elementTypes.ELEMENT_TYPE_CLIENT.id)"
                    :to="'/client/' + note.elementId"
                  >{{$store.state.elementTypes.ELEMENT_TYPE_CLIENT.title}}</router-link>
                  <router-link
                    v-if="(note.elementType == $store.state.elementTypes.ELEMENT_TYPE_LEAD.id)"
                    :to="'/lead/' + note.elementId"
                  >{{$store.state.elementTypes.ELEMENT_TYPE_LEAD.title}}</router-link>
                  <router-link
                    v-if="(note.elementType == $store.state.elementTypes.ELEMENT_TYPE_PLACE_LEAD.id)"
                    :to="'/place-lead/' + note.elementId"
                  >{{$store.state.elementTypes.ELEMENT_TYPE_PLACE_LEAD.title}}</router-link>
                </td>
                <td>{{note.dataValue.name?note.dataValue.name:'no name'}}</td>
                <td style="max-width: 270px; overflow: hidden;">
                  {{noteTypes[note.noteType]}}
                  <span
                    v-if="note.noteType == $store.state.noteTypes.NOTE_TYPE_LEAD_FIELD_UPDATE.id || note.noteType == $store.state.noteTypes.NOTE_TYPE_PLACE_LEAD_FIELD_UPDATE.id || note.noteType == $store.state.noteTypes.NOTE_TYPE_CLIENT_FIELD_UPDATE.id"
                  >: {{note.dataValue.data.field}}</span>
                </td>
                <td
                  colspan="2"
                  style="text-align: center;"
                  v-if="note.noteType != $store.state.noteTypes.NOTE_TYPE_LEAD_FIELD_UPDATE.id &&
                              note.noteType != $store.state.noteTypes.NOTE_TYPE_PLACE_LEAD_FIELD_UPDATE.id &&
                              note.noteType != $store.state.noteTypes.NOTE_TYPE_PLACE_LEAD_STATUS_CHANGED.id &&
                              note.noteType != $store.state.noteTypes.NOTE_TYPE_LEAD_STATUS_CHANGED.id &&
                              note.noteType != $store.state.noteTypes.NOTE_TYPE_CLIENT_FIELD_UPDATE.id"
                >
                  <span
                    v-if="note.noteType == $store.state.noteTypes.NOTE_TYPE_COMMON.id"
                  >{{note.dataValue.text||'worker ID: ' + note.dataValue.workerId}}</span>
                </td>
                <td
                  style="max-width: 100px; overflow: hidden;"
                  v-if="note.noteType == $store.state.noteTypes.NOTE_TYPE_LEAD_FIELD_UPDATE.id ||
                              note.noteType == $store.state.noteTypes.NOTE_TYPE_PLACE_LEAD_FIELD_UPDATE.id ||
                              note.noteType == $store.state.noteTypes.NOTE_TYPE_PLACE_LEAD_STATUS_CHANGED.id ||
                              note.noteType == $store.state.noteTypes.NOTE_TYPE_LEAD_STATUS_CHANGED.id ||
                              note.noteType == $store.state.noteTypes.NOTE_TYPE_CLIENT_FIELD_UPDATE.id"
                >
                  <router-link
                    v-if="(note.dataValue.data.field == 'responsible')"
                    :to="'/profile/' + note.dataValue.data.values.from"
                  >from</router-link>
                  <router-link
                    v-if="(note.dataValue.data.field == 'client')"
                    :to="'/client/' + note.dataValue.data.values.from"
                  >from</router-link>
                  <span
                    v-if="(note.dataValue.data.field != 'responsible' &&
                                note.dataValue.data.field != 'client')"
                  >
                    <span
                      v-if="note.dataValue.data.field == 'status'"
                    >{{statusesById[note.dataValue.data.values.from].name}}</span>
                    <span
                      v-if="note.dataValue.data.field == 'countryId'"
                    >{{countriesById[note.dataValue.data.values.from].name}}</span>
                    <span
                      v-if="note.dataValue.data.field == 'currency'"
                    >{{currenciesById[note.dataValue.data.values.from].name}}</span>
                    <span
                      v-if="note.dataValue.data.field != 'status' && note.dataValue.data.field != 'countryId' && note.dataValue.data.field != 'currency'"
                    >{{note.dataValue.data.values.from?note.dataValue.data.values.from:'null'}}</span>
                  </span>
                </td>
                <td
                  style="max-width: 100px; overflow: hidden;"
                  v-if="note.noteType == $store.state.noteTypes.NOTE_TYPE_LEAD_FIELD_UPDATE.id ||
                              note.noteType == $store.state.noteTypes.NOTE_TYPE_PLACE_LEAD_FIELD_UPDATE.id ||
                              note.noteType == $store.state.noteTypes.NOTE_TYPE_PLACE_LEAD_STATUS_CHANGED.id ||
                              note.noteType == $store.state.noteTypes.NOTE_TYPE_LEAD_STATUS_CHANGED.id ||
                              note.noteType == $store.state.noteTypes.NOTE_TYPE_CLIENT_FIELD_UPDATE.id"
                >
                  <router-link
                    v-if="(note.dataValue.data.field == 'responsible')"
                    :to="'/profile/' + note.dataValue.data.values.to"
                  >to</router-link>
                  <router-link
                    v-if="(note.dataValue.data.field == 'client')"
                    :to="'/client/' + note.dataValue.data.values.to"
                  >to</router-link>
                  <span
                    v-if="(note.dataValue.data.field != 'responsible' &&
                                note.dataValue.data.field != 'client')"
                  >
                    <span
                      v-if="note.dataValue.data.field == 'status'"
                    >{{statusesById[note.dataValue.data.values.to].name}}</span>
                    <span
                      v-if="note.dataValue.data.field == 'countryId'"
                    >{{countriesById[note.dataValue.data.values.to].name}}</span>
                    <span
                      v-if="note.dataValue.data.field == 'currency'"
                    >{{currenciesById[note.dataValue.data.values.to].name}}</span>
                    <span
                      v-if="note.dataValue.data.field != 'status' && note.dataValue.data.field != 'countryId' && note.dataValue.data.field != 'currency'"
                    >{{note.dataValue.data.values.to?note.dataValue.data.values.to:'null'}}</span>
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="col-12">
          <pagination
            v-if="notesTotalCount"
            :total="notesTotalCount"
            :current="notesPage"
            :size="notesLimit"
            :prefix="'/events/'"
          />
        </div>
      </card>
    </div>
  </div>
</template>
<script>
  import { Pagination } from "@/components";
  import { main } from "./../mixins/main";
  import { notes } from "./../mixins/notes";
  import authGuard from "./../guards/auth.guard";

  export default {
    beforeRouteEnter: authGuard,
    components: {
      Pagination
    },
    created() {
      const { state } = this.$store;
      this.getParams(["statuses", "countries", "currencies"]);
      this.notesFilter = {
        noteType: {
          $nin: [
            state.noteTypes.NOTE_TYPE_CALL_IN.id,
            state.noteTypes.NOTE_TYPE_CALL_OUT.id
          ]
        }
      };
      this.getNotes();
    },
    mixins: [main, notes]
  };
</script>
<style>
</style>
