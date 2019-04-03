<template>
  <div class="row" v-if="user">
    <div class="col-12">
      <card title="Calls">
        <div class="table">
          <table class="table tablesorter" v-if="!listError">
            <thead>
              <tr>
                <th>DATE</th>
                <th>AUTHOR</th>
                <th>CLIENT</th>
                <th>TYPE</th>
                <th>STATUS</th>
                <th>NOTE</th>
              </tr>
              <tr class="footable-even users-list-group" v-if="notesTotalCount">
                <th colspan="5">
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
                  <router-link v-if="note.elementId != 0" :to="'/client/' + note.elementId">Client</router-link>
                  <span v-if="note.elementId == 0">Client not defined</span>
                </td>
                <td>{{noteTypes[note.noteType]}}</td>
                <td>{{note.dataValue.data.hangup_cause}}</td>
                <td>
                  <audio controls>
                    <source
                      :src="'https://sancom.lv:533/recordings/' + note.dataValue.data.uniqueid + '.mp3'"
                      type="audio/mpeg"
                    >Your browser does not support the audio element.
                  </audio>
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
            :prefix="'/calls/'"
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
    watch: {
      $route(to, from) {
        this.getNotes();
      }
    },
    created() {
      const { state } = this.$store;
      this.notesFilter = {
        noteType: {
          $in: [
            state.noteTypes.NOTE_TYPE_CALL_IN.id,
            state.noteTypes.NOTE_TYPE_CALL_OUT.id
          ]
        }
      };
      if (this.user.roleId != 1) this.notesFilter.createdBy = this.user.id;
      this.getNotes();
    },
    mixins: [main, notes]
  };
</script>
<style>
</style>
